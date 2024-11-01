<?php 
if ( ! defined( 'ABSPATH' ) ) exit;

if ( is_admin() ) return;

/**
 * wpStore
 *
 * File for display metatags
 *
 * @since	2.3
 */
//add_filter( 'wp_title', 'wpsl_change_title', 10, 1 );
add_filter( 'pre_get_document_title', 'wpsl_change_title', 10, 1 );
function wpsl_change_title( $title ) {
	if ( is_singular( 'product' ) && wpsl_post( '_meta_title' ) ) {
		return wpsl_post( '_meta_title' );
	}
	return $title;
}


/**
 * Show description and keywords metatags
 */
add_action( 'wp_head', 'wpsl_show_product_meta' );
function wpsl_show_product_meta() {
	global $post;
	if ( !isset( $post->ID ) && !is_singular( 'product' ) ) return;
	
	if ( $desc = get_post_meta( $post->ID, '_description', true ) ) {
		echo '<meta name="description" content="' . $desc . '" />' . "\n";
	}
	if ( $key = get_post_meta( $post->ID, '_keywords', true ) ) {
		echo '<meta name="keywords" content="' . $key . '" />' . "\n";
	}
}