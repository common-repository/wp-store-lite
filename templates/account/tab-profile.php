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

global $current_user;
$fields = array(
	array(
		'type'        => 'email',
		'name'        => 'user_email',
		'title'       => __( 'Email', 'wpsl' ),
		'value'       => $current_user->user_email,
		'class'       => '',
		'placeholder' => __( 'Email', 'wpsl' ),
		'required'    => 1,
		'notice'      => __( 'Notice', 'wpsl' )
	),
	array(
		'type'        => 'text',
		'name'        => 'first_name',
		'title'       => __( 'Name', 'wpsl' ),
		'value'       => $current_user->first_name,
		'class'       => '',
		'placeholder' => __( 'Name', 'wpsl' ),
		'required'    => 0,
		'notice'      => __( 'Notice', 'wpsl' )
	),
	array(
		'type'        => 'text',
		'name'        => 'phone',
		'title'       => __( 'Phone', 'wpsl' ),
		'value'       => get_user_meta( $current_user->ID, 'phone', true ),
		'class'       => '',
		'placeholder' => __( 'Phone', 'wpsl' ),
		'required'    => 0,
		'notice'      => __( 'Notice', 'wpsl' )
	),
	array(
		'type'        => 'textarea',
		'name'        => 'address',
		'title'       => __( 'Address', 'wpsl' ),
		'value'       => get_user_meta( $current_user->ID, 'address', true ),
		'class'       => '',
		'placeholder' => __( 'Address', 'wpsl' ),
		'required'    => 0,
		'notice'      => __( 'Notice', 'wpsl' )
	),
	array(
		'type'        => 'text',
		'name'        => 'zip',
		'title'       => __( 'ZIP or postal code', 'wpsl' ),
		'value'       => get_user_meta( $current_user->ID, 'zip', true ),
		'class'       => '',
		'placeholder' => __( 'ZIP or postal code', 'wpsl' ),
		'required'    => 0,
		'notice'      => __( 'Notice', 'wpsl' )
	),
	array(
		'type'        => 'textarea',
		'name'        => 'description',
		'title'       => __( 'About', 'wpsl' ),
		'value'       => $current_user->description,
		'class'       => '',
		'placeholder' => __( 'About', 'wpsl' ),
		'required'    => 0,
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
	'onclick' => 'update_profile',
	'submit'  => '<i class="icon-save"></i>' . __( 'Save', 'wpsl' )
);
?>

<div class="wpsl-profile">
	<?php do_action( 'wpsl_account_tab_profile' ); ?>
	<?php echo wpsl_get_form( $fields, $args ); ?>
</div>