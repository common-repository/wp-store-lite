<?php
/**
 * Product reviews
 *
 * This template can be overridden by copying it to yourtheme/wpstore/single/product-reviews.php.
 *
 * @author  wpStore
 * @version 2.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $post, $current_user;

$fields = apply_filters( 'wpsl_review_form',
	array(
		array(
			'type'        => 'text',
			'name'        => 'name',
			'title'       => __( 'Your name', 'wpsl' ),
			'value'       => is_user_logged_in() ? $current_user->display_name : '',
			'class'       => '',
			'placeholder' => __( 'John Smith', 'wpsl' ),
			'required'    => 1,
			'notice'      => __( 'Your name', 'wpsl' )
		),
		array(
			'type'        => 'email',
			'name'        => 'email',
			'title'       => __( 'Your email', 'wpsl' ),
			'value'       => is_user_logged_in() ? $current_user->user_email : '',
			'class'       => '',
			'placeholder' => 'mail@mail.com',
			'required'    => 1,
			'notice'      => __( 'Your email', 'wpsl' )
		),
		array(
			'type'        => 'raiting',
			'name'        => 'assessment',
			'title'       => __( 'Your assessment', 'wpsl' ),
			'value'       => '',
			'class'       => '',
			'placeholder' => '',
			'required'    => 1,
			'notice'      => __( 'Your assessment', 'wpsl' )
		),
		array(
			'type'        => 'textarea',
			'name'        => 'plus',
			'title'       => __( 'Advantages', 'wpsl' ),
			'value'       => '',
			'class'       => '',
			'placeholder' => __( 'Advantages of product', 'wpsl' ),
			'required'    => 1,
			'notice'      => __( 'Advantages of product', 'wpsl' )
		),
		array(
			'type'        => 'textarea',
			'name'        => 'minus',
			'title'       => __( 'Disadvantages', 'wpsl' ),
			'value'       => '',
			'class'       => '',
			'placeholder' => __( 'Disadvantages of product', 'wpsl' ),
			'required'    => 1,
			'notice'      => __( 'Disadvantages of product', 'wpsl' )
		),
		array(
			'type'        => 'textarea',
			'name'        => 'comment',
			'title'       => __( 'Comment', 'wpsl' ),
			'value'       => '',
			'class'       => '',
			'placeholder' => __( 'Your comment', 'wpsl' ),
			'required'    => 0,
			'notice'      => __( 'Notice', 'wpsl' )
		),
		array(
			'type'        => 'hidden',
			'name'        => 'post_id',
			'value'       => $post->ID,
		),
		array(
			'type'        => 'hidden',
			'name'        => 'saveguard',
			'value'       => '',
		)
	)
);
$args = apply_filters( 'wpsl_review_form_args',
	array(
		'action'  => '',
		'onclick' => 'send-review',
	)
);

$reviews = wpsl_get_reviews( $post->ID );
?>
<!-- product reviews -->
<div class="wpsl-reviews">
	<div class="wpsl-reviews__form">
		<?php echo wpsl_get_form( $fields, $args ); ?>
		<div class="wpsl-notice wpsl-hidden">
			<?php echo wpautop( wpsl_opt( 'tab_reviews_desc' ) ); ?>
		</div>
	</div>
	<div class="wpsl-reviews__list">
		<div class="wpsl-review" data-grade="all">
			<div class="wpsl-review__data">
				<div class="wpsl-diagram">
					<div class="wpsl-diagram__rate"><?php echo wpsl_get_average_mark( $post->ID ); ?>
						<svg>
							<defs>
								<radialGradient id="radial" cx="0.5" cy="0.5" r="0.6" fx="0.4" fy="0.4">
									<stop offset="75%" stop-color="#e66300"></stop>
									<stop offset="100%" stop-color="#ff9800"></stop>
								</radialGradient>
							</defs>
							<circle r="48" cx="55" cy="55" style="stroke-dasharray: 339px 339px;"></circle>
							<circle r="48" cx="55" cy="55" style="stroke-linecap: round; stroke-dasharray: <?php echo round( wpsl_get_average_mark( $post->ID ) / 5 * 300 ); ?>px 339px;"></circle>
						</svg>
					</div>
				</div>
			</div>
			<div class="wpsl-review__box info">
				<div class="wpsl-diagram__title"><?php _e( 'Average assessment', 'wpsl' ); ?>:</div>
				<ul class="wpsl-mark">
					<li><span>5</span>
						<div class="wpsl-mark__item">
							<div class="wpsl-mark__item_bar"><div class="rate-bar" style="width: <?php $count = count( wpsl_get_reviews( $post->ID, 'assessment', 5 ) ); echo count( $reviews ) != '' ? round( $count / count( $reviews ) * 100 ) : '0'; ?>%;"></div></div>
							<a class="wpsl-mark__item_grade<?php echo $count == 0 ? ' wpsl-disabled' : ''; ?>" data-grade="5"><?php echo sprintf( _n( '%s review', '%s reviews', $count, 'wpsl' ), $count ); ?></a>
						</div>
					</li>
					<li><span>4</span>
						<div class="wpsl-mark__item">
							<div class="wpsl-mark__item_bar"><div class="rate-bar" style="width: <?php $count = count( wpsl_get_reviews( $post->ID, 'assessment', 4 ) ); echo count( $reviews ) != '' ? round( $count / count( $reviews ) * 100 ) : '0'; ?>%;"></div></div>
							<a class="wpsl-mark__item_grade<?php echo $count == 0 ? ' wpsl-disabled' : ''; ?>" data-grade="4"><?php echo sprintf( _n( '%s review', '%s reviews', $count, 'wpsl' ), $count ); ?></a>
						</div>
					</li>
					<li><span>3</span>
						<div class="wpsl-mark__item">
							<div class="wpsl-mark__item_bar"><div class="rate-bar" style="width: <?php $count = count( wpsl_get_reviews( $post->ID, 'assessment', 3 ) ); echo count( $reviews ) != '' ? round( $count / count( $reviews ) * 100 ) : '0'; ?>%;"></div></div>
							<a class="wpsl-mark__item_grade<?php echo $count == 0 ? ' wpsl-disabled' : ''; ?>" data-grade="3"><?php echo sprintf( _n( '%s review', '%s reviews', $count, 'wpsl' ), $count ); ?></a>
						</div>
					</li>
					<li><span>2</span>
						<div class="wpsl-mark__item">
							<div class="wpsl-mark__item_bar"><div class="rate-bar" style="width: <?php $count = count( wpsl_get_reviews( $post->ID, 'assessment', 2 ) ); echo count( $reviews ) != '' ? round( $count / count( $reviews ) * 100 ) : '0'; ?>%;"></div></div>
							<a class="wpsl-mark__item_grade<?php echo $count == 0 ? ' wpsl-disabled' : ''; ?>" data-grade="2"><?php echo sprintf( _n( '%s review', '%s reviews', $count, 'wpsl' ), $count ); ?></a>
						</div>
					</li>
					<li><span>1</span>
						<div class="wpsl-mark__item">
							<div class="wpsl-mark__item_bar"><div class="rate-bar" style="width: <?php $count = count( wpsl_get_reviews( $post->ID, 'assessment', 1 ) ); echo count( $reviews ) != '' ? round( $count / count( $reviews ) * 100 ) : '0'; ?>%;"></div></div>
							<a class="wpsl-mark__item_grade<?php echo $count == 0 ? ' wpsl-disabled' : ''; ?>" data-grade="1"><?php echo sprintf( _n( '%s review', '%s reviews', $count, 'wpsl' ), $count ); ?></a>
						</div>
					</li>
					<li>
						<div class="wpsl-mark__item">
							<a class="wpsl-mark__item_grade<?php echo count( $reviews ) == 0 ? ' wpsl-disabled' : ''; ?> active" data-grade="all"><?php $count = count( wpsl_get_reviews( $post->ID ) ); echo sprintf( _n( 'All %s review', 'All %s reviews', $count, 'wpsl' ), $count ); ?></a>
						</div>
					</li>
				</ul>
			</div>
			<div class="wpsl-review__add">
				<?php _e( 'Add review', 'wpsl' ); ?>
				<i class="icon-mic"></i>
			</div>
		</div>
	<?php
	if ( $reviews ) {
		foreach ( $reviews as $review ) {
			$assessment = get_comment_meta( $review->comment_ID, 'assessment', true ); ?>
			<div class="wpsl-review" data-grade="<?php echo $assessment; ?>">
				<div class="wpsl-review__data">
					<?php if ( $assessment ) : ?>
					<div class="wpsl-review__box_assessment"><?php echo wpsl_get_rating( $assessment ); ?></div>
					<?php endif; ?>
					<div class="wpsl-review__data_name"><span onclick="window.open('<?php echo get_permalink( wpsl_opt( 'pageaccount' ) ) . '?author=' . $review->user_id; ?>', '_blank');"><?php echo $review->comment_author; ?></span></div>
					<div class="wpsl-review__data_date"><?php echo mysql2date( 'd F Y H:i', $review->comment_date ); ?></div>
				</div>
				<div class="wpsl-review__box">
					<?php if ( $plus = get_comment_meta( $review->comment_ID, 'plus', true ) ) : ?>
					<div class="wpsl-review__box_title"><?php _e( 'Advantages of product', 'wpsl' ); ?></div>
					<div class="wpsl-review__box_desc"><?php echo $plus; ?></div>
					<?php endif; ?>

					<?php if ( $minus = get_comment_meta( $review->comment_ID, 'minus', true ) ) : ?>
					<div class="wpsl-review__box_title"><?php _e( 'Disadvantages of product', 'wpsl' ); ?></div>
					<div class="wpsl-review__box_desc"><?php echo $minus; ?></div>
					<?php endif; ?>

					<?php if ( $review->comment_content ) : ?>
					<div class="wpsl-review__box_title"><?php _e( 'Comment', 'wpsl' ); ?></div>
					<div class="wpsl-review__box_desc"><?php echo $review->comment_content; ?></div>
					<?php endif; ?>
				</div>
			</div>
		<?php
		}
	}?>
	</div>
</div>
