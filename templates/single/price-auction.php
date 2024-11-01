<?php
/**
 * Single auction product price
 *
 * This template can be overridden by copying it to yourtheme/wpstore/single/price-auction.php.
 *
 * @author  wpStore
 * @version 2.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post;
if ( $price = get_post_meta( $post->ID, '_price', true ) ) {
	$price = $price;
} else {
	$price = get_post_meta( $post->ID, 'start_price', true );
}
?>
<?php if ( is_singular( 'product' ) ) : ?>
<div class="wpsl-price__title">
	<span><?php _e( 'Current price', 'wpsl' ); ?></span>
</div>
<?php endif; ?>
	
<span class="wpsl-price__value" data-price="<?php echo $price; ?>"><?php echo wpsl_price( $price, false ); ?></span>
<span class="wpsl-price__currency"><?php echo wpsl_opt(); ?></span>