<?php
/**
 * Displays the block of shipping methods selection in the order form
 *
 * This template can be overridden by copying it to yourtheme/wpstore/order/order-shipping.php.
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

$shipping = new WPSL_Shipping();
$order    = new WPSL_Order();
echo $order->get_fields(
	array(
		'usershipping' => array(
			'type'        => wpsl_opt( 'shipping' ) == '1' ? 'button' : 'empty',
			'name'        => 'usershipping',
			'title'       => __( 'Select shipping type', 'wpsl' ),
			'value'       => '',
			'placeholder' => __( 'Select shipping type', 'wpsl' ),
			'notice'      => __( 'The field must be filled', 'wpsl' ),
			'args'        => $shipping->get_delivery_list(),
			'class'       => 'column-2',
			'required'    => true,
		),
	)
);