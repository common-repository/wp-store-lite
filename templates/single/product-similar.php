<?php 
/**
 * Show similar products
 *
 * @author	wpStore
 * @since	2.1
 */


if ( ! defined( 'ABSPATH' ) ) exit;


if ( wpsl_opt( 'similarproduct' ) != '1' ) return;


global $post;
$products = wpsl_get_similar( $post->ID, wpsl_opt( 'similar_product_count', 8 ) );
if ( !$products->have_posts() ) return;
?>
<div class="wpsl-carousel">
	<div class="wpsl-carousel__title">
		<span class="title"><?php _e( 'Similar products', 'wpsl' ); ?></span>
		<span class="wpsl-carousel__control prev" data-slide="prev"><i class="icon-chevron-left"></i></span>
		<span class="wpsl-carousel__control next" data-slide="next"><i class="icon-chevron-right"></i></span>
	</div>
	<div class="wpsl-carousel__wrap">
		<?php while ( $products->have_posts() ) : $products->the_post(); ?>
		<div class="wpsl-carousel__wrap_item">
			<?php echo wpsl_get_thumbnail( $post->ID ); ?>
			<a class="wpsl-carousel__wrap_title" href="<?php echo wpsl_get_permalink(); ?>" target="_blank"><?php the_title(); ?></a>
			<div class="wpsl-carousel__wrap_price"><?php wpsl_get_template( 'loop', 'product-price-' . wpsl_product_type() ); ?></div>
		</div>
		<?php endwhile; ?>
	</div>
</div>

<?php wp_reset_query();