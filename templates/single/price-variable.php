<?php
/**
 * Single variable product price
 *
 * This template can be overridden by copying it to yourtheme/wpstore/single/price-variable.php.
 *
 * @author  wpStore
 * @version 2.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post;

?>

<div class="wpsl-price__title">
	<span><?php _e( 'Prices', 'wpsl' ); ?></span>
</div>

<?php if ( wpsl_opt( 'variation_show_min_price' ) == '1' ) : ?>
	<span><?php _e( 'from', 'wpsl' ); ?> </span><span class="wpsl-price__value"><?php echo wpsl_get_min_price( $post->ID ); ?></span>
	<span class="wpsl-price__currency"><?php echo wpsl_opt(); ?></span>
<?php else: ?>
	<span class="wpsl-price__value"><?php echo wpsl_get_min_price( $post->ID ); ?></span><span class="wpsl-price__value">-</span><span class="wpsl-price__value"><?php echo wpsl_get_max_price( $post->ID ); ?></span> 
	<span class="wpsl-price__currency"><?php echo wpsl_opt(); ?></span>
<?php endif; ?>