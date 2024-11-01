<?php
/**
 * wpStore
 *
 * File for export products to xlsx format
 *
 * @since	2.5.0
 */


if ( ! defined( 'ABSPATH' ) ) exit;


if ( !is_admin() ) return;

	
/**
 * Export button output
 */
add_action( 'admin_footer', 'wpsl_export_users' );
function wpsl_export_users() {
	$screen = get_current_screen();
	if ( $screen->id != "users" )   // Only add to users.php page
		return;
	?>
	<script type="text/javascript">
		jQuery( document ).ready( function( $ ) {
			$( '.tablenav.top .clear:first, .tablenav.bottom .clear:first' ).before( '<form action="#" method="POST"><input type="hidden" id="export_users_csv" name="export_users_csv" value="1" /><input class="button button-primary user_export_button" style="margin-top:3px;" type="submit" value="<?php esc_attr_e( 'Export as CSV', 'wpsl' );?>" /></form><a href="<?php echo get_option( 'siteurl' ) . '/wp-admin/admin.php?page=wpsl-import'; ?>" class="page-title-action"><?php esc_attr_e( 'Import', 'wpsl' ); ?></a>' );
		} );
	</script>
	<?php
}



/**
 * Export all users to csv format
 *
 * @since	2.5.0
 */
add_action( 'admin_init', 'wpsl_export_users_csv' );
function wpsl_export_users_csv() {
	if ( !empty( $_POST['export_users_csv'] ) ) {
 
		if ( current_user_can( 'manage_options' ) ) {
			header( "Content-type: application/force-download" );
			header( 'Content-Disposition: inline; filename="users-' . date( 'd-m-Y' ) . '.csv"' );
 
			// WP_User_Query arguments
			$args = array ( 
				'order'   => 'ASC',
				'orderby' => 'display_name',
				'fields'  => 'all',
			 );
 
			// The User Query
			$blogusers = get_users( $args );
			// Array of WP_User objects.
			foreach ( $blogusers as $user ) {
				$meta = get_user_meta( $user->ID );
				$role = $user->roles;
				$email = $user->user_email;
 
				$first_name = ( isset( $meta['first_name'][0] ) && $meta['first_name'][0] != '' ) ? $meta['first_name'][0] : '' ;
				$last_name  = ( isset( $meta['last_name'][0] ) && $meta['last_name'][0] != '' ) ? $meta['last_name'][0] : '' ;
 
				echo '"' . $first_name . '","' . $last_name . '","' . $email . '","' . ucfirst( $role[0] ) . '"' . "\r\n";
			}
 
			exit();
		}
	}
}



/**
 * Export button output
 */
add_action( 'admin_footer', 'wpsl_export_products' );
function wpsl_export_products() {
	$screen = get_current_screen();
	if ( $screen->id != "edit-product" )
		return;
	?>
	<script type="text/javascript">
		jQuery( document ).ready( function( $ ) {
			$( '.wrap .page-title-action:first' ).after( '<form action="#" method="POST" style="display: inline-block;" ><input type="hidden" id="export_products_csv" name="export_products_csv" value="1" /><input class="page-title-action user_export_button" type="submit" value="<?php esc_attr_e( 'Export as CSV', 'wpsl' ); ?>" /></form><a href="<?php echo get_option( 'siteurl' ) . '/wp-admin/admin.php?page=wpsl-import'; ?>" class="page-title-action"><?php esc_attr_e( 'Import', 'wpsl' ); ?></a>' );
		} );
	</script>
	<?php
}



/**
 * wpStore
 *
 * Export all products to csv format
 *
 * @since	2.5.0
 */
add_action( 'admin_init', 'wpsl_export_products_csv' );
function wpsl_export_products_csv() {
	if ( !empty( $_POST['export_products_csv'] ) ) {
 
		if ( current_user_can( 'manage_options' ) ) {
			header( "Content-type: application/force-download" );
			header( 'Content-Disposition: inline; filename="products-' . date( 'd-m-Y' ) . '.csv"' );
 
			// WP_User_Query arguments
			$args = array ( 
				'post_type'      => 'product',
				'post_status'    => 'publish',
				'orderby'        => 'title',
				'order'          => 'DESC',
				'posts_per_page' => -1
			 );
 
			// The User Query
			$products = get_posts( $args );
			// Array of WP_User objects.
			if ( $products ) {
				foreach ( $products as $product ) {
					$id = $product->ID;
					echo '"' . $product->post_title . '","' . $product->post_excerpt . '","' . get_post_meta( $id, '_price', true ) . '","' . get_post_meta( $id, 'excerpt', true ) . '"' . "\r\n";
				}
			}
 
			exit();
		}
	}
}