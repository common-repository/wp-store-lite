<?php
/**
 * Displays a form for send ticket
 *
 * This template can be overridden by copying it to yourtheme/wpstore/account/tab-ticket.php.
 *
 * @author	wpStore
 * @since	2.7
 */


if ( ! defined( 'ABSPATH' ) ) exit;

?>
 

		
<div class="wpsl-ticket">
	<?php do_action( 'wpsl_account_tab_ticket' ); ?>
	<?php
	$posts = get_posts(
		array(
			'numberposts' => -1,
			'orderby'     => 'date',
			'order'       => 'DESC',
			'post_type'   => 'shop_order',
			'author'      => get_current_user_id(),
			'post_status' => 'publish',
		)
	);
	$value = array(
		'0'  => __( 'On a free theme', 'wpsl' ),
	);
	if ( $posts ) {
		$i = 1;
		foreach( $posts as $post ){
			$value[$post->ID] = $post->post_title . ' (' . get_post_meta( $post->ID, 'summa', true ) . ' ' . wpsl_opt() . ')';
			$i++;
		}
	}
	$fields = array(
		array(
			'type'        => 'text',
			'name'        => 'ticket-title',
			'title'       => __( 'Ticket title', 'wpsl' ),
			'value'       => '',
			'class'       => '',
			'placeholder' => __( 'Ticket title', 'wpsl' ),
			'required'    => 1,
			'notice'      => __( 'Notice', 'wpsl' )
		),
		array(
			'type'        => 'select',
			'name'        => 'ticket-order',
			'title'       => __('Select № of order', 'wpsl'),
			'value'       => $value,
			'class'       => '',
			'required'    => 0,
			'notice'      => __( 'Notice', 'wpsl' )
		),
		array(
			'type'        => 'empty',
			'name'        => 'ticket-content',
			'required'    => 1,
		),
		array(
			'type'        => 'textarea',
			'name'        => 'ticket-content',
			'title'       => __( 'Ticket text', 'wpsl' ),
			'value'       => '',
			'class'       => '',
			'placeholder' => __( 'Ticket text', 'wpsl' ),
			'required'    => 1,
			'notice'      => __( 'Notice', 'wpsl' )
		),
		array(
			'type'        => 'hidden',
			'name'        => 'my_form_submit',
			'value'       => 1
		)
	);
	$args = array(
		'action'  => '',
		'onclick' => 'add_ticket',
		'submit'  => '<i class="icon-telegram-app"></i>' . __( 'Send', 'wpsl' ),
	);
	echo wpsl_get_form( $fields, $args ); ?>
	<div class="wps-adding-ticket"></div>
</div>
		