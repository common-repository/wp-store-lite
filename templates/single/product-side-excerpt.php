<?php
/**
 * Single product excerpt box
 *
 * This template can be overridden by copying it to yourtheme/wpstore/single/product-side-excerpt.php.
 *
 * @author  wpStore
 * @version 2.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post;

?>

<div class="wpsl-desc">
	<?php echo get_post_meta( $post->ID, '_purchase_note', true ); ?>
</div>