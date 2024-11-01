<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * This template can be overridden by copying it to yourtheme/wpstore/single/product.php.
 *
 * @author 		wpStore
 * @version     2.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<?php do_action( 'wpsl_before_single_product_box' ); ?>

<div id="product-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php do_action( 'wpsl_before_single_product' ); ?>

	<?php
		/**
		 * wpsl_single_product hook.
		 *
		 * @hooked wpsl_single_product_gallery - 10
		 * @hooked wpsl_single_product_attributes - 20
		 * @hooked wpsl_single_product_tabs - 30
		 * @hooked wpsl_single_product_similar - 40
		 */
		do_action( 'wpsl_single_product' );
	?>

	<?php do_action( 'wpsl_after_single_product' ); ?>

</div>

<?php do_action( 'wpsl_after_single_product_box' ); ?>
