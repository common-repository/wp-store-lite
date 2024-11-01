<?php
/**
 * Single product buy button box
 *
 * This template can be overridden by copying it to yourtheme/wpstore/single/product-side-buy.php.
 *
 * @author  wpStore
 * @version 2.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


$args = apply_filters( 'wpsl_get_form_submit_' . wpsl_product_type(),
	array(
		'action'  => '',
		'method'  => 'get',
		'onclick' => 'send_' . wpsl_product_type(),
		'submit'  => '<i class="icon-shopping-cart"></i> ' . wpsl_buy_caption(),
	)
);
echo wpsl_get_form( apply_filters( 'wpsl_get_form_' . wpsl_product_type(), $fields = array() ), $args );