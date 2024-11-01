<?php
/**
 * wpStore
 *
 * Various settings in the admin panel
 *
 * @since	1.5
 */


if ( ! defined( 'ABSPATH' ) ) exit;


if ( !is_admin() || !wp_doing_ajax() ) return;


/**
 * If the size of thumbnails is equal to 0, the plugin is not working correctly
 * Display a notification if the thumbnail size is less than 100 pixels
 */
function wpsl_plugin_error_notice() {
	echo '<div class="notice notice-error is-dismissible"><p>' . sprintf( __( 'For the plugin to work correctly, the recommended width and height of the thumbnail must be at least 100 pixels. %s', 'wpsl' ), '<a href="' . get_admin_url( null, 'options-media.php' ) . '" target="_blank">' . __( 'Go to options', 'wpsl' ) . '</a>' ) . '</p></div>';
}


/*
 * Change the size of the thumbnail in the window
 */
add_action( 'init', 'wpsl_image_sizes' );
function wpsl_image_sizes() {
	if ( get_option( 'thumbnail_size_w' ) < 100 && get_option( 'thumbnail_size_h' ) < 100 ) {
		add_action( 'admin_notices', 'wpsl_plugin_error_notice' );
	}
	add_image_size( 'wpsl-small-thumb', get_option( 'thumbnail_size_w', 150 ), get_option( 'thumbnail_size_h', 150 ), true );
	add_image_size( 'wpsl-medium-thumb', 400, 400, true );
	add_image_size( 'wpsl-big-thumb', 900, 900 );
}


/*
 * Disable prevent loading svg files
 */
add_filter( 'upload_mimes', 'wpsl_upload_svg_files' );
function wpsl_upload_svg_files( $mimes ) {
	if ( wpsl_opt( 'enable_svg' ) == '1' ) {
		$mimes['svg']  = 'image/svg+xml';
	}
	return $mimes;
}


/*
 * Display the contents of meta in the comments list in the admin
 */
add_filter( 'comment_text', 'wpsl_modify_extend_comment' );
function wpsl_modify_extend_comment( $text ) {
	// comment
	if ( get_comment_text( get_comment_ID() ) ) {
		$title = '<strong>' . esc_attr( __( 'Comment', 'wpsl' ) ) . '</strong><br/>';
		$text  =  $title . $text;
	}
	// minus
	if ( $txt = get_comment_meta( get_comment_ID(), 'minus', true ) ) {
		$title = '<strong>' . esc_attr( __( 'Disadvantages of product', 'wpsl' ) ) . '</strong><br/>';
		$text  =  $title . '<p>' . $txt . '</p>' . $text;
	}
	// plus
	if ( $txt = get_comment_meta( get_comment_ID(), 'plus', true ) ) {
		$title = '<strong>' . esc_attr( __( 'Advantages of product', 'wpsl' ) ) . '</strong><br/>';
		$text  =  $title . '<p>' . $txt . '</p>' . $text;
	}
	// assessment
	if ( $assessment = get_comment_meta( get_comment_ID(), 'assessment', true ) ) {
		$title = '<br><strong>' . __( 'Assessment', 'wpsl' ) . '</strong>';
		$text  = $title . '<p>' . wpsl_get_rating( $assessment ) . '</p>' . $text;
	}
	return $text;
}


/*
 * Add a new metabox to the comment editing page
 */
add_action( 'add_meta_boxes_comment', 'wpsl_extend_comment_add_meta_box' );
function wpsl_extend_comment_add_meta_box() {
	add_meta_box( 'title', __( 'Comment Metadata' ), 'wpsl_extend_comment_meta_box', 'comment', 'normal', 'high' );
}


/*
 * Show new fields
 */
function wpsl_extend_comment_meta_box( $comment ) {
	$minus  = get_comment_meta( $comment->comment_ID, 'minus', true );
	$plus  = get_comment_meta( $comment->comment_ID, 'plus', true );
	$assessment = get_comment_meta( $comment->comment_ID, 'assessment', true );
	wp_nonce_field( 'extend_comment_update', 'extend_comment_update', false );
	?>
    <p>
        <label for="minus"><?php _e( 'Disadvantages of product', 'wpsl' ); ?></label>
        <textarea type="text" name="minus" class="widefat"><?php echo esc_attr( $minus ); ?></textarea>
    </p>
    <p>
        <label for="plus"><?php _e( 'Advantages of product', 'wpsl' ); ?></label>
        <textarea type="text" name="plus" class="widefat"><?php echo esc_attr( $plus ); ?></textarea>
    </p>
    <p>
        <label for="assessment"><?php _e( 'Assessment', 'wpsl' ); ?></label>
        <span class="commentratingbox">
		<?php
		for ( $i = 1; $i <= 5; $i ++ ) {
			echo '
		  <span class="commentrating">
				<input type="radio" name="assessment" id="assessment" value="' . $i . '" ' . checked( $i, $assessment, 0 ) . '/>
		  </span>';
		}
		?>
		</span>
    </p>
	<?php
}


/*
 * Save fields
 */
add_action( 'edit_comment', 'wpsl_extend_comment_edit_meta_data' );
function wpsl_extend_comment_edit_meta_data( $comment_id ) {
	if ( ! isset( $_POST['extend_comment_update'] ) || ! wp_verify_nonce( $_POST['extend_comment_update'], 'extend_comment_update' ) ) {
		return;
	}
	if ( ! empty( $_POST['minus'] ) ) {
		$minus = sanitize_text_field( $_POST['minus'] );
		update_comment_meta( $comment_id, 'minus', $minus );
	} else {
		delete_comment_meta( $comment_id, 'minus' );
	}
	if ( ! empty( $_POST['plus'] ) ) {
		$plus = sanitize_text_field( $_POST['plus'] );
		update_comment_meta( $comment_id, 'plus', $plus );
	} else {
		delete_comment_meta( $comment_id, 'plus' );
	}
	if ( ! empty( $_POST['assessment'] ) ) {
		$assessment = intval( $_POST['assessment'] );
		update_comment_meta( $comment_id, 'assessment', $assessment );
	} else {
		delete_comment_meta( $comment_id, 'assessment' );
	}
}


/**
 * Prevent removal of built-in statuses
 * If the current taxonomy is a product category, we remove all attributes associated with it
 */
add_action( 'pre_delete_term', 'wpsl_do_not_remove_status', 1, 2 );
function wpsl_do_not_remove_status( $term_id, $taxonomy ) {
    
	// check rooles
	if( !current_user_can( 'manage_options' ) ) return;
	
	// remove attributes from deleted product category
	if ( $taxonomy == 'product_cat' ) {
		if ( $term_atts = wpsl_get_atts_by_term_id( $term_id ) ) {
			foreach( $term_atts as $term_attr ) {
				wpsl_remove_attr( (int)$term_attr->attribute_id );
			}
		}
	}
	
	// do not remove default statuses
	if ( $status = get_term_by( 'id', $term_id, 'wpsl_status' ) ) {
		if( in_array( $status->slug, array( 'new', 'in_process', 'completed', 'canceled', 'pending', 'paid', 'picking', 'shipped', 'delivered' ) ) ) {
			wp_die( 'This status, can not be delete', 'wpsl' );
		}
	}
}