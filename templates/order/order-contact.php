<?php
/**
 * Displays the block contacts fields in the order form
 *
 * This template can be overridden by copying it to yourtheme/wpstore/order/order-contact.php.
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

$order = new WPSL_Order();
$cart  = WPSL_Cart::create();
echo $order->get_fields(
	array(
		$order::USER_NAME => array(
			'type'        => wpsl_opt( $order::USER_NAME ) != 'noactive' ? 'text' : 'empty',
			'name'        => $order::USER_NAME,
			'title'       => __( 'Your name', 'wpsl' ),
			'value'       => $cart->userName,
			'placeholder' => __( 'John Smith', 'wpsl' ),
			'required'    => wpsl_opt( $order::USER_NAME ) == 'required' ? true : false,
			'notice'      => __( 'The field must be filled', 'wpsl' ),
			'class'       => '',
		),
		$order::USER_EMAIL => array(
			'type'        => 'email',
			'name'        => $order::USER_EMAIL,
			'title'       => __( 'Your email', 'wpsl' ),
			'value'       => $cart->userEmail,
			'placeholder' => __( 'Your email', 'wpsl' ),
			'required'    => true,
			'notice'      => __( 'The field must be filled', 'wpsl' ),
			'class'       => '',
		),
		$order::USER_PHONE => array(
			'type'        => wpsl_opt( 'sms_confirm' ) == '1' ? 'text' : 'empty',
			'name'        => $order::USER_PHONE,
			'title'       => __( 'Your phone', 'wpsl' ),
			'value'       => $cart->userPhone,
			'placeholder' => __( 'Phone', 'wpsl' ),
			'notice'      => '',
			'required'    => 'required',
			'class'       => '',
		),
		'sms_password' => array(
			'type'        => wpsl_opt( 'sms_confirm' ) == '1' ? 'sms' : 'empty',
			'name'        => 'sms_password',
			'title'       => __( 'Get code', 'wpsl' ),
			'value'       => '',
			'placeholder' => __( 'Code from SMS', 'wpsl' ),
			'notice'      => '',
			'required'    => true,
			'class'       => '',
		),
		$order::USER_COMMENT => array(
			'type'        => wpsl_opt( $order::USER_COMMENT ) != 'noactive' ? 'textarea' : 'empty',
			'name'        => $order::USER_COMMENT,
			'title'       => __( 'Additional information', 'wpsl' ),
			'value'       => $cart->userComment,
			'placeholder' => __( 'Additional information', 'wpsl' ),
			'notice'      => '',
			'required'    => wpsl_opt( $order::USER_COMMENT ) == 'required' ? true : false,
			'class'       => '',
		),
		'sms' => array(
			'type'        => wpsl_opt( 'sms_confirm' ) == '1' ? 'password' : 'empty',
			'name'        => 'sms',
			'title'       => __( 'Confirmation code from SMS', 'wpsl' ),
			'value'       => '',
			'placeholder' => 'XXXX',
			'required'    => true,
			'notice'      => __( 'The field must be filled', 'wpsl' ),
			'class'       => '',
		),
	)
);