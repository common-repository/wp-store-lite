<?php
/**
 * Displays the contents of the "Login" tab for registered and non-registered users
 *
 * This template can be overridden by copying it to yourtheme/wpstore/account/tab-lostpassword.php.
 *
 * @author	wpStore
 * @since	2.7
 */


if ( ! defined( 'ABSPATH' ) ) exit;


if ( !is_user_logged_in() ) {
	echo '<div class="wpsl-lost">';
	do_action( 'wpsl_account_tab_lostpassword_before' );
	$fields = array(
		array(
			'type'        => 'text',
			'name'        => 'user_login',
			'title'       => __( 'User name or email', 'wpsl' ),
			'value'       => '',
			'class'       => '',
			'placeholder' => __( 'User name or email', 'wpsl' ),
			'required'    => 1,
			'notice'      => __( 'Notice', 'wpsl' )
		),
		array(
			'type'        => 'hidden',
			'name'        => 'redirect_to',
			'value'       => '',
		),
	);
	$args = array(
		'action'  => get_site_url( null, 'wp-login.php?action=lostpassword' ),
		'onclick' => 'lostpasswordform',
		'submit'  => '<i class="icon-telegram-app"></i>' . __( 'Send new password', 'wpsl' ),
		'ajax'    => false,
	);
	echo wpsl_get_form( $fields, $args );
	do_action( 'wpsl_account_tab_lostpassword_after' );
	echo '</div>';	
}