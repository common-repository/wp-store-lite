<?php
/**
 * Single auction product price
 *
 * This template can be overridden by copying it to yourtheme/wpstore/loop/product-price-auction.php.
 *
 * @author  wpStore
 * @version 2.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post;
$price = get_post_meta( $post->ID, '_price', true ) != '' ? get_post_meta( $post->ID, '_price', true ) : get_post_meta( $post->ID, 'start_price', true );
?>

<span class="wpsl-product__price_value"><?php echo wpsl_price( $price ); ?></span>
