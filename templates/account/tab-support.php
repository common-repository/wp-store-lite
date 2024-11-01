<?php
/**
 * Displays a list of technical support tickets
 *
 * This template can be overridden by copying it to yourtheme/wpstore/account/tab-support.php.
 *
 * @author	wpStore
 * @since	2.7
 */


if ( ! defined( 'ABSPATH' ) ) exit;


global $post;
$tickets = get_posts(
	array(
		'post_type'      => 'support',
		'posts_per_page' => -1,
		'post_status'    => 'publish',
		'author'         => get_current_user_id(),
	)
);

?>
 
<div class="wpsl-support">
	<?php do_action( 'wpsl_account_tab_support' ); ?>
	<div class="wpsl-support__content content tickets-content">
		<?php if ( !$tickets ) : ?>
		<div class="wps-ticket-list wps-no-tickets">
			<div class="wps-no-tickets-img"> </div>
			<p><?php _e( 'There are no support tickets', 'wpsl' ); ?></p>
			<p><?php _e( 'From here you can ask for support’s help & preview support tickets history', 'wpsl' ); ?></p>
		</div>
		<?php else: ?>
		<div class="wps-tickets-list wps-no-tickets">
			<div class="wps-no-tickets-img"></div>
			<p><?php _e( 'To receive ticket details, select one from the list', 'wpsl' ); ?></p>
		</div>
		<?php endif; ?>
	</div>
	<div class="wpsl-support__list list">
		<?php
		if ( $tickets ) {
			foreach ( $tickets as $ticket ) {
				echo '<div class="ticket" data-id="' . $ticket->ID . '" data-user="' . get_current_user_id() . '">' . $ticket->post_title . '</div>';
			}
		}
		?>
	</div>
</div>
