<?php

if ( ! defined( 'ABSPATH' ) ) exit;

if ( is_admin() ) return;

/**
 * Class for discount
 *
 * The class produces a simple discount calculation
 * Entering data on the discount in the cart produced in the class WPSL_Order
 */
class WPSL_Discount {
	
	/**
	 * Корзина
	 * @var WPSL_Cart
	 */
	private $cart;

	public $discount;

	public $errors;
	 
	/**
	 * Экземпляр класса
	 * @static
	 */
	private static $instance = NULL;

	/**
	 * Возвращает экземпляр класса
	 * @static
	 * @return WPSL_Discount
	 */
	public static function getInstance( $coupon_title = '' ) {
		if ( !self::$instance )
			self::$instance = new WPSL_Discount( $coupon_title );
		return self::$instance;
	}
	
	/**
	 * Constructor
	 */
	private function __construct( $coupon_title ) {
		if ( $coupon_title == '' ) {
			$coupon_title = get_the_title( $this->get_user_discounts() );
		}
		$this->cart = WPSL_Cart::create();
		$this->discount['coupon_title'] = $coupon_title;
		$this->discount['coupon_id'] = $this->get_coupon_id( $coupon_title );
		$fields = array(
			'discount_type',
			'discount_apply',
			'coupon_amount',
			'date_expires',
			'send_coupon',
			'minimum_amount',
			'maximum_amount',
			'individual_use',
			'exclude_sale_items',
			'users_limits',
			'usage_limit',
			'usage_limit_per_user'
		);
		foreach ( $fields as $field ) {
			$this->discount[$field] = get_post_meta( $this->discount['coupon_id'], $field, true );
		}
		$this->errors = $this->get_errors();
		if ( $this->discount['send_coupon'] == 'first_order' ) {
			add_action( 'wpsl_order_checkout _successful', array( $this, 'send_coupon_after_first_order' ) );
		}
    }
	
	/**
	 * Get all coupons
	 */
	function get_coupons( $args ) {
		$cache_key = 'wpsl_coupons_' . $args['post_status'];
		if ( $cache = wp_cache_get( $cache_key ) ) {
			$coupons = $cache;
		} else {
			$coupons = get_posts(
				array_merge(
					array(
						'post_type'   => array( 'shop_coupon' ),
						'numberposts' => -1,
						'post_status' => 'publish',
					),
					$args
				)
			);
			wp_cache_set( $cache_key, $coupons );
		}
		return $coupons;
	}
	
	/**
	 * Get the ID of all discount coupons of the current user
	 */
	function get_user_discounts() {
		$coupons = $this->get_coupons( $args = array( 'post_status' => 'publish' ) );
		if ( $coupons ) {
			$coupons_id = '';
			if ( is_user_logged_in() ) {
				foreach ( $coupons as $coupon ) {
					if ( get_post_meta( $coupon->ID, 'discount_apply', true ) == 'automatically' && get_post_meta( $coupon->ID, 'discount_apply', true ) == 'on' ) {
						$coupons_id[] = $coupon->ID;
					}
				}
			} elseif( !is_user_logged_in() && $this->cart->userEmail != '' ) {
				
			}
			return $coupons_id;
		}
	}
	
	/**
	 * Get coupon id
	 */
	function get_coupon_id( $coupon_title ) {
		$coupon = get_page_by_title( $coupon_title, OBJECT, 'shop_coupon' );
		if ( $coupon ) {
			return $coupon->ID;
		}
	}
	
	/**
	 * Get coupons title
	 */
	function get_coupons_titles( $args ) {
		$titles = array();
		$coupons = $this->get_coupons( $args );
		if ( $coupons ) {
			$titles = array();
			foreach( $coupons as $coupon ) {
				$titles[] = $coupon->post_title;
			}
		}
		return $titles;
	}
	
