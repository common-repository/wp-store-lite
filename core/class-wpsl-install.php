<?php
if ( ! defined( 'ABSPATH' ) ) exit;


if ( !is_admin() ) return;


/**
 * wpStore
 *
 * Create new roles, order statuses and pages in shop
 *
 * @since	2.2.0
 */

class WPSL_Install {
	
	static function wpsl_edit_roles() {
		
		if ( get_option( 'wpsl_was_active', false ) == true ) {
			return false;
		}
		
		$role = get_role( 'administrator' );
		
		// add the ability to edit orders by the administrator
		$role->add_cap( 'edit_order' );
		$role->add_cap( 'read_order' );
		$role->add_cap( 'delete_order' );
		$role->add_cap( 'delete_orders' );
		$role->add_cap( 'edit_orders' );
		$role->add_cap( 'edit_others_orders' );
		$role->add_cap( 'publish_orders' );
		$role->add_cap( 'read_private_orders' );
	}

	static function wpsl_add_roles() {
		
		if ( get_option( 'wpsl_was_active', false ) == true ) {
			return false;
		}
		
		add_role( 'wpsl_manager', __( 'Store manager', 'wpsl' ),
			array(
				'level_9'                => true,
				'level_8'                => true,
				'level_7'                => true,
				'level_6'                => true,
				'level_5'                => true,
				'level_4'                => true,
				'level_3'                => true,
				'level_2'                => true,
				'level_1'                => true,
				'level_0'                => true,
				'read'                   => true,
				'read_private_pages'     => true,
				'read_private_posts'     => true,
				'edit_users'             => false,
				'promote_users'          => false,
				'edit_posts'             => true,
				'edit_pages'             => true,
				'edit_published_posts'   => true,
				'edit_published_pages'   => true,
				'edit_private_pages'     => true,
				'edit_private_posts'     => true,
				'edit_others_posts'      => true,
				'edit_others_pages'      => true,
				'publish_posts'          => true,
				'publish_pages'          => true,
				'delete_posts'           => true,
				'delete_pages'           => true,
				'delete_private_pages'   => true,
				'delete_private_posts'   => true,
				'delete_published_pages' => true,
				'delete_published_posts' => true,
				'delete_others_posts'    => true,
				'delete_others_pages'    => true,
				'manage_categories'      => true,
				'manage_links'           => true,
				'moderate_comments'      => true,
				'upload_files'           => true,
				'export'                 => true,
				'import'                 => true,
				'list_users'             => true,
				'install_plugins'        => true,
				// add the ability to manage orders
				'edit_order'             => true,
				'read_order'             => true,
				'delete_order'           => true,
				'edit_orders'            => true,
				'edit_others_orders'     => true,
				'publish_orders'         => true,
				'read_private_orders'    => true,
			)
		);
		add_role( 'wpsl_consultant', __( 'Sales consultant', 'wpsl' ),
			array(
				'read'                   => true,
				'read_private_pages'     => true,
				'read_private_posts'     => true,
				'edit_posts'             => true,
				'edit_pages'             => true,
				'edit_published_posts'   => true,
				'edit_published_pages'   => true,
				'edit_private_pages'     => true,
				'edit_private_posts'     => true,
				'edit_others_posts'      => true,
				'edit_others_pages'      => true,
				'publish_posts'          => true,
				'publish_pages'          => true,
				'delete_posts'           => true,
				'delete_pages'           => true,
				'delete_private_pages'   => true,
				'delete_private_posts'   => true,
				'delete_published_pages' => true,
				'delete_published_posts' => true,
				'delete_others_posts'    => true,
				'delete_others_pages'    => true,
				'manage_categories'      => true,
				'manage_options'         => false,
				'manage_links'           => true,
				'moderate_comments'      => true,
				'upload_files'           => true,
				'export'                 => true,
				'import'                 => true,
				// add the ability to manage orders
				'edit_order'             => true,
				'read_order'             => true,
				'delete_order'           => true,
				'edit_orders'            => true,
				'edit_others_orders'     => true,
				'publish_orders'         => true,
				'read_private_orders'    => true,
				'edit_orders'            => true,
			)
		);
		add_role( 'wpsl_cashier', __( 'Seller cashier', 'wpsl' ),
			array(
				'level_3'                => true,
				'level_2'                => true,
				'level_1'                => true,
				'level_0'                => true,
				'edit_others_posts'      => true,
				'read'                   => true,
				'edit_order'             => true,
				'read_order'             => true,
				'delete_order'           => true,
				'edit_orders'            => true,
				'edit_others_orders'     => true,
				'publish_orders'         => true,
				'read_private_orders'    => true,
				'edit_orders'            => true,
			)
		);
	}
	
