<?php
/**
 * The template for displaying product average rating
 *
 * This template can be overridden by copying it to yourtheme/wpstore/loop/product-rating.php.
 *
 * @author  wpStore
 * @version 2.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( wpsl_opt( 'tab_reviews_enable', true ) == false ) return;

global $post;
?>

<div class="wpsl-product__rating">
	<?php echo wpsl_get_rating( wpsl_post( '_rate' ) ); ?>
</div>
