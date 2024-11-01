<?php
/**
 * wpStore
 *
 * Extending the WordPress Media Uploader: Custom Tab
 *
 * @author	https://www.ibenic.com/extending-wordpress-media-uploader-custom-tab/
 * @since	2.5
 */


if ( ! defined( 'ABSPATH' ) ) exit;


if ( !is_admin() ) return;



/*
 * Add image to tab
 */
add_filter( 'ajax_query_attachments_args', 'wpsl_query_attachments', 99 );
function wpsl_query_attachments( $args ) {
	if( isset( $_POST['query']['zip'] ) ) {
		// image/gif, image/png, image/jpeg, application/zip, application/pdf etc.
		$args['post_mime_type'] = array( 'application/zip' );
		unset( $_POST['query']['zip'] );
	}
	return $args;
}



/*
 * Add the tab
 */
add_filter('media_upload_tabs', 'my_upload_tab');
function my_upload_tab($tabs) {
    $tabs['mytabname'] = __( 'Digital products', 'wpsl' );
    return $tabs;
}



/*
 * Call the new tab with wp_iframe
 */
add_action( 'media_upload_mytabname', 'add_my_new_form' );
function add_my_new_form() {
	echo '<div class="wps-media">';
    wp_iframe( 'my_new_form' );
	echo '</div>';
}



/*
 * Fill tab
 */
function my_new_form() {
    //echo media_upload_form(); // This function is used for print media uploader headers etc.
	//echo media_upload_library();
	//echo wp_media_upload_handler();
	//echo wp_iframe( 'media_upload_type_url_form', 'file' );
	//echo media_upload_gallery();
	//echo media_post_single_attachment_fields_to_edit();
	//echo media_upload_header();
		//echo media_upload_type_form();
	//echo media_upload_gallery_form();
	//echo media_upload_library_form();
}