<?php 
/**
 * wpStore
 *
 * The output of block sorting of product
 *
 * This template can be overridden by copying it to yourtheme/wpstore/loop/product-sorting.php.
 *
 * @author	wpStore
 * @since	2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( wpsl_opt( 'sorting_enable' ) != true ) return;

?>

<div class="wpsl-sort">
	<span class="wpsl-sort__title"><?php _e( 'Sort by', 'wpsl' ); ?>:</span>
	<?php echo wpsl_get_sort_form(); ?>				
</div>
