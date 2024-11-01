<?php
/**
 * Displays the contents of the "Login" tab for registered and non-registered users
 *
 * This template can be overridden by copying it to yourtheme/wpstore/account/tab-login.php.
 *
 * @author	wpStore
 * @since	2.7
 */


if ( ! defined( 'ABSPATH' ) ) exit;


if ( !is_user_logged_in() ) {
	echo '<div class="wpsl-login">';
	do_action( 'wpsl_account_tab_login_before' );
	$fields = array(
		array(
			'type'        => 'text',
			'name'        => 'log',
			'title'       => __( 'User name or email', 'wpsl' ),
			'value'       => '',
			'class'       => '',
			'placeholder' => __( 'User name or email', 'wpsl' ),
			'required'    => 1,
			'notice'      => __( 'Notice', 'wpsl' )
		),
		array(
			'type'        => 'password',
			'name'        => 'pwd',
			'title'       => __( 'User password', 'wpsl' ),
			'value'       => '',
			'class'       => '',
			'placeholder' => __( 'User password', 'wpsl' ),
			'required'    => 1,
			'notice'      => __( 'Notice', 'wpsl' )
		),
		array(
			'type'        => 'checkbox',
			'name'        => 'rememberme',
			'title'       => __( 'Remember me', 'wpsl' ),
			'value'       => 'forever',
			'class'       => '',
			'placeholder' => '',
			'required'    => 0,
			'notice'      => __( 'Notice', 'wpsl' )
		),
		array(
			'type'        => 'hidden',
			'name'        => 'redirect_to',
			'value'       => get_page_uri( wpsl_opt( 'pageaccount' ) ),
		),
	);
	$args = array(
		'action'  => get_site_url( null, 'wp-login.php' ),
		'onclick' => 'loginform',
		'submit'  => '<i class="icon-telegram-app"></i>' . __( 'Login', 'wpsl' ),
		'ajax'    => false,
	);
	echo wpsl_get_form( $fields, $args );
	do_action( 'wpsl_account_tab_login_after' );
	echo '</div>';
} else {
	echo '<i class="icon-log-out"></i><a href="' . wp_logout_url( get_page_uri( wpsl_opt( 'pageaccount' ) ) ) . '" title="' . __( 'Log out', 'wpsl' ) . '">' . __( 'Log out', 'wpsl' ) . '</a>';
}