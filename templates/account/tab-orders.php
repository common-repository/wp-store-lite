<?php
/**
 * wpStore
 *
 * Create account of buyer
 *
 * @author	wpStore
 * @since	1.5
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$orders = get_posts(
	array(
		'post_type'      => 'shop_order',
		'posts_per_page' => -1,
		'post_status'    => 'publish',
		'author'         => get_current_user_id(),
	)
);
?>

<div class="wpsl-orders">
	<div class="content order-content">
		<?php if ( !$orders ) : ?>
		<div class="wps-order-list wps-no-orders">
			<div class="wps-no-orders-img"></div>
			<p><?php _e( 'You have no orders', 'wpsl' ); ?></p>
			<p><?php _e( 'After making the first purchase, you will be able to follow your orders', 'wpsl' ); ?></p>
		</div>
		<?php else: ?>
		<div class="wps-order-list wps-no-orders">
			<div class="wps-no-orders-img"></div>
			<p><?php _e( 'To receive order details, select one from the list', 'wpsl' ); ?></p>
		</div>
		<?php endif; ?>
	</div>
	<div class="list order-list">
	<?php
	if ( $orders ) {
		foreach ( $orders as $order ) {
			echo '<div class="order" data-id="' . $order->ID . '" data-user="' . get_current_user_id() . '">' . $order->post_title . '</div>';
		}
	}
	?>
	</div>
</div>
	