	/**
	 * Return discount title
	 */
	public function get_total() {
		$total = 0;
		if ( $this->discount['exclude_sale_items'] == 'on' ) {
			if ( $products = $this->cart->items ) {
				foreach( $products as $product_id => $product ) {
					if ( get_post_meta( $product_id, '_sale_price', true ) == '' ) {
						$total += (int)$product[$this->cart->QUO] * (int)$product[$this->cart->PRICE];
					}
				}
				return $total;
			}
		} else {
			return $this->cart->getTotal();
		}
	}

	/**
	 * Get amount of discount by all cart
	 */
	public function get_discount_by_cart() {
		$discount_value = 0;
		if ( is_numeric( $this->discount['coupon_amount'] ) ) {
			$discount_value = (int)$this->discount['coupon_amount'];
		} else {
			$amounts = explode( ',', $this->discount['coupon_amount'] );
			array_walk( $amounts, function( $amount ) use ( &$array ) {
				$list = explode( ':', $amount );
				return $array[trim( $list[0] )] = trim( $list[1] );
			} );
			ksort( $array );
			
			foreach ( $array as $value => $discount ) {
				// Get last amount
				if ( $this->get_total() >= ( float )$value ) {
					$discount_value = ( float )$discount;
				}
			}
		}
		return $discount_value;
    }
	
	/**
	 * Get amount of discount per quantity of product
	 * The discount amount is calculated for each product in the shopping cart separately
	 */
	public function get_discount_by_quantity() {
		
		$amounts = explode( ',', $this->discount['coupon_amount'] );
		array_walk( $amounts, function( $amount ) use ( &$array ) {
			$list = explode( ':', $amount );
			return $array[trim( $list[0] )] = trim( $list[1] );
		} );
		ksort( $array );
		
		$discount_value = $product_discount_value = 0;
 		if ( $this->cart->items && is_array( $array ) ) {
			foreach( $this->cart->items as $product_id => $product ) {
				foreach ( $array as $value => $discount ) {
					// Get last amount
					if ( (int)$product[$this->cart->QUO] >= (int)$value ) {
						$product_discount_value = 0;
						if ( $this->discount['discount_type'] == 'percent_quantity' ) {
							$product_discount_value += ( $product[$this->cart->QUO] * $product[$this->cart->PRICE] ) / 100 * $discount;
						}
						if ( $this->discount['discount_type'] == 'fixed_quantity' ) {
							$product_discount_value += $discount;
						}
					} else {
						$product_discount_value += 0;
					}
				}
				$discount_value += $product_discount_value;
			}
		}
		return $discount_value;
	}
	
	/**
	 * Check limits per user
	 *
	 * @since 2.7.0
	 * @param string|int $user_data Passes the user's email or ID to the method
	 */
	public function get_user_id() {
		$user_id = '';
		if ( is_user_logged_in() ) {
			$user_id = get_current_user_id();
		} else {
			$user = get_user_by( 'email', $this->cart->userEmail );
			$user_id = $user->ID;
		}
		return $user_id;
	}
	
