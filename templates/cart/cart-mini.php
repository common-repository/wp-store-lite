<?php
/**
 * Show cart
 *
 * This template can be overridden by copying it to yourtheme/wpstore/cart/cart-mini.php.
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

?>

<div class="wps-product-cart">
	<a href="<?php echo get_permalink( wpsl_opt( 'cart_page' ) ); ?>">
		<p class="wps-cart-total"><?php _e( 'Total', 'wpsl' ); ?>: <span class="product-basket-total"></span><?php echo wpsl_opt(); ?></p>
		<p><?php _e( 'Go to cart', 'wpsl' ); ?></p>
	</a>
</div>