	/**
	 * wpStore
	 *
	 * Create new pages in shop, when activated plugin
	 *
	 * @since	2.4.0
	 */
	static function wpsl_add_pages() {
		
		if ( get_option( 'wpsl_was_active', false ) == true ) {
			return false;
		}
		
		global $wpdb;
		$cart       = $wpdb->get_row( "SELECT post_name FROM wp_posts WHERE post_name = 'cart'",            'ARRAY_A' );
		$order      = $wpdb->get_row( "SELECT post_name FROM wp_posts WHERE post_name = 'order'",           'ARRAY_A' );
		$payment    = $wpdb->get_row( "SELECT post_name FROM wp_posts WHERE post_name = 'payment'",         'ARRAY_A' );
		$account    = $wpdb->get_row( "SELECT post_name FROM wp_posts WHERE post_name = 'account'",         'ARRAY_A' );
		$storefront = $wpdb->get_row( "SELECT post_name FROM wp_posts WHERE post_name = 'storefront'",      'ARRAY_A' );
		$pickup     = $wpdb->get_row( "SELECT post_name FROM wp_posts WHERE post_name = 'in-store-pickup'", 'ARRAY_A' );

		include_once( WPSL_DIR . 'admin/wpsl-post-types-adding.php' );
		include_once( WPSL_DIR . 'core/wpsl-currency-functions.php' );
		
		$cur = wpsl_get_currency( 'locale', get_user_locale() );
		$default_opt = array(
			'currency'              => $cur['code'],
			'currency_symbol'       => $cur['symbol'],
			'currency_decimal_sep'  => ',',
			'currency_thousand_sep' => '',
			'currency_num_decimals' => 2,
			'currency_position'     => 'right_space',
			'tab_reviews_enable'    => true,
		);
		
		// Cart
		if ( !$cart ) {
			$post_data = array(
				'post_title'   => __( 'Cart', 'wpsl' ),
				'post_content' => '[wpsl-cart]',
				'post_status'  => 'publish',
				'post_author'  => 1,
				'post_name'    => 'cart',
				'post_type'    => 'page'
			);
			$post_id = wp_insert_post( $post_data );
			$default_opt['cart_page'] = $post_id;
		}
		// Order page
		if ( !$order ) {
			$post_data = array(
				'post_title'   => __( 'Checkout order', 'wpsl' ),
				'post_content' => '[wpsl-order]',
				'post_status'  => 'publish',
				'post_author'  => 1,
				'post_name'    => 'order',
				'post_type'    => 'page',
				'post_parent'  => $post_id
			);
			$post_id = wp_insert_post( $post_data );
			$default_opt['order_page'] = $post_id;
		}
		// Payment page
		if ( !$payment ) {
			$post_data = array(
				'post_title'   => __( 'Order payment', 'wpsl' ),
				'post_content' => '[wpsl-payment]',
				'post_status'  => 'publish',
				'post_author'  => 1,
				'post_name'    => 'payment',
				'post_type'    => 'page',
				'post_parent'  => $post_id
			);
			$post_id = wp_insert_post( $post_data );
			$default_opt['payment_page'] = $post_id;
		}
		// Account
		if ( !$account ) {
			$post_data = array(
				'post_title'   => __( 'Account', 'wpsl' ),
				'post_content' => '[wpsl-account]',
				'post_status'  => 'publish',
				'post_author'  => 1,
				'post_name'    => 'account',
				'post_type'    => 'page'
			);
			$post_id = wp_insert_post( $post_data );
			$default_opt['pageaccount'] = $post_id;
		}
		// Storefront
		if ( !$storefront ) {
			$post_data = array(
				'post_title'   => __( 'Storefront', 'wpsl' ),
				'post_content' => '[wpsl-storefront]',
				'post_status'  => 'publish',
				'post_author'  => 1,
				'post_name'    => 'storefront',
				'post_type'    => 'page'
			);
			$post_id = wp_insert_post( $post_data );
			$default_opt['storefront'] = $post_id;
		}
		// Now add shipping types
		if ( !$pickup ) {
			$post_data = array(
				'post_title'   => __( 'In-Store Pickup', 'wpsl' ),
				'post_content' => '',
				'post_status'  => 'publish',
				'post_author'  => 1,
				'post_name'    => 'in-store-pickup',
				'post_type'    => 'delivery'
			);
			$post_id = wp_insert_post( $post_data );
			if( !empty( $post_id ) ){
				update_post_meta( $post_id, 'delivery_price', '0' );
			}
			$default_opt['shipping_default'] = $post_id;
		}
		
		add_option( 'wpsl_option', $default_opt );
		
		
		// create simple product
		$post_data = array(
			'post_title'   => __( 'Simple product', 'wpsl' ),
			'post_content' => __( 'Product description', 'wpsl' ),
			'post_status'  => 'publish',
			'post_author'  => 1,
			'post_type'    => 'product'
		);
		$post_meta = array(
			'_sku'           => '0001',
			'_length'        => '10',
			'_width'         => '20',
			'_height'        => '30',
			'_weight'        => '100',
			'_regular_price' => '1000',
			'_price'         => '1000',
			'type-product'   => 'simple',
			'_stock_status'  => 'instock',
		);
		if ( $post_id = wp_insert_post( $post_data ) ) {
			foreach( $post_meta as $k => $v ) {
				update_post_meta( $post_id, $k, $v );
			}
		}	
	}

