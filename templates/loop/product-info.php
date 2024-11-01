<?php 
/**
 * wpStore
 *
 * Display a block with information about the product
 *
 * This template can be overridden by copying it to yourtheme/wpstore/loop/product-info.php.
 *
 * @author	wpStore
 * @since	2.7.0
 */


if ( ! defined( 'ABSPATH' ) ) exit;
?>

<div class="wpsl-product__info">
	<div class="wpsl-product__info_title"><a href="<?php echo wpsl_get_permalink(); ?>" title="<?php the_title(); ?>" target="_blank"><?php the_title(); ?></a></div>
	<div class="wpsl-product__info_main">
		<div class="wpsl-product__price">
			<?php wpsl_get_template( 'loop', 'product-price-' . wpsl_product_type() ); ?>
		</div>
		<div class="button"><?php echo wpsl_get_buy_button(); ?></div>
	</div>
</div>
