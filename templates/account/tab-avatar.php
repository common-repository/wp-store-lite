<?php 
/**
 * Displays the contents of the "Login" tab for registered and non-registered users
 *
 * This template can be overridden by copying it to yourtheme/wpstore/account/tab-avatar.php.
 *
 * @author	wpStore
 * @since	2.7
 */


if ( ! defined( 'ABSPATH' ) ) exit;

global $current_user;

if( is_user_logged_in() ) {
	printf( '<span>%s</span> %s', __( 'Online', 'wpsl' ), get_avatar( $current_user->ID, 350 ) );
} else {
	printf( '<span>%s</span> %s', __( 'Offline', 'wpsl' ), get_avatar( $current_user->ID, 350 ) );
}