<?php
/**
 * Single simple product price
 *
 * This template can be overridden by copying it to yourtheme/wpstore/loop/product-price-simple.php.
 *
 * @author  wpStore
 * @version 2.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post;
?>

<span class="wpsl-product__price_value"><?php echo wpsl_price( get_post_meta( $post->ID, '_price', true ) ); ?></span>