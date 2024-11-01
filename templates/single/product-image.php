<?php
/**
 * Product gallery
 *
 * @author  wpStore
 * @version 2.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


global $post;

$thumbs_ids = get_post_meta( $post->ID, '_product_image_gallery', true );
?>
<!-- product card -->
			<div class="wpsl-gallery">
				<div class="wpsl-gallery__thumb">
					<?php wpsl_get_template( 'single', 'product-icons' ); ?>
					<a href="<?php echo wpsl_get_thumbnail_url( $post->ID, 'wpsl-big-thumb' ); ?>" class="wpsl-item">
						<img src="<?php echo wpsl_get_thumbnail_url( $post->ID, 'wpsl-big-thumb' ); ?>" class="wpsl-item__img" data-id="0" data-title="<?php echo wp_get_attachment_caption( get_post_thumbnail_id( $post->ID ) ); ?>">
						<i class="wpsl-item__cover icon-zoom-in"></i>
					</a>
				</div>
				<div class="wpsl-gallery__box">
<?php if ( $thumbs_ids ) : ?>
					<div class="wpsl-thumbs">
						<a href="<?php echo wpsl_get_thumbnail_url( $post->ID, 'wpsl-big-thumb' ); ?>" class="wpsl-thumbs__main">
							<img src="<?php echo wpsl_get_thumbnail_url( $post->ID, 'wpsl-small-thumb' ); ?>" class="wpsl-item__img" data-id="0" data-title="<?php echo wp_get_attachment_caption( get_post_thumbnail_id( $post->ID ) ); ?>">
						</a>
						<?php
						$thumbs_ids = explode( ',', $thumbs_ids );
						$i = '1';
						foreach ( $thumbs_ids as $id ) {
							echo '<a href="' . wp_get_attachment_image_url( $id, 'wpsl-big-thumb' ) . '" class="wpsl-thumbs__main">
							<img src="' . wp_get_attachment_image_url( $id, 'wpsl-small-thumb' ) . '" class="wpsl-item__img" data-id="' . $i . '" data-title="' . wp_get_attachment_caption( $id ) . '">
						</a>';
						$i++;
						}
						?>
					</div>
					<?php endif; ?>
					
					<div class="wpsl-slider" style="display: none;">
						<div class="wpsl-slider__items">
							<div class="wpsl-slider__item">
								<div class="wpsl-slider__item-number">
								</div>
								
								<div class="wpsl-slider__item-inner">
									<img src="<?php echo wpsl_get_thumbnail_url( $post->ID, 'wpsl-big-thumb' ); ?>" alt="" class="wpsl-cur-img">
								</div>  
								<div class="wpsl-slider__item-title">
								</div>
								
								<?php if ( $thumbs_ids ) : ?>
								<span class="wpsl-btn wpsl-btn-prev icon-chevron-left"></span>
								<span class="wpsl-btn wpsl-btn-next icon-chevron-right"></span>
								<?php endif; ?>
								
								<span class="wpsl-btn-full icon-maximize"></span>
								<span class="wpsl-btn-close icon-x"></span>
							</div>
							<div class="wpsl-slider__item-comment">
							</div>
						</div>
					</div>
				</div>
			</div>