	/**
	 * Create new tables in database for attributes
	 *
	 * @since	2.7.0
	 */
	static function wpsl_add_table() {
		
		if ( get_option( 'wpsl_was_active', false ) == true ) {
			return false;
		}
		
		global $wpdb;
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		$table_name = $wpdb->get_blog_prefix() . 'wpsl_attributes';
		$charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset} COLLATE {$wpdb->collate}";

		$sql = "CREATE TABLE {$table_name} (
			attribute_id BIGINT UNSIGNED NOT NULL auto_increment,
			attribute_name varchar(200) NOT NULL,
			attribute_label varchar(200) NULL,
			attribute_value longtext NOT NULL DEFAULT '',
			attribute_measure varchar(20) NOT NULL,
			attribute_desc longtext NOT NULL DEFAULT '',
			attribute_type varchar(200) NOT NULL,
			attribute_term_id int(1) NOT NULL DEFAULT 0,
			attribute_filterable int(1) NOT NULL DEFAULT 1,
			attribute_variable int(1) NOT NULL DEFAULT 0,
			attribute_position int(1) NOT NULL DEFAULT 0,
			attribute_count int(1) NOT NULL DEFAULT 0,
			PRIMARY KEY  (attribute_id),
			KEY attribute_name (attribute_name(20))
		)
		{$charset_collate};";

		dbDelta( $sql );
		
		
		update_option( 'wpsl_was_active', true, false );
		update_option( 'wpsl_plugin_version', WPSL_VERSION, false );
		
	}
	
	
	/**
	 * wpStore
	 *
	 * After activate plugin, redirect to setting page
	 *
	 * @since	2.4.0
	 */
	static function redirect() {
		//exit( wp_redirect( admin_url( 'admin.php?page=wpsl_options' ) ) );
	}
	
}