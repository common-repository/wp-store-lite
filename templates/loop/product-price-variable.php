<?php
/**
 * Single variable product price
 *
 * This template can be overridden by copying it to yourtheme/wpstore/loop/product-price-variable.php.
 *
 * @author  wpStore
 * @version 2.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post;

?>

<?php if ( wpsl_opt( 'variation_show_min_price' ) == '1' ) : ?>
	<span><?php _e( 'from', 'wpsl' ); ?> </span><span class="wpsl-product__price_value"><?php echo wpsl_price( wpsl_get_min_price( $post->ID ) ); ?></span>
<?php else: ?>
	<span class="wpsl-product__price_value"><?php echo wpsl_price( wpsl_get_min_price( $post->ID ) ); ?></span>
	<span class="wpsl-product__price_value">-</span>
	<span class="wpsl-product__price_value"><?php echo wpsl_price( wpsl_get_max_price( $post->ID ) ); ?></span>
<?php endif; ?>