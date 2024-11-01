<?php 
/**
 * wpStore
 *
 * Displaying the thumbnail of product
 *
 * This template can be overridden by copying it to yourtheme/wpstore/loop/product-image.php.
 *
 * @author	wpStore
 * @since	2.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;  

global $post;
?>

<div class="wpsl-product__img">
	<?php echo wpsl_get_thumbnail( $post->ID ); ?>
</div>
