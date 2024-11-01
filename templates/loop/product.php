<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/wpstore/loop/product.php.
 *
 * @author  wpStore
 * @version 2.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<!-- product -->
<div class="wpsl-loop__product">
	<div class="wpsl-product">
		<?php
		/**
		 * wpsl_loop_product_icons_before hook.
		 */
		do_action( 'wpsl_loop_product_icons_before' );
		
		wpsl_get_template( 'loop', 'product-icons' );
		
		/**
		 * wpsl_loop_product_rating_before hook.
		 */
		do_action( 'wpsl_loop_product_rating_before' );
		
		wpsl_get_template( 'loop', 'product-rating' );
		
		/**
		 * wpsl_loop_product_icons_before hook.
		 */
		do_action( 'wpsl_loop_product_image_before' );
		
		wpsl_get_template( 'loop', 'product-image' );
		
		/**
		 * wpsl_loop_product_icons_before hook.
		 */
		do_action( 'wpsl_loop_product_info_before' );
		
		wpsl_get_template( 'loop', 'product-info' );
		
		/**
		 * wpsl_loop_product_info_after hook.
		 */
		do_action( 'wpsl_loop_product_info_after' );
		?>
	</div>
</div>