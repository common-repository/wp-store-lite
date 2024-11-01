<?php
/**
 * wpStore
 *
 * Shortcodes
 *
 * @author	wpStore
 * @since	2.0
 */


if ( ! defined( 'ABSPATH' ) ) exit;


if ( is_admin() ) return;


class WPSL_Shortcodes {

	/**
	 * Show product categories
	 *
	 * @since	2.0
	 */
	public static function categories_list() {
		$catlist = get_categories(
			array(
				'parent'     => 0,
				'type'       => 'product',
				'hide_empty' => 0,
				'number'     => '0',
				'taxonomy'   => 'product_cat',
				'pad_counts' => true
			 )
		);
		if ( $catlist ) {
			echo '<ul class="wpsl-categories">';
			foreach ( $catlist as $categories_item ) {
				echo '<li class="wpsl-categories__item"><a href="' . get_term_link( $categories_item->term_id, 'product_cat' ) . '"><img src="' . wpsl_cat_thumbnail( get_term_meta( $categories_item->term_id, '_thumbnail_id', 1 ) ) . '" alt="" /><h3>' . $categories_item->cat_name . '</h3></a><span class="small-text">' . $categories_item->category_description . '</span></li>';
			}
			echo '</ul>';
		}
	}

	/**
	 * Show storefront
	 *
	 * @since	1.1
	 */
	public static function storefront() {
		wpsl_content();
	}

	/**
	 * Show contactform on your shop
	 *
	 * @since	1.5
	 */
	public static function contactform() {
		$fields = array(
			array(
				'type'        => 'text',
				'name'        => 'name',
				'title'       => __( 'Your name', 'wpsl' ),
				'value'       => '',
				'class'       => '',
				'placeholder' => __( 'John Smith', 'wpsl' ),
				'required'    => 1,
				'notice'      => __( 'Notice', 'wpsl' )
			 ),
			array(
				'type'        => 'email',
				'name'        => 'email',
				'title'       => __( 'Your email', 'wpsl' ),
				'value'       => '',
				'class'       => '',
				'placeholder' => 'mail@mail.com',
				'required'    => 1,
				'notice'      => __( 'Notice', 'wpsl' )
			 ),
			array(
				'type'        => 'text',
				'name'        => 'subject',
				'title'       => __( 'Subject', 'wpsl' ),
				'value'       => '',
				'class'       => '',
				'placeholder' => __( 'Subject', 'wpsl' ),
				'required'    => 0,
				'notice'      => __( 'Notice', 'wpsl' )
			 ),
			array(
				'type'        => 'textarea',
				'name'        => 'message',
				'title'       => __( 'Your message', 'wpsl' ),
				'value'       => '',
				'class'       => '',
				'placeholder' => __( 'Your message', 'wpsl' ),
				'required'    => 1,
				'notice'      => __( 'Notice', 'wpsl' )
			 ),
			array(
				'type'        => 'hidden',
				'name'        => 'my_form_submit',
				'value'       => 1
			 )
		 );
		$args = array(
			'action'  => '',
			'onclick' => 'contact_form'
		 );
		ob_start(); ?>
		<div class="wpsl-contactform">
			<div class="wpsl-contactform__header">
				<h2><?php _e( 'Contact form', 'wpsl' ); ?></h2>
				<p><?php _e( 'contact us in any convenient way for you', 'wpsl' ); ?></p>
			</div>
			<div class="wpsl-contactform__body">
				<?php echo wpsl_get_form( $fields, $args ); ?>
			</div>
		</div>
		<?php
		$contactform = ob_get_contents();
		ob_end_clean();
		return $contactform;
	}

	/**
	 * Show order
	 *
	 * @since	1.5
	 */
	public static function mini_cart() {
		return wpsl_get_template_html( 'cart', 'cart-mini' );
	}

	/**
	 * Output of the personal account page via shortcode [wpsl-account]
	 *
	 * @since	1.5.0
	 */
	public static function account() {
		return wpsl_get_template_html( 'account', 'tab' );
	}

	/**
	 * Show cart
	 *
	 * @since	2.7
	 */
	public static function cart() {
		// обработка формы заказа
		$orderForm = new WPSL_Order();
		$orderForm->handle();
		// show cart
		$cart = WPSL_Cart::create();
		return $cart->getHTML();
	}

	/**
	 * Show order form
	 *
	 * @since	1.5
	 */
	public static function checkout() {
		// обработка формы заказа
		$orderForm = new WPSL_Order();
		$orderForm->handle();
		return wpsl_get_template_html( 'order', 'order' );
	}

	/**
	 * Payment form
	 *
	 * @since	2.7.0
	 */
	public static function payment() {
		$payment = new WPSL_Payment();
		if ( !$payment->order_id() && empty( $_GET ) ) {
			return wpsl_get_template_html( 'payment', 'payment-empty' );
		}
		if ( isset( $_GET['payment'] ) && $_GET['payment'] == 'process' && $payment->order_id() ) {
			return wpsl_get_template_html( 'payment', 'payment' );
		}
		if ( isset( $_GET['payment'] ) && $_GET['payment'] == 'success' && $payment->order_id() ) {
			if ( isset( $_GET['token'] ) && $_GET['token'] == get_post_meta( $payment->order_id(), 'token', true ) ) {
				set_query_var( 'wpsl_order_id', $payment->order_id() );
				return wpsl_get_template_html( 'payment', 'payment-successful' );
			} else {
				return esc_html__( 'You are trying to get data with the wrong token', 'wpsl' );
			}
		}
	}

}
add_shortcode( 'wpsl-categories',  array( 'WPSL_Shortcodes', 'categories_list' ) );
add_shortcode( 'wpsl-storefront',  array( 'WPSL_Shortcodes', 'storefront' ) );
add_shortcode( 'wpsl-contactform', array( 'WPSL_Shortcodes', 'contactform' ) );
add_shortcode( 'wpsl-account',     array( 'WPSL_Shortcodes', 'account' ) );
add_shortcode( 'wpsl-minicart',    array( 'WPSL_Shortcodes', 'mini_cart' ) );
add_shortcode( 'wpsl-cart',        array( 'WPSL_Shortcodes', 'cart' ) );
add_shortcode( 'wpsl-checkout',    array( 'WPSL_Shortcodes', 'checkout' ) );
add_shortcode( 'wpsl-payment',     array( 'WPSL_Shortcodes', 'payment' ) );


/**
 * Add support for shortcodes in widgets
 */
add_filter( 'widget_text', 'do_shortcode' );
