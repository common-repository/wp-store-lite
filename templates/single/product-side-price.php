<?php
/**
 * Single product price
 *
 * This template can be overridden by copying it to yourtheme/wpstore/single/product-side-price.php.
 *
 * @author  wpStore
 * @version 2.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div class="wpsl-price">
	<?php
	wpsl_get_template( 'single', 'price-' . wpsl_product_type() );

	if ( wpsl_post( '_sale_price' ) && $price = wpsl_post( '_regular_price' ) ) {
		echo '<div class="wpsl-price__sale">' . __( "Old price", "wpsl" ) . '<p>' . $price . ' ' . wpsl_opt( 'currency_symbol' ) . '</p></div>';
	}
	?>
</div>