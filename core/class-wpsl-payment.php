<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if ( is_admin() ) return;

/**
 * Class for payment
 *
 * @since 2.3.0
 */
class WPSL_Payment {

	/**
	 * Constructor
	 */
    public function __construct() {
		add_filter( 'wpsl_payment_method_ym',       array( $this, 'yandex_money' ) );
		add_filter( 'wpsl_payment_method_webmoney', array( $this, 'webmoney' ) );
		add_filter( 'wpsl_payment_method_paypal',   array( $this, 'paypal' ) );
		
		if( isset( $_GET['secret'] ) && $_GET['secret'] == wpsl_opt( 'secret' ) ) {
			add_action( 'wp',                       array( $this, 'payment_notification' ) );
		}	
		
		add_action( 'wpsl_payment_notification',    array( $this, 'payment_notification_yandex_money' ) );
    }
	
	/**
	 * Get payment methods
	 */
	function get_methods() {
		$methods = apply_filters( 'wpsl_get_methods',
			array(
				'cash' => array(
					'name'   => __( 'Cash', 'wpsl' ),
					'img'    => wpsl_opt( 'cash_icon' ),
					'desc'   => wpsl_opt( 'cash_note' ),
					'online' => false,
				),
				'ym' => array(
					'name'   => __( 'Yandex.Money', 'wpsl' ),
					'img'    => wpsl_opt( 'ymoney_icon' ),
					'desc'   => wpsl_opt( 'ym_note' ),
					'online' => true,
				),
				'webmoney' => array(
					'name'   => __( 'WebMoney', 'wpsl' ),
					'img'    => wpsl_opt( 'webmoney_icon' ),
					'desc'   => wpsl_opt( 'webmoney_note' ),
					'online' => true,
				),
				'paypal' => array(
					'name'   => __( 'PayPal', 'wpsl' ),
					'img'    => wpsl_opt( 'paypal_icon' ),
					'desc'   => wpsl_opt( 'paypal_note' ),
					'online' => true,
				),
				'cash_on_delivery' => array(
					'name'   => __( 'Cash on delivery', 'wpsl' ),
					'img'    => wpsl_opt( 'cash_on_delivery_icon' ),
					'desc'   => wpsl_opt( 'cash_on_delivery_note' ),
					'online' => false,
				),
				'bank_transfer' => array(
					'name'   => __( 'Bank transfer', 'wpsl' ),
					'img'    => wpsl_opt( 'bank_transfer_icon' ),
					'desc'   => wpsl_opt( 'bank_transfer_note' ),
					'online' => false,
				),
			)
		);
		if ( isset( $_GET['order_id'] ) && $_GET['order_id'] != '' ) {
			$index = get_post_meta( $this->order_id(), 'payment', true );
			if ( isset( $methods[$index] ) ) {
				return $methods[$index];
			}
		} else {
			return $methods;
		}
	}
	
	/**
	 * Get payment methods list in order page
	 */
	public function methods() {
		$methods = array();
		if ( $this->get_methods() ) {
			foreach ( $this->get_methods() as $k => $method ) {
				$methods[$k] = $method['name'];
			}
		}
		return $methods;
	}
	
	/**
	 * Get payment methods list in order page
	 */
	public function get_methods_list() {
		$methods = array();
		if ( $this->get_methods() ) {
			foreach ( $this->get_methods() as $k => $method ) {
				if ( wpsl_opt( $k ) == '1' ) $methods[$k] = $method;
			}
		}
		return $methods;
	}
	
	/**
	 * Get order id
	 */
	public function order_id() {
		if ( isset( $_GET['order_id'] ) && $_GET['order_id'] != '' ) {
			return (int)$_GET['order_id'];
		}
	}
	
	/**
	 * Get order code
	 */
	public function order_code() {
		return get_the_title( $this->order_id() );
	}
	
	/**
	 * Get order amount
	 */
	public function order_amount() {
		return get_post_meta( $this->order_id(), 'summa', true );
	}
	
	/**
	 * Only free products
	 */
	public function only_free() {
		$html = '';
		$texts = apply_filters( 'wpsl_only_free_products_in_cart',
			array(
				__( 'In your cart only free products. A message was sent to the e-mail specified in the order', 'wpsl' ),
				__( 'If after registration and payment you have not received links to the products ordered for your mail, you can get them in private account', 'wpsl' ),
			)
		);
		foreach ( $texts as $text ) {
			$html .= '<p>' . $text . '</p>';
		}
		return $html;
	}

	/**
	 * Yandex.Money payment system
	 */
	public function yandex_money() {
		$html = '<form method="POST" action="https://money.yandex.ru/quickpay/confirm.xml">

			<!--Номер кошелька в системе Яндекс.Деньги-->
			<input type="hidden" name="receiver" value="' . wpsl_opt( 'ymaccount' ) . '">
			<input type="hidden" name="formcomment" value="' . sanitize_text_field( wpsl_opt( 'email_from_name' ) ) . ' ' . __( 'Payment the order №', 'wpsl' ) . ' ' . get_the_title( $this->order_id() ) . '">
			 
			<!--Этот параметр передаёт ID плагина, для того, чтобы скрипту было понятно, что потом отсылать пользователю (длина 64 символа)-->
			<input type="hidden" name="label" value="' . get_the_title( $this->order_id() ) . '">
			<input type="hidden" name="quickpay-form" value="shop">
			<input type="hidden" name="targets" value="' . __( 'Payment the order №', 'wpsl' ) . ' ' . get_the_title( $this->order_id() ) . '">
			 
			<!--Сумма платежа, валюта - рубли по умолчанию-->
			<input type="hidden" name="sum" value="' . get_post_meta( $this->order_id(), 'summa', true ) . '" data-type="number">
			 
			<input type="hidden" name="need-fio" value="' . wpsl_opt( 'ymfio', 'false' ) . '">
			<input type="hidden" name="need-email" value="' . wpsl_opt( 'ymemail', 'false' ) . '">
			<input type="hidden" name="need-phone" value="' . wpsl_opt( 'ymphone', 'false' ) . '">
			<input type="hidden" name="need-address" value="' . wpsl_opt( 'ymaddress', 'false' ) . '">
			 
			<!--Метод оплаты, PC - Яндекс Деньги, AC - банковская карта-->
			<input type="hidden" name="paymentType" value="PC" />
			 
			<!--Куда перенаправлять пользователя после успешной оплаты платежа-->
			<input type="hidden" name="successURL" value="' . get_permalink( wpsl_opt( 'payment_page' ) ) . '?payment=success&order_id=' . $this->order_id() . '&token=' . get_post_meta( $this->order_id(), 'token', true ) . '">
			<button class="wpsl-ym">
				<span class="wpsl-ym__icon"></span>
				<span class="wpsl-ym__text">' . __( 'Pay', 'wpsl' )  . '</span>
			</button>
			</form>';
		return $html;
    }

