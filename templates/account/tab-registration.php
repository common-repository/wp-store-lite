<?php
/**
 * Displays the user registration form
 *
 * This template can be overridden by copying it to yourtheme/wpstore/account/tab-registration.php.
 *
 * @author	wpStore
 * @since	2.7
 */


if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="wpsl-register">
	<?php
	if ( !is_user_logged_in() ) {
		do_action( 'wpsl_account_tab_registration_before' );
		$fields = array(
			array(
				'type'        => 'text',
				'name'        => 'user_login',
				'title'       => __( 'User name', 'wpsl' ),
				'value'       => '',
				'class'       => '',
				'placeholder' => __( 'User name', 'wpsl' ),
				'required'    => 1,
				'notice'      => __( 'Notice', 'wpsl' )
			),
			array(
				'type'        => 'text',
				'name'        => 'user_email',
				'title'       => __( 'User email', 'wpsl' ),
				'value'       => '',
				'class'       => '',
				'placeholder' => __( 'User email', 'wpsl' ),
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
			'action'  => get_site_url( null, 'wp-login.php?action=register' ),
			'onclick' => 'user_register',
			'submit'  => '<i class="icon-telegram-app"></i>' . __( 'Send', 'wpsl' ),
			'ajax'    => false,
		);
		echo wpsl_get_form( $fields, $args );
		do_action( 'wpsl_account_tab_registration_after' );
	}
	?>
</div>
