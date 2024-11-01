<?php
/**
 * Show cart
 *
 * This template can be overridden by copying it to yourtheme/wpstore/cart/cart.php.
 *
 * HOWEVER, on occasion wpStore will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @author  wpStore
 * @version 2.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


$cart = WPSL_Cart::create();

$col = array(
	array(
		'photo'  => __( 'Photo', 'wpsl' ),
		'title'  => __( 'Product', 'wpsl' ),
		'price'  => __( 'Price', 'wpsl' ),
		'quo'    => __( 'Quantity', 'wpsl' ),
		'summ'   => __( 'Total', 'wpsl' ),
		'delete' => '',
	)
);

$class = $cart->getTotal() > (int)wpsl_opt( 'cart_minimum' ) ? '' : 'wpsl-disabled';
$fields = array(
	array(
		'type'        => 'custom',
		'class'       => 'cart',
		'fill'        => $cart->get_table( array_merge( $col, $cart->get_items() ) ),
	),
	array(
		'type'        => 'custom',
		'class'       => 'notif',
		'fill'        => $cart->get_notif(),
	),
	array(
		'type'        => wpsl_opt( 'cart_coupons' ) == true ? 'coupon' : 'hidden',
		'name'        => 'coupon_code',
		'title'       => __( 'Coupon code', 'wpsl' ),
		'value'       => '',
		'class'       => 'coupon',
		'placeholder' => __( 'Coupon code', 'wpsl' ),
		'required'    => 0,
		'notice'      => __( 'Coupon code', 'wpsl' )
	),
	array(
		'type'        => 'custom',
		'class'       => 'subtotal',
		'fill'        => $cart->get_subtotal(),
	),
	array(
		'type'        => 'custom',
		'class'       => 'to-order',
		'fill'        => '<a href="' . get_permalink( wpsl_opt( 'order_page' ) ) . '" class="to-order ' . $class . '">' . __( 'Proceed to checkout', 'wpsl' ) . '</a>',
	),
);
$args = array(
	'action'  => get_permalink( wpsl_opt( 'cart_page' ) ),
	'onclick' => 'update_cart',
	'class'   => 'wpsl-cart',
	'submit'  => '',
	'ajax'    => false,
);

echo wpsl_get_form( $fields, $args );
