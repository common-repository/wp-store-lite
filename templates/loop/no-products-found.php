<?php
/**
 * Display a message about the absence of products
 *
 * This template can be overridden by copying it to yourtheme/wpstore/loop/no-products-found.php.
 *
 * @author  wpStore
 * @version 2.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<!-- product -->
<div class="wpsl-loop__noproduct">
	<p>
		<span><?php _e( 'No products were found matching your selection.', 'wpsl' ); ?></span>
		<img src="<?php echo WPSL_URL . '/assets/img/no-products.png'; ?>" width="300px" alt="<?php _e( 'No products were found matching your selection.', 'wpsl' ); ?>" />
	</p>
</div>