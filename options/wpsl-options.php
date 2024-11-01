<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( !is_admin() ) return;

/**
 * Function to add options
 */
add_action( 'admin_menu', 'wpsl_create_options', 1 );
function wpsl_create_options() {
	
	if ( !current_user_can( 'manage_options' ) ) return;
	
	// pages
	$pages_array = array();
	$pages = get_posts(
		array(
			'post_type'   => array( 'page' ),
			'numberposts' => -1,
			'post_status' => 'publish',
		)
	);
	if ( $pages ) {
		foreach( $pages as $page ) {
			$pages_array[$page->ID] = $page->post_title;
		}
	}
	
	//delivery
	$delivery_array = array();
	$delivery = new WPSL_Shipping();
	if ( $deliverys = $delivery->get_delivery() ) {
		foreach( $deliverys as $delivery ) {
			$delivery_array[$delivery->ID] = $delivery->post_title;
		}
	}
	
	// currency
	$cur = array();
	$currency = wpsl_get_currency();
	foreach( $currency as $code => $data ) {
		$cur[$code] = $data['name'] . ' (' . $data['symbol'] . ')';
	}
	
	require( plugin_dir_path( __FILE__ ) . 'class-wpsl-options.php' );
	
	$order = new WPSL_Order();
	
	$sections = array();
	$sections[] = apply_filters( 'wpsl_base_options', array(
		'icon'       => 'icon-home',
		'title'      => __( 'Store', 'wpsl' ),
		'desc'       => __( 'Base information', 'wpsl' ),
		'groups' => array(
			array(
				array(
					'id'    => 'store_address',
					'type'  => 'text',
					'title' => __( 'Postal address', 'wpsl' ),
					'help'  => __( 'You can leave this field blank', 'wpsl' ),
					'desc'  => __( 'Store address offline', 'wpsl' ),
				),
				array(
					'id'    => 'useful',
					'type'  => current_theme_supports( 'wpstore' ) ? 'custom' : 'empty',
					'title' => __( 'This can be useful', 'wpsl' ),
					'help'  => '',
					'desc'  => sprintf( __( 'Your theme does not support all plugin features. Try the %s theme from the developer or learn %s the plugin into the theme.', 'wpsl' ), '<a href="https://codyshop.ru" target="_blank">StoreBox</a>', '<a href="https://codyshop.ru/?p=1611" target="_blank">' . __( 'how to integrate', 'wpsl' ) . '</a>' ),
					'html'  => '',
				),
				array(
					'id'    => 'store_contactform',
					'type'  => 'checkbox',
					'title' => __( 'Contact form', 'wpsl' ),
					'help'  => '',
					'desc'  => __( 'The output via the shortcode [wpsl-contactform]', 'wpsl' ),
					'std'   => '0'
				),
			),
			array(
				array(
					'id'    => 'currency',
					'type'  => 'select',
					'title' => __( 'Currency', 'wpsl' ),
					'help'  => __( 'Base currency of store', 'wpsl' ),
					'desc'  => '',
					'opts'  => $cur,
					'std'   => wpsl_opt( 'currency' ),
				),
				array(
					'id'    => 'currency_symbol',
					'type'  => 'text',
					'title' => __( 'Currency symbol', 'wpsl' ),
					'help'  => __( 'Replaces the default currency symbol', 'wpsl' ),
					'desc'  => '',
					'std'   => '',
				),
				array(
					'id'    => 'currency_position',
					'type'  => 'select',
					'title' => __( 'Position', 'wpsl' ),
					'help'  => __( 'This controls the position of the currency symbol', 'wpsl' ),
					'desc'  => '',
					'opts'  => array(
						'left'        => __( 'Left', 'wpsl' ),
						'right'       => __( 'Right', 'wpsl' ),
						'left_space'  => __( 'Left with space', 'wpsl' ),
						'right_space' => __( 'Right with space', 'wpsl' ),
					),
					'std'   => 'right_space'
				),
				array(
					'id'    => 'currency_thousand_sep',
					'type'  => 'text',
					'title' => __( 'Thousand separator', 'wpsl' ),
					'help'  => __( 'This sets the thousand separator of displayed prices', 'wpsl' ),
					'desc'  => '',
					'std'   => '',
				),
				array(
					'id'    => 'currency_decimal_sep',
					'type'  => 'text',
					'title' => __( 'Decimal separator', 'wpsl' ),
					'help'  => __( 'This sets the decimal separator of displayed prices', 'wpsl' ),
					'desc'  => '',
					'std'   => ',',
				),
				array(
					'id'    => 'currency_num_decimals',
					'type'  => 'number',
					'title' => __( 'Number of decimals', 'wpsl' ),
					'help'  => __( 'This sets the number of decimal points shown in displayed prices', 'wpsl' ),
					'desc'  => '',
					'std'   => '2',
				),
			),
			array(
				array(
					'id'    => 'pageaccount',
					'type'  => 'select',
					'title' => __( 'Account page', 'wpsl' ),
					'help'  => __( 'To display the order form, use the shortcode', 'wpsl' ) . ' [wpsl-account]',
					'desc'  => '',
					'opts'  => $pages_array,
					'std'   => wpsl_opt( 'pageaccount' ),
				),
			),
			array(
				array(
					'id'    => 'support',
					'type'  => 'checkbox',
					'title' => __( 'Support', 'wpsl' ),
					'help'  => '',
					'desc'  => __( 'Customers will be able to create tickets in personal account', 'wpsl' ),
					'std'   => '0',
					'rel'   => 'parent'
				),
				array(
					'id'    => 'schedule',
					'type'  => 'text',
					'title' => __( 'Work schedule', 'wpsl' ),
					'help'  => '',
					'desc'  => __( 'Work schedule of technical support', 'wpsl' ),
					'rel'   => 'child'
				),
				array(
					'id'    => 'phone',
					'type'  => 'text',
					'title' => __( 'Phone', 'wpsl' ),
					'help'  => __( 'Phone number on which customers can solve issues with the site', 'wpsl' ),
					'desc'  => '',
					'rel'   => 'child'
				),
				array(
					'id'    => 'operator_email',
					'type'  => 'email',
					'title' => __( 'Email', 'wpsl' ),
					'help'  => __( 'Assign an email through which technical support will be provided', 'wpsl' ),
					'desc'  => '',
					'std'   => get_option( 'admin_email' ),
					'rel'   => 'child'
				),
				array(
					'id'    => 'taboo',
					'type'  => 'checkbox',
					'title' => '',
					'help'  => __( 'Prevent the creation of a free products and orders', 'wpsl' ),
					'desc'  => '',
					'std'   => '0',
					'rel'   => 'child'
				),
				array(
					'id'    => 'ticket_system',
					'type'  => 'checkbox',
					'title' => '',
					'help'  => __( 'Technical support is provided only through the internal ticket system in perconal account', 'wpsl' ),
					'desc'  => '',
					'std'   => '0',
					'rel'   => 'child'
				),
			),
		)
	) );
	
	$sections[] = apply_filters( 'wpsl_main_options', array(
		'icon'       => 'icon-settings',
		'title'      => __( 'Settings', 'wpsl' ),
		'desc'       => __( 'Store settings', 'wpsl' ),
		'groups' => array(
			array(
				array(
					'id'    => 'perfomance',
					'type'  => 'custom',
					'title' => __( 'Performance', 'wpsl' ),
				),
				array(
					'id'    => 'emoji',
					'type'  => 'checkbox',
					'title' => __( 'Disable emoji', 'wpsl' ),
					'help'  => __( 'Option to disable emoticons and reduce the count of http requests', 'wpsl' ),
					'desc'  => '',
					'std'   => '0'
				),
				array(
					'id'    => 'combinejs',
					'type'  => 'checkbox',
					'title' => __( 'Combine js', 'wpsl' ),
					'help'  => __( 'Merges all js files into one. After activation, ensure non-conflict with other plugins', 'wpsl' ),
					'desc'  => '',
					'std'   => '0'
				),
				array(
					'id'    => 'enable_svg',
					'type'  => 'checkbox',
					'title' => __( 'Allow svg', 'wpsl' ),
					'help'  => __( 'After activation refresh the options page', 'wpsl' ),
					'desc'  => '',
					'std'   => '0'
				),
			),
			array(
				array(
					'id'    => 'mobile_menu',
					'type'  => 'checkbox',
					'title' => __( 'Mobile menu', 'wpsl' ),
					'help'  => '',
					'desc'  => __( 'Enables menus on mobile devices', 'wpsl' ),
					'std'   => '0',
					'rel'   => 'parent'
				),
				array(
					'id'    => 'mobile',
					'type'  => 'select',
					'title' => '',
					'help'  => __( 'Select which parameters users will be able to sort products', 'wpsl' ),
					'opts'  => array(
						'phone'    => __( 'Call back', 'wpsl' ),
						'account'  => __( 'Account', 'wpsl' ),
						'search'   => __( 'Search', 'wpsl' ),
						'filter'   => __( 'Filter', 'wpsl' ),
						'cart'     => __( 'Cart', 'wpsl' ),
					),
					'multi' => true,
					'rel'   => 'child'
				),
			),
			array(
				array(
					'id'    => 'law',
					'type'  => 'custom',
					'title' => __( 'Law', 'wpsl' ),
				),
				array(
					'id'    => 'policy',
					'type'  => 'checkbox',
					'title' => 'GDPR',
					'help'  => '',
					'desc'  => __( 'Personal data processing policy', 'wpsl' ),
					'std'   => '0',
					'rel'   => 'parent'
				),
				array(
					'id'    => 'policy_page',
					'type'  => 'select',
					'title' => '',
					'help'  => __( 'Enter the page which contains information about the policy the processing of personal data', 'wpsl' ),
					'desc'  => '',
					'opts'  => $pages_array,
					'std'   => '',
					'rel'   => 'child'
				),
				array(
					'id'    => 'policy_text',
					'type'  => 'textarea',
					'title' => '',
					'help'  => __( 'Text will be displayed in order form', 'wpsl' ),
					'desc'  => '',
					'std'   => __( 'Attention! In accordance with the law ... you must read and accept the personal data processing agreement', '' ),
					'rel'   => 'child'
				),
			),
		)
	) );

	$sections[] = apply_filters( 'wpsl_storefont_options', array(
		'icon'   => 'icon-grid',
		'title'  => __( 'Storefront', 'wpsl' ),
		'desc'   => __( 'List of products', 'wpsl' ),
		'groups' => array(
			array(
				array(
					'id'    => 'storefront',
					'type'  => 'select',
					'title' => __( 'Page of showcase', 'wpsl' ),
					'help'  => '',
					'desc'  => '',
					'opts'  => $pages_array,
					'std'   => wpsl_opt( 'storefront' ),
				),
				array(
					'id'    => 'productcount',
					'type'  => 'number',
					'title' => __( 'Products in the row', 'wpsl' ),
					'help'  => __( 'This must be numeric', 'wpsl' ),
					'desc'  => __( 'How many products should be shown on the line?', 'wpsl' ),
					'std'   => '4',
				),
				array(
					'id'    => 'productrows',
					'type'  => 'number',
					'title' => __( 'Number of rows', 'wpsl' ),
					'help'  => __( 'This must be numeric', 'wpsl' ),
					'desc'  => __( 'How many product lines should be shown on the page?', 'wpsl' ),
					'std'   => '4',
				),
			),
			array(
				array(
					'id'    => 'sorting_enable',
					'type'  => 'checkbox',
					'title' => __( 'Sorting', 'wpsl' ),
					'help'  => '',
					'desc'  => __( 'Show the sorting of products above the storefront', 'wpsl' ),
					'std'   => '0',
					'rel'   => 'parent'
				),
				array(
					'id'    => 'sorting_fields',
					'type'  => 'select',
					'title' => '',
					'help'  => __( 'Sort fields', 'wpsl' ),
					'opts'  => array(
						'_price'         => __( 'By price', 'wpsl' ),
						'_product_views' => __( 'Popularity', 'wpsl' ),
						'title'          => __( 'By name', 'wpsl' ),
						'date'           => __( 'By date', 'wpsl' ),
					),
					'multi' => true,
					'rel'   => 'child'
				),
			),
			array(
				array(
					'id'    => 'products',
					'type'  => 'custom',
					'title' => __( 'Products', 'wpsl' ),
				),
				array(
					'id'    => 'placeholder_image',
					'type'  => 'upload',
					'title' => __( 'Placeholder image', 'wpsl' ),
					'help'  => '',
					'desc'  => __( 'Will be used if the item has no picture', 'wpsl' ),
					'std'   => WPSL_URL . '/assets/img/no-photo.png',
					'rel'   => ''
				),
			),
		)
	) );

	
	// get tabs
	$tabs = array();
	$tabs[] = array(
				'id'    => 'tabs',
				'type'  => 'custom',
				'title' => __( 'Tabs', 'wpsl' ),
			);
	$tabs[] = array(
				'id'    => 'atts_to_tab',
				'type'  => 'checkbox',
				'title' => __( 'Attributes', 'wpsl' ),
				'help'  => '',
				'desc'  => __( 'Transfer attributes of a product to tab', 'wpsl' ),
				'std'   => '0'
			);
	$tabs[] = array(
				'id'    => 'tab_count',
				'type'  => 'select',
				'title' => __( 'Сount of tabs', 'wpsl' ),
				'help'  => '',
				'desc'  => __( 'Save options and refresh the page', 'wpsl' ),
				'std'   => '1',
				'opts'  => array(
					'0' => '0',
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
					'7' => '7',
				)
			);
	$tabs[] = array(
				'id'    => 'tab_reviews_enable',
				'type'  => 'checkbox',
				'title' => __( 'Reviews', 'wpsl' ),
				'help'  => '',
				'desc'  => '',
				'std'   => '1',
				'rel'   => 'parent'
			);
	$tabs[] = array(
				'id'    => 'tab_reviews_title',
				'type'  => 'text',
				'title' => '',
				'help'  => '',
				'desc'  => __( 'Name of the tab', 'wpsl' ),
				'std'   => __( 'Reviews', 'wpsl' ),
				'rel'   => 'child'
			);
	$tabs[] = array(
				'id'    => 'tab_reviews_icon',
				'type'  => 'text',
				'title' => '',
				'help'  => '',
				'desc'  => __( 'Icon of the tab', 'wpsl' ),
				'std'   => 'icon-star',
				'rel'   => 'child'
			);
	$tabs[] = array(
				'id'    => 'tab_reviews_desc',
				'type'  => 'editor',
				'title' => '',
				'help'  => '',
				'desc'  => __( 'Rules for reviews', 'wpsl' ),
				'std'   => '<strong>В отзывах запрещено:</strong><br>Использовать нецензурные выражения, оскорбления и угрозы<br>Публиковать адреса, телефоны и ссылки, содержащие прямую рекламу<br>Писать отвлеченные от темы и бессмысленные комментарии',
				'rel'   => 'child'
			);
	if ( $count = wpsl_opt( 'tab_count' ) ) {
		for ( $i=0; $i<$count; $i++ ) {
			$title = wpsl_opt( 'tab_title_' . $i ) != '' ? wpsl_opt( 'tab_title_' . $i ) : __( 'Tab', 'wpsl' );
			$tabs[] = array(
					'id'    => 'tab_enable_' . $i,
					'type'  => 'checkbox',
					'title' => $title,
					'help'  => '',
					'desc'  => '',
					'std'   => '0',
					'rel'   => 'parent'
				);
			$tabs[] = array(
					'id'    => 'tab_title_' . $i,
					'type'  => 'text',
					'title' => '',
					'help'  => '',
					'desc'  => __( 'Name of the tab', 'wpsl' ),
					'std'   => __( 'Tab', 'wpsl' ),
					'rel'   => 'child'
				);
			$tabs[] = array(
					'id'    => 'tab_icon_' . $i,
					'type'  => 'text',
					'title' => '',
					'help'  => '',
					'desc'  => __( 'Icon of the tab', 'wpsl' ),
					'std'   => 'icon-gift',
					'rel'   => 'child'
				);
			$tabs[] = array(
					'id'    => 'tab_desc_' . $i,
					'type'  => 'editor',
					'title' => '',
					'help'  => '',
					'desc'  => __( 'Fill of the tab', 'wpsl' ),
					'std'   => '',
					'rel'   => 'child'
				);
		}
	}
	$sections[] = apply_filters( 'wpsl_product_card_options', array(
		'icon'   => 'icon-package',
		'title'  => __( 'Product card', 'wpsl' ),
		'desc'   => __( 'Single product', 'wpsl' ),
		'groups' => array(
			array(
				array(
					'id'    => 'measurements',
					'type'  => 'custom',
					'title' => __( 'Measurements', 'wpsl' ),
				),
				array(
					'id'    => 'weight_unit',
					'type'  => 'select',
					'title' => __( 'Weight unit', 'wpsl' ),
					'help'  => '',
					'desc'  => __( 'This controls what unit you will define weights in', 'wpsl' ),
					'std'   => 'kg',
					'opts'  => wpsl_weight_unit(),
				),
				array(
					'id'    => 'dimension_unit',
					'type'  => 'select',
					'title' => __( 'Dimensions unit', 'wpsl' ),
					'desc'  => __( 'This controls what unit you will define lengths in', 'wpsl' ),
					'help'  => '',
					'std'   => 'mm',
					'opts'  => wpsl_dimensions_unit()
				),
			),
			$tabs,
			array(
				array(
					'id'    => 'badges',
					'type'  => 'custom',
					'title' => __( 'Badges', 'wpsl' ),
				),
				array(
					'id'    => 'new_icon',
					'type'  => 'upload',
					'title' => __( 'Icon "New"', 'wpsl' ),
					'help'  => '',
					'desc'  => '',
					'std'   => WPSL_URL . '/assets/img/new.svg',
					'rel'   => ''
				),
				array(
					'id'    => 'newproduct',
					'type'  => 'number',
					'title' => '',
					'help'  => __( 'The number of days from the date the product was published to be considered new. If you do not want to display the icon, leave the field blank', 'wpsl' ),
					'desc'  => '',
					'std'   => '',
				),
				array(
					'id'    => 'discount_icon',
					'type'  => 'upload',
					'title' => __( 'Icon "Discount"', 'wpsl' ),
					'help'  => __( 'Set manually on the edit page of an individual product', 'wpsl' ),
					'desc'  => '',
					'std'   => WPSL_URL . '/assets/img/discount.svg',
					'rel'   => ''
				),
				array(
					'id'    => 'best_icon',
					'type'  => 'upload',
					'title' => __( 'Icon "Best seller"', 'wpsl' ),
					'help'  => __( 'Set manually on the edit page of an individual product', 'wpsl' ),
					'desc'  => '',
					'std'   => WPSL_URL . '/assets/img/bestseller.svg',
					'rel'   => ''
				),
			),
			array(
				array(
					'id'    => 'similarproduct',
					'type'  => 'checkbox',
					'title' => __( 'Similar products', 'wpsl' ),
					'help'  => '',
					'desc'  => __( 'Output similar products', 'wpsl' ),
					'std'   => '0'
				),
				array(
					'id'    => 'similar_product_count',
					'type'  => 'number',
					'title' => '',
					'help'  => __( 'Number of similar products', 'wpsl' ),
					'desc'  => '',
					'std'   => '8',
					'rel'   => 'child'
				),
			),
			array(
				array(
					'id'    => 'variation_show_min_price',
					'type'  => 'checkbox',
					'title' => __( 'Variable products', 'wpsl' ),
					'help'  => __( 'Display only minimal price', 'wpsl' ),
					'desc'  => '',
					'std'   => '0'
				),
			),
		)
	) );


	$sections[] = apply_filters( 'wpsl_before_options_cart', array(
		'icon'   => 'icon-shopping-cart',
		'title'  => __( 'Cart', 'wpsl' ),
		'desc'   => __( 'Cart page', 'wpsl' ),
		'groups' => array(
			array(
				array(
					'id'    => 'cart_page',
					'type'  => 'select',
					'title' => __( 'Cart page', 'wpsl' ),
					'help'  => '',
					'desc'  => __( 'To display the order form, use the shortcode', 'wpsl' ) . ' [wpsl-cart]',
					'opts'  => $pages_array,
					'std'   => wpsl_opt( 'cart_page' ),
				),
				array(
					'id'    => 'cart_minimum',
					'type'  => 'number',
					'title' => __( 'Minimum order', 'wpsl' ),
					'help'  => '',
					'desc'  => __( 'If the cart of products less than the specified amount, order will be impossible', 'wpsl' ),
					'std'   => '',
				),
				array(
					'id'    => 'cart_single',
					'type'  => 'checkbox',
					'title' => __( 'Single-piece product', 'wpsl' ),
					'help'  => __( 'The buyer will not be able to order more than one unit of each item', 'wpsl' ),
					'desc'  => __( 'Activate if you sell the piece', 'wpsl' ),
					'std'   => '0'
				),
			),
			array(
				array(
					'id'    => 'order',
					'type'  => 'custom',
					'title' => __( 'Order page', 'wpsl' ),
				),
				array(
					'id'    => 'order_page',
					'type'  => 'select',
					'title' => __( 'Order page', 'wpsl' ),
					'help'  => '',
					'desc'  => __( 'To display the order form, use the shortcode', 'wpsl' ) . ' [wpsl-order]',
					'opts'  => $pages_array,
					'std'   => wpsl_opt( 'order_page' ),
				),
				array(
					'id'    => 'numeration',
					'type'  => 'select',
					'title' => __( 'Order numeration', 'wpsl' ),
					'help'  => __( 'Generates the names of the orders. When changing, does not change the numbering of already created orders', 'wpsl' ),
					'desc'  => '',
					'std'   => 'active',
					'opts'  => array(
						'default' => sprintf( __( 'Default. %s: %s', 'wpsl' ), __( 'Example', 'wpsl' ), '20190820-1949' ),
						'start'   => sprintf( __( 'Numbering from a certain number. %s: %s', 'wpsl' ), __( 'Example', 'wpsl' ), '1000' ),
						'user'    => sprintf( __( 'User ID and order number. %s: %s', 'wpsl' ), __( 'Example', 'wpsl' ), '54_2' ),
						'random'  => sprintf( __( 'Random unique number. %s: %s', 'wpsl' ), __( 'Example', 'wpsl' ), '67TJ34OP' ),
						'day'     => sprintf( __( 'Numbering within a day. %s: %s', 'wpsl' ), __( 'Example', 'wpsl' ), '15012020/2' ),
						'month'   => sprintf( __( 'Numbering within a month. %s: %s', 'wpsl' ), __( 'Example', 'wpsl' ), '012020/15' ),
						'year'    => sprintf( __( 'Numbering within a year. %s: %s', 'wpsl' ), __( 'Example', 'wpsl' ), '2020/239' ),
					)
				),
				array(
					'id'    => 'fields',
					'type'  => 'custom',
					'title' => __( 'Fields of the order', 'wpsl' ),
					'help'  => '',
					'desc'  => '',
					'html'  => '',
				),
				array(
					'id'    => $order::USER_NAME,
					'type'  => 'select',
					'title' => __( 'Name', 'wpsl' ),
					'help'  => __( 'Select the status field', 'wpsl' ),
					'desc'  => '',
					'std'   => 'active',
					'opts'  => array(
						'noactive' => __( 'Not active', 'wpsl' ),
						'active'   => __( 'Active', 'wpsl' ),
						'required' => __( 'Required', 'wpsl' ),
					)
				),
				array(
					'id'    => $order::USER_PHONE,
					'type'  => 'select',
					'title' => __( 'Phone', 'wpsl' ),
					'help'  => __( 'Select the status field', 'wpsl' ),
					'desc'  => '',
					'std'   => 'noactive',
					'opts'  => array(
						'noactive' => __( 'Not active', 'wpsl' ),
						'active'   => __( 'Active', 'wpsl' ),
						'required' => __( 'Required', 'wpsl' ),
					)
				),
				array(
					'id'    => $order::USER_COMMENT,
					'type'  => 'select',
					'title' => __( 'Additionaly', 'wpsl' ),
					'help'  => __( 'Select the status field', 'wpsl' ),
					'desc'  => '',
					'std'   => 'active',
					'opts'  => array(
						'noactive' => __( 'Not active', 'wpsl' ),
						'active'   => __( 'Active', 'wpsl' ),
						'required' => __( 'Required', 'wpsl' ),
					)
				),
			),
			array(
				array(
					'id'    => 'coupons',
					'type'  => 'custom',
					'title' => __( 'Coupons', 'wpsl' ),
				),
				array(
					'id'    => 'cart_coupons',
					'type'  => 'checkbox',
					'title' => __( 'Enable', 'wpsl' ),
					'help'  => '',
					'desc'  => __( 'Enable coupon support in cart', 'wpsl' ),
					'std'   => '0'
				),
			),
		)
	) );
	

	$sections[] = apply_filters( 'wpsl_before_shipping', array(
		'icon'   => 'icon-truck',
		'title'  => __( 'Shipping', 'wpsl' ),
		'desc'   => __( 'Shipping settings', 'wpsl' ),
		'groups' => array(
			array(
				array(
					'id'    => 'shipping',
					'type'  => 'custom',
					'title' => __( 'Shipping', 'wpsl' ),
				),
				array(
					'id'    => 'shipping',
					'type'  => 'checkbox',
					'title' => __( 'Enable shipping', 'wpsl' ),
					'help'  => '',
					'desc'  => __( 'After changing the setting, refresh the page', 'wpsl' ),
					'std'   => '0',
					'rel'   => 'parent'
				),
				array(
					'id'    => 'shipping_default',
					'type'  => 'select',
					'title' => __( 'Shipping type', 'wpsl' ),
					'help'  => '',
					'desc'  => __( 'Default shipping type', 'wpsl' ),
					'opts'  => $delivery_array,
					'rel'   => 'child',
					'std'   => wpsl_opt( 'shipping_default' )
				),
			),
		)
	) );
	

	$sections[] = apply_filters( 'wpsl_before_email', array(
		'icon'   => 'icon-mail',
		'title'  => __( 'Email', 'wpsl' ),
		'desc'   => __( 'Sending email', 'wpsl' ),
		'groups' => array(
			array(
				array(
					'id'    => 'sender',
					'type'  => 'custom',
					'title' => __( 'Sender', 'wpsl' ),
				),
				array(
					'id'    => 'email_from_name',
					'type'  => 'text',
					'title' => __( '"From" name', 'wpsl' ),
					'desc'  => __( 'How the sender name is displayed', 'wpsl' ),
					'std'   => get_bloginfo( 'name' )
				),
			),
			array(
				array(
					'id'    => 'admin',
					'type'  => 'custom',
					'title' => __( 'To administrator', 'wpsl' ),
				),
				array(
					'id'    => 'new_order_email_admin',
					'type'  => 'text',
					'title' => __( 'Subject of "new order"', 'wpsl' ),
					'help'  => '',
					'desc'  => '',
					'std'   => __( 'Received new order', 'wpsl' ),
				),
			),
			array(
				array(
					'id'    => 'customer',
					'type'  => 'custom',
					'title' => __( 'To buyer', 'wpsl' ),
				),
				array(
					'id'    => 'new_order_email_customer',
					'type'  => 'text',
					'title' => __( 'Subject of "new order"', 'wpsl' ),
					'help'  => '',
					'desc'  => '',
					'std'   => __( 'Your order has been received and is in process our manager', 'wpsl' ),
				),
			),
		)
	) );
	

	$sections[] = apply_filters( 'wpsl_before_sms', array(
		'icon'   => 'icon-message-square',
		'title'  => __( 'Sms', 'wpsl' ),
		'desc'   => __( 'Sending SMS', 'wpsl' ),
		'groups' => array(
			array(
				array(
					'id'    => 'sms',
					'type'  => 'custom',
					'title' => __( 'Gateway settings', 'wpsl' ),
				),
				array(
					'id'    => 'sms_url',
					'type'  => 'text',
					'title' => __( 'Url to send SMS via http', 'wpsl' ),
					'help'  => '',
					'desc'  => '<span style="cursor: pointer;background-color: #f1f1f1;padding: 2px 3px;font-size: 11px;border: 1px solid #eaeaea;" onClick="document.getElementById(\'wpsl-sms_url\').value=\'https://sms.ru/sms/send?api_id=[secret_code]&to=[phone]&msg=[msg]&json=1\'">SMS.RU</span> <span style="cursor: pointer;background-color: #f1f1f1;padding: 2px 3px;font-size: 11px;border: 1px solid #eaeaea;" onClick="document.getElementById(\'wpsl-sms_url\').value=\'https://smsc.ua/sys/send.php?login=[login]&psw=[secret_code]&phones=[phone]&mes=[msg]\'">SMSC.UA</span>',
					'std'   => ''
				),
				array(
					'id'    => 'secret_code',
					'type'  => 'text',
					'title' => __( 'Your', 'wpsl' ) . ' [secret_code]',
					'help'  => '',
					'desc'  => __( 'This can be a password, API ID, or other secret code to send SMS. For receiving please refer to SMS gateway.', 'wpsl' ),
					'std'   => ''
				),
				array(
					'id'    => 'sms_login',
					'type'  => 'text',
					'title' => __( 'Your', 'wpsl' ) . ' [login]',
					'help'  => '',
					'desc'  => __( 'If the formation of the http request requires login.', 'wpsl' ),
					'std'   => ''
				),
			),
			array(
				array(
					'id'    => 'sending',
					'type'  => 'custom',
					'title' => __( 'SMS sending', 'wpsl' ),
				),
				array(
					'id'    => 'smsinforming',
					'type'  => 'checkbox',
					'title' => __( 'SMS to administrator', 'wpsl' ),
					'help'  => '',
					'desc'  => __( 'Send to administrator about new order', 'wpsl' ),
					'std'   => '0',
					'rel'   => 'parent'
				),
				array(
					'id'    => 'phone_admin',
					'type'  => 'text',
					'title' => __( 'The phone of administrator', 'wpsl' ),
					'help'  => '',
					'desc'  => __( 'The cost of 1 SMS at the rates of your SMS gateway', 'wpsl' ),
					'std'   => '',
					'rel'   => 'child'
				),
				array(
					'id'    => 'smstobuyer',
					'type'  => 'checkbox',
					'title' => __( 'SMS to buyer', 'wpsl' ),
					'help'  => '',
					'desc'  => __( 'Send SMS to the buyer after ordering', 'wpsl' ),
					'std'   => '0',
					'rel'   => 'parent'
				),
				array(
					'id'    => 'smstobuyer_text',
					'type'  => 'text',
					'title' => __( 'Text of SMS', 'wpsl' ),
					'help'  => __( '1 SMS = 160 latin or 70 cyrillic characters. The length of the message includes spaces and punctuation', 'wpsl' ),
					'desc'  => '',
					'std'   => __( 'Thank you for being with us', 'wpsl' ),
					'rel'   => 'child'
				),
			),
			array(
				array(
					'id'    => 'sms_confirm',
					'type'  => 'checkbox',
					'title' => __( 'Verification', 'wpsl' ),
					'help'  => '',
					'desc'  => __( 'Enable sending a verification code via SMS in order page', 'wpsl' ),
					'std'   => '0',
					'rel'   => 'parent'
				),
				array(
					'id'    => 'sms_code',
					'type'  => 'number',
					'title' => __( 'Password', 'wpsl' ),
					'help'  => '',
					'desc'  => __( 'Current password to confirm phone', 'wpsl' ),
					'std'   => '1234',
					'rel'   => 'child'
				),
			),
		)
	) );

	
	$sections = apply_filters( 'wpsl_before_payment', $sections );
	
	
	$sections[] = apply_filters( 'wpsl_payment_settings', array(
		'icon'   => 'icon-credit-card',
		'title'  => __( 'Payment', 'wpsl' ),
		'desc'   => __( 'Payment gateways', 'wpsl' ),
		'groups' => array(
			array(
				array(
					'id'    => 'payment_methods',
					'type'  => 'checkbox',
					'title' => __( 'Payment', 'wpsl' ),
					'help'  => '',
					'desc'  => __( 'Enable selection of payment methods', 'wpsl' ),
					'std'   => '0',
					'rel'   => 'parent'
				),
				array(
					'id'    => 'payment_page',
					'type'  => 'select',
					'title' => __( 'Payment page', 'wpsl' ),
					'help'  => '',
					'desc'  => __( 'Payment page', 'wpsl' ),
					'opts'  => $pages_array,
					'std'   => wpsl_opt( 'payment_page' ),
					'rel'   => 'child'
				),
				array(
					'id'    => 'secret',
					'type'  => 'text',
					'title' => __( 'Secret code', 'wpsl' ),
					'help'  => __( 'This is a secret code that will allow you to receive notifications from the payment gateway about incoming payments or transfers via HTTPS (HTTP) if it supports this feature', 'wpsl' ),
					'desc'  => '',
					'std'   => hash( 'crc32b', rand() ),
					'rel'   => 'child'
				),
			),
			array(
				array(
					'id'    => 'cash',
					'type'  => 'checkbox',
					'title' => __( 'Cash payment', 'wpsl' ),
					'help'  => '',
					'desc'  => '',
					'std'   => '0',
					'rel'   => 'parent'
				),
				array(
					'id'    => 'cash_icon',
					'type'  => 'upload',
					'title' => '',
					'help'  => '',
					'desc'  => __( 'Icon of the payment method', 'wpsl' ),
					'std'   => WPSL_URL . '/assets/img/coins.svg',
					'rel'   => 'child'
				),
				array(
					'id'    => 'cash_note',
					'type'  => 'textarea',
					'title' => '',
					'help'  => '',
					'desc'  => __( 'Note the method of payment', 'wpsl' ),
					'std'   => __( 'Payment in cash at self-shipping or courier shipping', 'wpsl' ),
					'rel'   => 'child'
				),
			),
			array(
				array(
					'id'    => 'ym',
					'type'  => 'checkbox',
					'title' => __( 'Yandex.Money', 'wpsl' ),
					'help'  => '',
					'desc'  => '',
					'std'   => '0',
					'rel'   => 'parent'
				),
				array(
					'id'    => 'ymoney_icon',
					'type'  => 'upload',
					'title' => '',
					'help'  => '',
					'desc'  => __( 'Icon of the payment method', 'wpsl' ),
					'std'   => WPSL_URL . '/assets/img/yandex-money.svg',
					'rel'   => 'child'
				),
				array(
					'id'    => 'ymaccount',
					'type'  => 'text',
					'title' => __( 'Account number', 'wpsl' ),
					'help'  => __( 'To integrate the payment system Yandex.Money, enter the account number', 'wpsl' ),
					'desc'  => '',
					'rel'   => 'child'
				),
				array(
					'id'    => 'ymcode',
					'type'  => 'text',
					'title' => __( 'Secret code', 'wpsl' ),
					'help'  => sprintf( __( '%s to authenticate notifications', 'wpsl' ), '<a href="https://money.yandex.ru/myservices/online.xml" target="_blank">' . __( 'Get a secret code', 'wpsl'  ) . '</a>' ) . '<br>' . __( 'Notification receiving address', 'wpsl' ) . ':<br>' . get_site_url( null, '/?secret=' . wpsl_opt( 'secret' ) . '&payment=ym' ),
					'desc'  => '',
					'rel'   => 'child'
				),
				array(
					'id'    => 'ymfio',
					'type'  => 'checkbox',
					'title' => __( 'FIO', 'wpsl' ),
					'help'  => '',
					'desc'  => __( 'Should the buyer enter this data?', 'wpsl' ),
					'std'   => '0',
					'rel'   => 'child'
				),
				array(
					'id'    => 'ymphone',
					'type'  => 'checkbox',
					'title' => __( 'Phone', 'wpsl' ),
					'help'  => '',
					'desc'  => __( 'Should the buyer enter this data?', 'wpsl' ),
					'std'   => '0',
					'rel'   => 'child'
				),
				array(
					'id'    => 'ymaddress',
					'type'  => 'checkbox',
					'title' => __( 'Address', 'wpsl' ),
					'help'  => '',
					'desc'  => __( 'Should the buyer enter this data?', 'wpsl' ),
					'std'   => '0',
					'rel'   => 'child'
				),
				array(
					'id'    => 'ymemail',
					'type'  => 'checkbox',
					'title' => __( 'Email', 'wpsl' ),
					'help'  => '',
					'desc'  => __( 'Should the buyer enter this data?', 'wpsl' ),
					'std'   => '0',
					'rel'   => 'child'
				),
				array(
					'id'    => 'ym_note',
					'type'  => 'textarea',
					'title' => '',
					'help'  => '',
					'desc'  => __( 'Note the method of payment', 'wpsl' ),
					'std'   => __( 'Instant payment', 'wpsl' ),
					'rel'   => 'child'
				),
			),
			array(
				array(
					'id'    => 'webmoney',
					'type'  => 'checkbox',
					'title' => __( 'WebMoney', 'wpsl' ),
					'help'  => '',
					'desc'  => '',
					'std'   => '0',
					'rel'   => 'parent'
				),
				array(
					'id'    => 'webmoney_icon',
					'type'  => 'upload',
					'title' => '',
					'help'  => __( 'Icon of the payment method', 'wpsl' ),
					'desc'  => '',
					'std'   => WPSL_URL . '/assets/img/webmoney.svg',
					'rel'   => 'child'
				),
				array(
					'id'    => 'webmoney_account',
					'type'  => 'text',
					'title' => __( 'Purse', 'wpsl' ),
					'help'  => __( 'Purse number of Webmoney', 'wpsl' ),
					'desc'  => '',
					'std'   => 'R54000000000',
					'rel'   => 'child'
				),
				array(
					'id'    => 'webmoney_secret',
					'type'  => 'text',
					'title' => __( 'Secret code', 'wpsl' ),
					'help'  => sprintf( __( '%s to authenticate notifications', 'wpsl' ), '<a href="https://merchant.wmtransfer.com/conf/purses.asp" target="_blank">' . __( 'Get a secret code', 'wpsl'  ) . '</a>' ) . '<br>Result URL:<br>' . get_site_url( null, '/?secret=' . wpsl_opt( 'secret' ) . '&payment=webmoney' ),
					'desc'  => '',
					'std'   => '',
					'rel'   => 'child'
				),
				array(
					'id'    => 'webmoney_note',
					'type'  => 'textarea',
					'title' => '',
					'help'  => __( 'Note the method of payment', 'wpsl' ),
					'desc'  => '',
					'std'   => '',
					'rel'   => 'child'
				),
			),
			array(
				array(
					'id'    => 'paypal',
					'type'  => 'checkbox',
					'title' => __( 'PayPal', 'wpsl' ),
					'help'  => '',
					'desc'  => '',
					'std'   => '0',
					'rel'   => 'parent'
				),
				array(
					'id'    => 'paypal_icon',
					'type'  => 'upload',
					'title' => '',
					'help'  => '',
					'desc'  => __( 'Icon of the payment method', 'wpsl' ),
					'std'   => WPSL_URL . '/assets/img/paypal.svg',
					'rel'   => 'child'
				),
				array(
					'id'    => 'paypal_email',
					'type'  => 'email',
					'title' => __( 'Email of seller', 'wpsl' ),
					'help'  => '',
					'desc'  => '',
					'rel'   => 'child'
				),
				array(
					'id'    => 'paypal_note',
					'type'  => 'textarea',
					'title' => '',
					'help'  => '',
					'desc'  => __( 'Note the method of payment', 'wpsl' ),
					'std'   => '',
					'rel'   => 'child'
				),
			),
			array(
				array(
					'id'    => 'cash_on_delivery',
					'type'  => 'checkbox',
					'title' => __( 'Cash on delivery', 'wpsl' ),
					'help'  => '',
					'desc'  => '',
					'std'   => '0',
					'rel'   => 'parent'
				),
				array(
					'id'    => 'cash_on_delivery_icon',
					'type'  => 'upload',
					'title' => '',
					'help'  => '',
					'desc'  => __( 'Icon of the payment method', 'wpsl' ),
					'std'   => WPSL_URL . '/assets/img/russian-post.svg',
					'rel'   => 'child'
				),
				array(
					'id'    => 'cash_on_delivery_note',
					'type'  => 'textarea',
					'title' => '',
					'help'  => '',
					'desc'  => __( 'Note the method of payment', 'wpsl' ),
					'std'   => '',
					'rel'   => 'child'
				),
			),
			array(
				array(
					'id'    => 'bank_transfer',
					'type'  => 'checkbox',
					'title' => __( 'Bank transfer', 'wpsl' ),
					'help'  => '',
					'desc'  => '',
					'std'   => '0',
					'rel'   => 'parent'
				),
				array(
					'id'    => 'bank_transfer_icon',
					'type'  => 'upload',
					'title' => '',
					'help'  => '',
					'desc'  => __( 'Icon of the payment method', 'wpsl' ),
					'std'   => WPSL_URL . '/assets/img/bank.svg',
					'rel'   => 'child'
				),
				array(
					'id'    => 'bank_transfer_note',
					'type'  => 'textarea',
					'title' => '',
					'help'  => '',
					'desc'  => __( 'Note the method of payment', 'wpsl' ),
					'std'   => '',
					'rel'   => 'child'
				),
			),
		)
	) );
	
	$sections = apply_filters( 'wpsl_after_payment', $sections );
	
	new WPSL_Options( array( 'options' => apply_filters( 'wpsl_initial_options', $sections ) ) );
}