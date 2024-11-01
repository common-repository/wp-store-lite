<?php 
/**
 * wpStore
 *
 * Product attributes
 *
 * @author	wpStore
 * @since	2.1
 *
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<div class="wpsl-side">
	<?php
	/**
	 * Hook wpsl_before_price
	 */
	do_action( 'wpsl_before_price' );
	
	/**
	 * Include price box
	 */
	wpsl_get_template( 'single', 'product-side-price' );
	
	/**
	 * Hook wpsl_before_atts_box
	 */
	do_action( 'wpsl_before_atts_box' );
	
	/**
	 * Include attributes box
	 */
	if ( wpsl_opt( 'atts_to_tab' ) != true ) {
		wpsl_get_template( 'single', 'product-side-atts' );
	}
	
	/**
	 * Hook wpsl_before_buy_box
	 */
	do_action( 'wpsl_before_buy_box' );
	
	/**
	 * Include buy box
	 */
	wpsl_get_template( 'single', 'product-side-buy' );
	
	/**
	 * Hook wpsl_before_excerpt_box
	 */
	do_action( 'wpsl_before_excerpt_box' );
	
	/**
	 * Include excerpt box
	 */
	wpsl_get_template( 'single', 'product-side-excerpt' );
	
	/**
	 * Hook wpsl_after_excerpt_box
	 */
	do_action( 'wpsl_after_excerpt_box' );
	?>
</div>