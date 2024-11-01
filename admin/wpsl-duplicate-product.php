<?php
/**
 * wpStore
 *
 * Create link for duplicate product in admin page
 *
 * @author	wpStore
 * @since	2.1
 */


if ( ! defined( 'ABSPATH' ) ) exit;


if ( !is_admin() ) return;


/*
 * The function creates a duplicate post as a draft and redirects to its edit page
 */
add_action( 'admin_action_wpsl_duplicate_post_as_draft', 'wpsl_duplicate_post_as_draft' );
function wpsl_duplicate_post_as_draft() {
	
	if( ! current_user_can( 'edit_posts' ) ) return;
	
	global $wpdb;
	if ( ! ( isset( $_GET['post'] ) || isset( $_POST['post'] )  || ( isset( $_REQUEST['action'] ) && 'wpsl_duplicate_post_as_draft' == $_REQUEST['action'] ) ) ) {
		wp_die( __( 'There is nothing to duplicate!', 'wpsl' ) );
	}
 
	/*
	 * Get ID of the original post
	 */
	$post_id = ( isset( $_GET['post'] ) ? $_GET['post'] : $_POST['post'] );
	$post = get_post( $post_id );
 
	/*
	 * If you don't want the current author to be the author of the new post
	 * then replace the following two lines with: $new_post_author = $post->post_author;
	 * when replacing these lines, the author will be copied from the original post
	 */
	$current_user = wp_get_current_user();
	$new_post_author = $current_user->ID;
 
	/*
	 * If the post exists, create a duplicate of it
	 */
	if ( isset( $post ) && $post != null ) {
 
		/*
		 * New post data array
		 */
		$args = array( 
			'comment_status' => $post->comment_status,
			'ping_status'    => $post->ping_status,
			'post_author'    => $new_post_author,
			'post_content'   => $post->post_content,
			'post_excerpt'   => $post->post_excerpt,
			'post_name'      => $post->post_name,
			'post_parent'    => $post->post_parent,
			'post_password'  => $post->post_password,
			'post_status'    => 'draft',
			'post_title'     => $post->post_title,
			'post_type'      => $post->post_type,
			'to_ping'        => $post->to_ping,
			'menu_order'     => $post->menu_order
		);
 
		/*
		 * Create a post using the wp_insert_post()
		 */
		$new_post_id = wp_insert_post( $args );
 
		/*
		 * Assign the new post all the elements of taxonomies (headings, labels, etc.) of the old
		 */
		$taxonomies = get_object_taxonomies( $post->post_type );
		foreach ( $taxonomies as $taxonomy ) {
			$post_terms = wp_get_object_terms( $post_id, $taxonomy, array( 'fields' => 'slugs' ) );
			wp_set_object_terms( $new_post_id, $post_terms, $taxonomy, false );
		}
 
		/*
		 * Duplicate all the custom fields
		 */
		$post_meta_infos = $wpdb->get_results( "SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$post_id" );
		if ( count( $post_meta_infos )!=0 ) {
			$sql_query = "INSERT INTO $wpdb->postmeta ( post_id, meta_key, meta_value ) ";
			foreach ( $post_meta_infos as $meta_info ) {
				$meta_key = $meta_info->meta_key;
				$meta_value = addslashes( $meta_info->meta_value );
				$sql_query_sel[]= "SELECT $new_post_id, '$meta_key', '$meta_value'";
			}
			$sql_query.= implode( " UNION ALL ", $sql_query_sel );
			$wpdb->query( $sql_query );
		}
 
		/*
		 * Redirecting the user to the edit page of the new post
		 */
		wp_redirect( admin_url( 'post.php?action=edit&post=' . $new_post_id ) );
		exit;
	} else {
		wp_die( __( 'Error creating post, can"t find original post with ID: ', 'wpsl' ) . $post_id );
	}
}



/*
 * Add a link to duplicate post for post_row_actions
 */
add_filter( 'post_row_actions', 'wpsl_duplicate_product', 10, 2 );
add_filter( 'product_row_actions', 'wpsl_duplicate_product', 10, 2 );
function wpsl_duplicate_product( $actions, $post ) {
	//unset( $actions['edit'] );
	if ( current_user_can( 'edit_posts' ) && isset( get_current_screen()->parent_file ) && get_current_screen()->parent_file == 'edit.php?post_type=product' ) {
		$actions['duplicate'] = '<a href="admin.php?action=wpsl_duplicate_post_as_draft&amp;post=' . $post->ID . '" rel="permalink">' . __( 'Duplicate', 'wpsl' ) . '</a>';
	}
	return $actions;
}