	/**
	 * Check errors
	 *
	 * @since 2.7.0
	 */
	public function check_errors() {
		$error = array();
		
		if ( empty( $this->discount['coupon_id'] ) || get_post_meta( $this->discount['coupon_id'], 'discount_apply', true ) == 'automatically' ) return;
		
		/**
		 * Check the discount title
		 */
		$titles = $this->get_coupons_titles( $args = array( 'post_status' => 'publish' ) );
		if ( $titles && !in_array( $this->discount['coupon_title'], $titles ) ) {
			$error[] = sprintf( __( 'Coupon "%s" does not exist!', 'wpsl' ), $this->discount['coupon_title'] );
		}
		
		/**
		 * Check scheduled discount coupon
		 * If the date of activation of the coupon has not yet come, display the message
		 */
		$titles = $this->get_coupons_titles( $args = array( 'post_status' => 'future' ) );
		if ( $titles && in_array( $this->discount['coupon_title'], $titles ) ) {
			$error[] = sprintf( __( 'The coupon is not active yet. Until the early remained: %s', 'wpsl' ), human_time_diff( get_post_time( 'U', true, $this->discount['coupon_id'] ) ) );
		}
		
		/**
		 * Checks if the discount period is over
		 */
		if ( $this->discount['date_expires'] != '' && current_time( 'timestamp' ) > strtotime( $this->discount['date_expires'] ) ) {
			$error[] = __( 'This coupon has expired', 'wpsl' );
		}
		
		/**
		 * Check the minimum spend amount.
		 */
		if ( $this->discount['minimum_amount'] != '' && $this->get_total() <= $this->discount['minimum_amount'] ) {
			$error[] = sprintf( __( 'The minimum amount to apply the discount is %s', 'wpsl' ), wpsl_price( $this->discount['minimum_amount'] ) );
		}
		
		/**
		 * Check the maximum spend amount.
		 */
		if ( $this->discount['maximum_amount'] != '' && $this->get_total() >= $this->discount['maximum_amount'] ) {
			$error[] = sprintf( __( 'The maximum amount to apply the discount is %s', 'wpsl' ), wpsl_price( $this->discount['maximum_amount'] ) );
		}
		
		/**
		 * Check usage limit
		 */
		if ( $this->discount['usage_limit'] != '' && $this->discount['usage_limit'] == '0' ) {
			$error[] = __( 'Coupon usage limit has been reached', 'wpsl' );
		}
		
		/**
		 * Check limits per user
		 */
		if ( $this->get_user_id() ) {
			$user_discount = get_user_meta( $this->get_user_id(), $this->discount['coupon_title'], true );
			if ( $this->discount['usage_limit_per_user'] != '' && $this->discount['usage_limit_per_user'] <= $user_discount ) {
				$error[] = __( 'Coupon code already applied!', 'wpsl' );
			}
		} else {
			$error[] = __( 'Sorry, this coupon is not applicable to your cart contents', 'wpsl' );
		}
		
		return $error;
	}
	
	/**
	 * Get discount
	 */
	public function get_errors() {
		$errors = $this->check_errors();
		if ( !empty( $errors ) && count( $errors ) > 0 ) {
			return $errors;
		}
	}

	/**
	 * Get discount
	 */
	public function get_discount() {
		if ( empty( $this->check_errors() ) || count( $this->check_errors() ) == '0' ) {
			switch( $this->discount['discount_type'] ) {
				case 'percent':
					$discount = ( $this->get_total() / 100 ) * $this->get_discount_by_cart();
					break;
				case 'fixed_cart':
					$discount = $this->get_discount_by_cart();
					break;
				case 'fixed_product':
					$discount = $this->get_discount_by_cart();
					break;
				case 'percent_quantity':
					$discount = $this->get_discount_by_quantity();
					break;
				case 'fixed_quantity':
					$discount = $this->get_discount_by_quantity();
					break;
				default:
					$discount = '';
					break;
			}
			return (float) $discount;
		}
    }
	
	/**
	 * Send coupon
	 */
	public function send_coupon_after_first_order() {
		
		$orders = get_posts(
			array(
				'post_type'   => array( 'shop_order' ),
				'numberposts' => -1,
				'post_status' => 'publish',
				'author'      => $this->get_user_id()
			)
		);
		
		if ( count( $orders ) == 0 && $this->discount['discount_apply'] != 'automatically' ) {
			$subject = apply_filters( 'wpsl_send_coupon_title',
				__( 'Discount for next order', 'wpsl' )
			);
			$mail = apply_filters( 'wpsl_send_coupon_mail',
				sprintf( __( 'Thank you for your order.
				
				Get a discount coupon for your next purchase: %s', 'wpsl' ), '<code style="margin-top: 20px; display: table; background-color: #f8f8f8; border: 1px solid #eaeaea; font-size: 20px; padding: 10px;">' . $this->discount['coupon_title'] . '</code>' )
			);
			wpsl_mail( $this->cart->userEmail, $subject, $mail );
		}
	}
}