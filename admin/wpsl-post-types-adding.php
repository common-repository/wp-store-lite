<?php
/**
 * wpStore
 *
 * Registers post types and taxonomies.
 *
 * @author	wpStore
 * @since	1.0
 */

 
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Create post types
 *
 * @author	wpStore
 * @since	1.0.0
 */
add_action( 'init', 'wpsl_create_post_types' );
function wpsl_create_post_types() {

	/**
	 * Create post type product
	 *
	 * @author	wpStore
	 * @since	1.0.0
	 */
	register_post_type( 'product', 
		apply_filters( 'wpsl_register_product_post_type',
			array(
				'labels'		  	 => array(
					'name'			     => __( 'Products',            'wpsl' ),
					'singular_name'      => __( 'Products',            'wpsl' ),
					'add_new'		     => __( 'Add product',         'wpsl' ),
					'add_new_item'	     => __( 'Add new product',     'wpsl' ),
					'edit_item'		     => __( 'Edit product',        'wpsl' ),
					'new_item'		     => __( 'New product',         'wpsl' ),
					'all_items'		     => __( 'All products',        'wpsl' ),
					'view_item'		     => __( 'Viewing product',     'wpsl' ),
					'search_items'	     => __( 'Search product',      'wpsl' ),
					'not_found'		     => __( 'Product not found',   'wpsl' ),
					'not_found_in_trash' => __( 'In trash no product', 'wpsl' ),
					'menu_name'		     => __( 'Products',            'wpsl' ),
				),
				'public'		     => true,
				'show_ui'		     => true,
				'has_archive'		 => true,
				'hierarchical'	     => false,
				'show_in_admin_bar'  => true,
				'menu_icon'		     => 'dashicons-cart',
				'menu_position'	     => 5,
				'supports'		     => array( 'title', 'editor', 'comments', 'thumbnail', 'custom-fields', 'comments' ),
				'taxonomies'		 => array( 'product_cat' ),
				'show_in_nav_menus'  => true,
				'show_in_rest'	     => true,
				'publicly_queryable' => true,
				'query_var'          => true,
				/* 'rewrite'            => false, */
			)
		)
	);
	/**
	 * Create post type product_variation
	 *
	 * @author	wpStore
	 * @since	2.0.0
	 */
	register_post_type( 'product_variation', 
		apply_filters( 'wpsl_register_product_variation_post_type',
			array(
				'label'			     => __( 'Variations', 'wpsl' ),
				'public'		     => false,
				'show_ui'		     => true,
				'show_in_menu'       => false,
				'has_archive'		 => true, 
				'publicly_queryable' => false,
				'hierarchical'	     => false,
				'show_in_admin_bar'  => false,
				'menu_icon'		     => 'dashicons-cart',
				'menu_position'	     => 6,
				'supports'		     => array( 'title', 'editor', 'comments', 'thumbnail', 'custom-fields', 'comments' ),
				'taxonomies'		 => array( 'product_cat' ),
			)
		)
	);
	
	
	/**
	 * Create post type delivery
	 *
	 * @author	wpStore
	 * @since	1.0.0
	 */
	register_post_type( 'delivery', 
		apply_filters( 'wpsl_register_delivery_post_type',
			array(
				'labels'		  	 => array(
					'name'                => __( 'Delivery types', 'wpsl' ),
					'singular_name'       => __( 'Delivery types', 'wpsl' ),
					'menu_name'           => __( 'Delivery types', 'wpsl' ),
					'parent_item_colon'   => __( 'Parent Delivery Type:', 'wpsl' ),
					'all_items'           => __( 'All Delivery Types', 'wpsl' ),
					'view_item'           => __( 'View Delivery Types', 'wpsl' ),
					'add_new_item'        => __( 'Add New Delivery Type', 'wpsl' ),
					'add_new'             => __( 'New Delivery Type', 'wpsl' ),
					'edit_item'           => __( 'Edit Delivery Type', 'wpsl' ),
					'update_item'         => __( 'Update Delivery Type', 'wpsl' ),
					'search_items'        => __( 'Search delivery type', 'wpsl' ),
					'not_found'           => __( 'No delivery types found', 'wpsl' ),
					'not_found_in_trash'  => __( 'No delivery types found in Trash', 'wpsl' ),
				),
				'label'               => __( 'Delivery', 'wpsl' ),
				'description'         => __( 'Delivery types', 'wpsl' ),
				'supports'            => array( 'title', 'editor', 'thumbnail', ),
				'hierarchical'        => false,
				'public'              => true,
				'show_ui'             => true,
				'show_in_menu'        => false,
				'show_in_nav_menus'   => false,
				'show_in_admin_bar'   => true,
				'menu_position'       => 50,
				'can_export'          => true,
				'has_archive'         => true,
				'exclude_from_search' => true,
				'publicly_queryable'  => false,
				'capability_type'     => 'page',
				//'rewrite'             => array( 'slug ' => 'delivery', 'with_front' => true, 'feeds' => false, 'pages' => false ),
			)
		)
	);
	
	
	/**
	 * Create post type orders
	 *
	 * @author	wpStore
	 * @since	1.0.0
	 */
	register_post_type( 'shop_order', 
		apply_filters( 'wpsl_register_orders_post_type',
			array(
				'labels'		  	 => array(
					'name'				  => __( 'Orders', 'wpsl' ),
					'singular_name'	      => __( 'Order', 'wpsl' ),
					'menu_name'		      => __( 'Orders', 'wpsl' ),
					'all_items'		      => __( 'All orders', 'wpsl' ),
					'view_item'		      => __( 'View orders', 'wpsl' ),
					'add_new_item'		  => __( 'Add new order', 'wpsl' ),
					'add_new'			  => __( 'New order', 'wpsl' ),
					'edit_item'		      => __( 'Edit order', 'wpsl' ),
					'update_item'		  => __( 'Update order', 'wpsl' ),
					'search_items'		  => __( 'Search order', 'wpsl' ),
					'not_found'		      => __( 'No orders found', 'wpsl' ),
					'not_found_in_trash'  => __( 'No orders found in trash', 'wpsl' ),
				),
				'label'			      => __( 'Orders', 'wpsl' ),
				'description'		  => __( 'Orders', 'wpsl' ),
				'supports'			  => array( 'title', 'author', 'custom-fields' ),
				'taxonomies'		  => array(),
				'hierarchical'		  => false,
				'public'			  => true,
				'show_ui'			  => true,
				'show_in_menu'		  => true,
				'show_in_nav_menus'   => false,
				'show_in_admin_bar'   => true,
				'menu_position'	      => 5,
				'menu_icon'		      => 'dashicons-megaphone',
				'can_export'		  => true,
				'has_archive'		  => false,
				'exclude_from_search' => true,
				'publicly_queryable'  => false,
				'rewrite'			  => true,   // используем ЧПУ для заказов
				'map_meta_cap'		  => false,
				'capability_type'	  => 'order',
				'capabilities'	      => array(
					'edit_post'          => 'edit_order',
					'read_post'          => 'read_order',
					'delete_post'        => 'delete_order',
					'delete_posts'       => 'delete_orders',
					'edit_posts'         => 'edit_orders',
					'edit_others_posts'  => 'edit_others_orders',
					'publish_posts'      => 'publish_orders',
					'read_private_posts' => 'read_private_orders',
					'create_posts'       => 'edit_orders'
				),
			)
		)
	);
	
	
	/**
	 * Create post type promocode
	 *
	 * @author	wpStore
	 * @since	1.0.0
	 */
	register_post_type( 'shop_coupon', 
		apply_filters( 'wpsl_register_promocode_post_type',
			array(
				'labels'		  	 => array(
					'name'                => __( 'Promocodes', 'wpsl' ),
					'singular_name'       => __( 'Promocode', 'wpsl' ),
					'menu_name'           => __( 'Promocode', 'wpsl' ),
					'parent_item_colon'   => __( 'Parent promocode', 'wpsl' ),
					'all_items'           => __( 'All promocodes', 'wpsl' ),
					'view_item'           => __( 'View promocodes', 'wpsl' ),
					'add_new_item'        => __( 'Add new promocode', 'wpsl' ),
					'add_new'             => __( 'New promocode', 'wpsl' ),
					'edit_item'           => __( 'Edit promocode', 'wpsl' ),
					'update_item'         => __( 'Update promocode', 'wpsl' ),
					'search_items'        => __( 'Search promocode', 'wpsl' ),
					'not_found'           => __( 'No promocode found', 'wpsl' ),
					'not_found_in_trash'  => __( 'No promocode found in trash', 'wpsl' ),
				),
				'label'               => __( 'Promocode', 'wpsl' ),
				'description'         => __( 'Promocodes', 'wpsl' ),
				'supports'            => array( 'title' ),
				'hierarchical'        => false,
				'public'              => true,
				'show_ui'             => true,
				'show_in_menu'        => false,
				'show_in_nav_menus'   => false,
				'show_in_admin_bar'   => true,
				'menu_position'       => 50,
				'can_export'          => true,
				'has_archive'         => true,
				'exclude_from_search' => true,
				'publicly_queryable'  => false,
				'capability_type'     => 'page',
			)
		)
	);
	
	
	/**
	 * Create post type support
	 *
	 * @author	wpStore
	 * @since	1.0.0
	 */
	if ( wpsl_opt( 'support' ) == '1' ) {
		register_post_type( 'support', 
			apply_filters( 'wpsl_register_support_post_type',
				array(
					'labels'		  	 => array(
						'name'                => __( 'Support tickets', 'wpsl' ),
						'singular_name'       => __( 'Support ticket', 'wpsl' ),
						'menu_name'           => __( 'Support ticket', 'wpsl' ),
						'parent_item_colon'   => __( 'Parent ticket', 'wpsl' ),
						'all_items'           => __( 'All support tickets', 'wpsl' ),
						'view_item'           => __( 'View support tickets', 'wpsl' ),
						'add_new_item'        => __( 'Add new support ticket', 'wpsl' ),
						'add_new'             => __( 'New support ticket', 'wpsl' ),
						'edit_item'           => __( 'Edit support ticket', 'wpsl' ),
						'update_item'         => __( 'Update support ticket', 'wpsl' ),
						'search_items'        => __( 'Search support ticket', 'wpsl' ),
						'not_found'           => __( 'Support ticket is not found', 'wpsl' ),
						'not_found_in_trash'  => __( 'Support ticket is not found in trash', 'wpsl' ),
					),
					'label'               => __( 'Support ticket', 'wpsl' ),
					'description'         => __( 'Support tickets', 'wpsl' ),
					'supports'            => array( 'title', 'editor', 'custom-fields', 'comments' ),
					'hierarchical'        => false,
					'public'              => true,
					'show_ui'             => true,
					'show_in_menu'        => false, // не показывать в подменю
					'show_in_nav_menus'   => false,
					'show_in_admin_bar'   => true,
					'menu_position'       => 50,
					'can_export'          => true,
					'has_archive'         => true,
					'exclude_from_search' => true, // исключаем из поиска по сайту
					'publicly_queryable'  => true,
					'capability_type'     => 'post',
				)
			)
		);
	}
	
	
	/**
	 * Create taxonomies product_cat
	 *
	 * @since	1.0.0
	 */
	register_taxonomy( 'product_cat', array( 'product' ),
		apply_filters( 'wpsl_register_product_cat_tax', 
			array(
				'labels'			 => array(
					'name'			     => __( 'Categories', 'wpsl' ),
					'singular_name'	     => __( 'Category', 'wpsl' ),
					'search_items'	     => __( 'Search category', 'wpsl' ),
					'all_items'		     => __( 'All categories', 'wpsl' ),
					'parent_item'	     => __( 'The parent category of the product', 'wpsl' ),
					'parent_item_colon'  => __( 'Category parent', 'wpsl' ),
					'edit_item'		     => __( 'Category parent', 'wpsl' ),
					'update_item'	     => __( 'Update the category', 'wpsl' ),
					'add_new_item'	     => __( 'Add new category', 'wpsl' ),
					'new_item_name'	     => __( 'The name of the new category of products', 'wpsl' ),
					'menu_name'		     => __( 'Categories', 'wpsl' ),
				),
				'hierarchical'	     => true,
				'show_ui'		     => true,
				'public'             => true,
				'query_var'		     => true,
				'rewrite'		     => array( 'slug' => 'product_cat' ),
				'show_admin_column'  => true
			)
		)
	);
	
	/**
	 * Create taxonomies product_tag
	 *
	 * @since	1.0.0
	 */
	register_taxonomy( 'product_tag', array( 'product' ),
		apply_filters( 'wpsl_register_product_tag_tax', 
			array(
				'labels'			 => array(
					'name'			     => __( 'Tags', 'wpsl' ),
					'singular_name'	     => __( 'Tag of product', 'wpsl' ),
					'search_items'	     => __( 'Search tag of product', 'wpsl' ),
					'all_items'		     => __( 'All tags of products', 'wpsl' ),
					'parent_item'	     => __( 'The parent tag of the product', 'wpsl' ),
					'parent_item_colon'  => __( 'Tag parent', 'wpsl' ),
					'edit_item'		     => __( 'Tag parent', 'wpsl' ),
					'update_item'	     => __( 'Update the tag', 'wpsl' ),
					'add_new_item'	     => __( 'Add new tag', 'wpsl' ),
					'new_item_name'	     => __( 'The name of the new tag of products', 'wpsl' ),
					'menu_name'		     => __( 'Tags', 'wpsl' ),
				 ),
				'hierarchical'	     => false,
				'show_ui'		     => true,
				'query_var'		     => true,
				'rewrite'		     => array( 'slug' => 'product_tag' ),
				'show_admin_column'  => true,
				'show_in_nav_menus'  => false,
			)
		)
	);
	
	/**
	 * Create taxonomies wpsl_status
	 *
	 * @since	1.0.0
	 */
	register_taxonomy( 'wpsl_status', array( 'shop_order' ),
		apply_filters( 'wpsl_register_wpsl_status_tax', 
			array(
				'labels'			 => array(
					'name'					     => __( 'Statuses', 'wpsl' ),
					'singular_name'			     => __( 'Status', 'wpsl' ),
					'menu_name'				     => __( 'Statuses', 'wpsl' ),
					'all_items'				     => __( 'All Statuses', 'wpsl' ),
					'parent_item'		         => __( 'Parent Status', 'wpsl' ),
					'parent_item_colon'		     => __( 'Parent Status:', 'wpsl' ),
					'new_item_name'			     => __( 'New Status Name', 'wpsl' ),
					'add_new_item'			     => __( 'Add New Status', 'wpsl' ),
					'edit_item'				     => __( 'Edit Status', 'wpsl' ),
					'update_item'				 => __( 'Update Status', 'wpsl' ),
					'separate_items_with_commas' => __( 'Separate statuses with commas', 'wpsl' ),
					'search_items'			     => __( 'Search statuses', 'wpsl' ),
					'add_or_remove_items'		 => __( 'Add or remove statuses', 'wpsl' ),
					'choose_from_most_used'	     => __( 'Choose from the most used statuses', 'wpsl' ),
				),
				'hierarchical'			     => false,
				'public'					 => false,
				'show_ui'					 => true,
				'show_admin_column'		     => true,
				'show_in_nav_menus'		     => false,
				'show_tagcloud'			     => false,
				'rewrite'				     => false,
			)
		)
	);
	
	if ( is_admin() ) {
		$defaultStatuses = array(
			'new'		=> array(
				'title'         => __( 'Approved', 'wpsl' ),
				'description'   => __( 'The order is received and is waiting for processing by the moderator', 'wpsl' ),
				'status_type'   => 'order'
			),
			'in_process' => array(
				'title'         => __( 'Processing', 'wpsl' ),
				'description'   => __( 'Your order is checked by the moderator and transferred for a complete set', 'wpsl' ),
				'status_type'   => 'order'
			),
			'completed'	 => array(
				'title'         => __( 'Completed', 'wpsl' ),
				'description'   => __( 'Your order is completed. Thank you for your purchase! Waiting for you again!', 'wpsl' ),
				'status_type'   => 'order'
			),
			'canceled'	 => array(
				'title'         => __( 'Cancelled', 'wpsl' ),
				'description'   => __( 'The order was either canceled by you or the order was not purchased within the specified period', 'wpsl' ),
				'status_type'   => 'order'
			),
			
			'pending'	 => array(
				'title'         => __( 'Not paid', 'wpsl' ),
				'description'   => __( 'We expect payment of products', 'wpsl' ),
				'status_type'   => 'payment'
			),
			'paid'	 => array(
				'title'         => __( 'Pend', 'wpsl' ),
				'description'   => __( 'The money went to the account of seller', 'wpsl' ),
				'status_type'   => 'payment'
			),
			
			'picking' => array(
				'title'         => __( 'Order picking', 'wpsl' ),
				'description'   => __( 'At this stage, we reserve the products for your order and prepare it for shipment', 'wpsl' ),
				'status_type'   => 'delivery'
			),
			'shipped'	 => array(
				'title'         => __( 'Order shipped', 'wpsl' ),
				'description'   => __( 'The products in your order have been handed over to the courier, shipping company or postal service', 'wpsl' ),
				'status_type'   => 'delivery'
			),
			'delivered'	 => array(
				'title'         => __( 'Delivered', 'wpsl' ),
				'description'   => __( 'The products are delivered to the point of receipt of the order', 'wpsl' ),
				'status_type'   => 'delivery'
			),
		);
		foreach ( $defaultStatuses as $slug => $status ) {
			if ( !term_exists( $slug, 'wpsl_status' ) ) {
				$term = wp_insert_term(
					$status['title'],	// the term 
					'wpsl_status',		// the taxonomy
					array(
						'description'	=> $status['description'],
						'slug'			=> $slug
					)
				);
				update_term_meta( $term['term_id'], 'status_type', $status['status_type'] );
			}
		}		
	}
}