<?php
/**
 * Single sponsorship product price
 *
 * This template can be overridden by copying it to yourtheme/wpstore/single/price-sponsorship.php.
 *
 * @author  wpStore
 * @version 2.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post;
$price = get_post_meta( $post->ID, 'goal', true );
?>

<div class="wpsl-price__title">
	<span><?php _e( 'Project goal', 'wpsl' ); ?></span>
</div>

<span class="wpsl-price__value" data-price="<?php echo $price; ?>"><?php echo wpsl_price( $price, false ); ?></span>
<span class="wpsl-price__currency"><?php echo wpsl_opt(); ?></span>