	/**
	 * WebMoney payment system
	 */
	public function webmoney() {
		$html = '<form action="https://merchant.webmoney.ru/lmi/payment.asp" method="POST">
					<input type="hidden" name="LMI_PAYMENT_AMOUNT" value="' . get_post_meta( $this->order_id(), 'summa', true ) . '">
					<input type="hidden" name="LMI_PAYMENT_DESC_BASE64" value="' . get_the_title( $this->order_id() ) . '">
					<input type="hidden" name="LMI_PAYEE_PURSE" value="' . wpsl_opt( 'webmoney_account' ) . '">
					<input type="submit" class="wpsl-webmoney" value="' . __( 'Pay', 'wpsl' )  . ' ' . get_post_meta( $this->order_id(), 'summa', true ) . ' WMR">
				</form>';
		return $html;
    }
	
	/**
	 * PayPal payment system
	 */
	public function paypal() {
		$html = '<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
					<input type="hidden" name="cmd" value="_xclick">
					<input type="hidden" name="business" value="' . wpsl_opt( 'paypal_email' ) . '">
					<input type="hidden" name="lc" value="RU">
					<input type="hidden" name="item_name" data-product-input="linked" value="' . __( 'Payment the order №', 'wpsl' ) . ' ' . get_the_title( $this->order_id() ) . '">
					<input type="hidden" name="amount" data-product-input="price" value="' . get_post_meta( $this->order_id(), 'summa', true ) . '">
					<input type="hidden" name="currency_code" data-product-input="currency" value="' . wpsl_opt( 'paypal_currency' ) . '">
					<input type="hidden" name="button_subtype" value="services">
					<input type="hidden" name="no_note" value="0">
					<input type="hidden" name="bn" value="PP-BuyNowBF:btn_paynowCC_LG.gif:NonHostedGuest">
					<button type="submit" name="submit" class="wpsl-paypal">
						<span>' . __( 'Pay', 'wpsl' ) . '</span>
					</button>
					<span class="wpsl-paypal-txt">The safer, easier way to pay</span>
				</form>';
		return $html;
    }
	
	/**
	 * Payment form
	 */
	public function get_form() {
		if ( $method = get_post_meta( $this->order_id(), 'payment', true ) ) {
			return apply_filters( 'wpsl_payment_method_' . $method, $html = '', $this->order_id() );
		}
	}
	
	/**
	 * The action to be performed upon successful payment
	 */
	public function payment_notification() {
		do_action( 'wpsl_payment_notification' );
		exit;
	}
	
	/**
	 * The reception of the request about the successful payment from a Yandex.Money
	 *
	 * See all params in $_POST: https://yandex.ru/dev/money/doc/payment-buttons/reference/notifications-docpage/
	 */
	public function payment_notification_yandex_money() {
		
		if( !isset( $_GET['payment'] ) || $_GET['payment'] != 'ym' ) return;
		
		$sha1 = sha1(
			$_POST['notification_type'] . '&' . 
			$_POST['operation_id'] . '&' . 
			$_POST['amount'] . '&643&' . 
			$_POST['datetime'] . '&'. 
			$_POST['sender'] . '&' . 
			$_POST['codepro'] . '&' . 
			wpsl_opt( 'ymcode' ) . '&' . 
			$_POST['label']
		);
		
		/**
		 * Check if there was a substitution of the amount
		 * Exit if the verification is not passed and the amount of payment and order differ
		 */
		$order = get_page_by_title( $_POST['label'], '', 'shop_order' );
		if ( isset( $order->ID ) && isset( $_POST['sha1_hash'] ) && $sha1 != $_POST['sha1_hash'] && abs( trim( $_POST['withdraw_amount'] ) - get_post_meta( $order->ID, 'summa', true ) ) > 0.00001 ) return;
		
		// Set order statuse
		wpsl_set_statuses( $order->ID, array( 'payment'  => 'paid' ) );
		
		
		// Change order and products data
		do_action( 'wpsl_change_order_product', $order->ID, null );
		
		
		// Sent a letter to administrators about the successful payment
		set_query_var( 'wpsl_order_id', $order->ID );
		wpsl_mail( 'admin', __( 'Order payment received', 'wpsl' ), wpsl_get_template_html( 'email', 'admin-payment-received' ) );
		
		
		// We send a letter to the buyer about successful payment
		wpsl_mail( get_post_meta( $order->ID, 'email', true ), __( 'Your payment has been received', 'wpsl' ), wpsl_get_template_html( 'email', 'customer-payment-received' ) );
	}
}
new WPSL_Payment();