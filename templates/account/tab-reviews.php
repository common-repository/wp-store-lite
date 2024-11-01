<?php
/**
 * Personal account reviews output
 *
 * This template can be overridden by copying it to yourtheme/wpstore/account/tab-reviews.php.
 *
 * @author	wpStore
 * @since	2.7
 */

if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="wps-reviews">
	<?php if( $reviews = wpsl_get_user_reviews() ) : ?>
	<div class="wps-reviews-list">
		<div class="wps-review-list">
			<div class="wps-review wps-review-date"><?php _e( 'Date', 'wpsl' ); ?></div>
			<div class="wps-review wps-review-title"><?php _e( 'Product', 'wpsl' ); ?></div>
			<div class="wps-review wps-review-content"><?php _e( 'Review', 'wpsl' ); ?></div>
		</div>
		<div class="wps-review-list">
			<?php foreach( $reviews as $id => $review ) : ?>
			<div class="wps-review-id" data-product-id="<?php echo $id; ?>">
				<div class="wps-review wps-review-date"><?php echo $review['comment_ID'] != '' ? get_comment_date( 'j.n.Y', $review['comment_ID'] ) : '-'; ?></div>
				<div class="wps-review wps-review-title"><a href="<?php echo wpsl_get_permalink( wpsl_product_id( $id ) ); ?>" target="_blank"><?php echo get_the_title( $id ); ?></a></div>
				<div class="wps-review wps-review-content"><?php echo $review['comment_ID'] != '' ? wpsl_get_review( $review['comment_ID'] ) : __( 'Leave a review about this product!' , 'wpsl' ); ?></div>
			</div>
			<?php endforeach; ?>
		</div>
	</div>
	<?php else: ?>
	<div class="wps-review-list wps-no-tickets">
		<div class="wps-no-reviews-img"> </div>
		<p><?php _e( 'You have no reviews', 'wpsl' ); ?></p>
		<p><?php _e( 'Evaluate the purchased products!', 'wpsl' ); ?></p>
	</div>
	<?php endif; ?>
</div>