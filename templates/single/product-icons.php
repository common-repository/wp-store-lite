<?php 
/**
 * wpStore
 *
 * Displaying the icons of "HIT" and "NEW"
 *
 * This template can be overridden by copying it to yourtheme/wpstore/single/product-icons.php.
 *
 * @author	wpStore
 * @since	2.0
 */


if ( ! defined( 'ABSPATH' ) ) exit;  


if ( is_admin() ) return;
?>

<div class="wpsl-icon">
	<?php do_action( 'wpsl_product_icons' ); ?>
</div>
