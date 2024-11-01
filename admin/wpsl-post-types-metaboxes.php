<?php
/**
 * wpStore
 *
 * Add metaboxes
 * 
 * @author	    WP Kama
 * @author url	https://wp-kama.ru/id_6732/kama_post_meta_box-sozdaem-metapolya-dlya-zapisej.html
 * @since	    2.1.0
 */


if ( ! defined( 'ABSPATH' ) ) exit;


if ( !is_admin() ) return;
 

add_action( 'init', 'wpsl_add_metaboxes' );
function wpsl_add_metaboxes() {
	/**
	 * Метабокс для управления параметрами товара
	 *
	 * @author	wpStore
	 * @since	2.7.0
	 */
	$tab[] = apply_filters( 'wpsl_product_metabox_tab_base', array(
		'icon'   => 'icon-settings',
		'title'  => __( 'General', 'wpsl' ),
		'rel'    => '',
		'class'  => '',
		'fields' => array(
			'type-product' => array(
				'class' => 'check',
				'type'  => 'select',
				'title' => '<h2>' . __( 'Product card', 'wpsl' ) . ' — </h2>',
				'args'  => wpsl_type_product_list(),
				'desc'  => '',
				'attr'  => '',
				'std'   => '',
				'ph'    => ''
			),
			'_digital' => array(
				'class' => 'check',
				'type'  => 'checkbox',
				'title' => __( 'Digital', 'wpsl' ),
				'attr'  => '',
				'std'   => ''
			),
			'hit_product' => array(
				'class' => 'check',
				'type'  => 'checkbox',
				'title' => __( 'Bestseller', 'wpsl' ),
				'attr'  => '',
				'desc'  => '',
				'std'   => '',
				'ph'    => ''
			)
		)
	) );


	$tab[] = apply_filters( 'wpsl_product_metabox_tab_general', array(
		'icon'   => 'icon-settings',
		'title'  => __( 'General', 'wpsl' ),
		'rel'    => '',
		'class'  => '',
		'fields' => array(
			'_regular_price' => array(
				'type'  => 'text',
				'title' => sprintf( __( 'Base price (%s)', 'wpsl' ), wpsl_opt() ),
				'desc'  => '',
				'attr'  => '',
				'std'   => '',
				'ph'    => ''
			),
			'_sale_price' => array(
				'type'  => 'text',
				'title' => sprintf( __( 'Sale price (%s)', 'wpsl' ), wpsl_opt() ),
				'desc'  => '',
				'attr'  => '',
				'std'   => '',
				'ph'    => ''
			),
			'_sku' => array(
				'type'  => 'text',
				'title' => __( 'Sku', 'wpsl' ),
				'desc'  => __( 'Sku is a unique identifier of the product', 'wpsl' ),
				'attr'  => '',
				'std'   => '',
				'ph'    => ''
			),
			'_stock_status' => array(
				'title' => __( 'Stock status', 'wpsl' ),
				'type'  => 'select',
				'args'  => wpsl_get_stock_status(),
				'attr'  => '',
				'desc'  => __( 'Controls how the product will be displayed in the frontend', 'wpsl' ),
				'std'   => 'instock',
				'ph'    => '',
				'rel'   => '',
			),
			'_upload_file' => array(
				'title' => __( 'Downloadable files', 'wpsl' ),
				'type'  => 'uploader',
				'attr'  => '',
				'desc'  => __( 'Buyer receives one-time download link', 'wpsl' ),
				'std'   => '',
				'ph'    => ''
			),
			'_product_url' => array(
				'title' => __( 'Product URL', 'wpsl' ),
				'type'  => 'text',
				'attr'  => '',
				'desc'  => __( 'Enter an external link to the product', 'wpsl' ),
				'std'   => '',
				'ph'    => 'https://',
				'rel'   => 'external',
			),
			'_product_image_gallery' => array(
				'type'  => 'gallery',
				'title' => __( 'Gallery of product', 'wpsl' ),
				'desc'  => '',
				'attr'  => '',
				'std'   => '',
				'ph'    => ''
			),
		)
	) );
	
	$tab[] = apply_filters( 'wpsl_product_metabox_tab_attributes', array(
		'icon'   => 'icon-layers',
		'title'  => __( 'Attributes', 'wpsl' ),
		'rel'    => '',
		'class'  => '',
		'fields' => array(
			'char' => array(
				'type'  => 'char',
				'title' => '',
				'desc'  => '',
				'attr'  => '',
				'std'   => '',
				'ph'    => ''
			),
		)
	) );
	
	$tab[] = apply_filters( 'wpsl_product_metabox_tab_variations', array(
		'icon'   => 'icon-grid',
		'title'  => __( 'Variations', 'wpsl' ),
		'rel'    => 'variable',
		'class'  => '',
		'fields' => array(
			'variations' => array(
				'type'  => 'variations',
				'title' => __( 'Product variations', 'wpsl'),
				'desc'  => '',
				'attr'  => '',
				'std'   => '',
				'ph'    => ''
			),
		)
	) );
	
	$tab[] = apply_filters( 'wpsl_product_metabox_tab_sponsorship', array(
		'icon'   => 'icon-bar-chart',
		'title'  => __( 'Project', 'wpsl' ),
		'rel'    => 'sponsorship',
		'class'  => '',
		'fields' => array(
			'goal' => array(
				'type'  => 'number',
				'title' => __( 'Goal', 'wpsl' ) . ' (' . wpsl_opt() . ')',
				'desc'  => __( 'The amount needed to complete your project', 'wpsl' ),
				'attr'  => '',
				'std'   => '',
				'ph'    => ''
			),
			'funded' => array(
				'type'  => 'number',
				'title' => __( 'Investments', 'wpsl' ) . ' (' . wpsl_opt() . ')',
				'desc'  => __( 'How much money is collected at the moment', 'wpsl' ),
				'attr'  => '',
				'std'   => '',
				'ph'    => ''
			),
			'minimum_fee' => array(
				'type'  => 'number',
				'title' => __( 'Minimum sponsor fee', 'wpsl' ) . ' (' . wpsl_opt() . ')',
				'desc'  => '',
				'attr'  => '',
				'std'   => '',
				'ph'    => ''
			),
			'completion' => array(
				'type'  => 'date',
				'title' => __( 'Date of completion', 'wpsl' ),
				'desc'  => __( 'If the field is empty, the duration of the investment collection is unlimited', 'wpsl' ),
				'attr'  => '',
				'std'   => '',
				'ph'    => ''
			),
		)
	) );
	
	$tab[] = apply_filters( 'wpsl_product_metabox_tab_auction', array(
		'icon'   => 'icon-pie-chart',
		'title'  => __( 'Auction', 'wpsl' ),
		'rel'    => 'auction',
		'class'  => 'auction',
		'fields' => array(
			'start_price' => array(
				'type'  => 'number',
				'title' => __( 'Start price', 'wpsl' ) . ' (' . wpsl_opt() . ')',
				'desc'  => __( 'The price at which the auction will start', 'wpsl'),
				'attr'  => '',
				'std'   => '',
				'ph'    => ''
			),
			'bid_increment' => array(
				'type'  => 'number',
				'title' => __( 'Bid increment', 'wpsl' ) . ' (' . wpsl_opt() . ')',
				'desc'  => __( 'The minimum step bid', 'wpsl'),
				'attr'  => '',
				'std'   => '',
				'ph'    => ''
			),
			'lowest_price' => array(
				'type'  => 'number',
				'title' => __( 'Reserve price', 'wpsl' ) . ' (' . wpsl_opt() . ')',
				'desc'  => __( 'Price below which you are not required to sell the product', 'wpsl'),
				'attr'  => '',
				'std'   => '',
				'ph'    => ''
			),
			'blitz_price' => array(
				'type'  => 'number',
				'title' => __( 'The blitz price', 'wpsl' ) . ' (' . wpsl_opt() . ')',
				'desc'  => __( 'Price for which the seller is ready to sell the goods without waiting for the end of the auction', 'wpsl'),
				'attr'  => '',
				'std'   => '',
				'ph'    => ''
			),
			'auction_end_date' => array(
				'type'  => 'date',
				'title' => __( 'Ending date', 'wpsl' ),
				'desc'  => __( 'Set the end date of auction product', 'wpsl'),
				'attr'  => '',
				'std'   => '',
				'ph'    => ''
			),
		)
	) );
	
	$tab[] = apply_filters( 'wpsl_product_metabox_tab_dimensions', array(
		'icon'   => 'icon-box',
		'title'  => __( 'Dimensions', 'wpsl' ),
		'rel'    => '',
		'class'  => '',
		'fields' => array(
			'_weight' => array(
				'type'  => 'text',
				'title' => __( 'Weight', 'wpsl' ) . ' (' . wpsl_weight_unit( wpsl_opt( 'weight_unit', 'kg' ) ) . ')',
				'desc'  => '',
				'attr'  => '',
				'std'   => '',
				'ph'    => ''
			),
			'_length' => array(
				'type'  => 'text',
				'title' => __( 'Length', 'wpsl' ) . ' (' . wpsl_dimensions_unit( wpsl_opt( 'dimension_unit', 'mm' ) ) . ')',
				'desc'  => '',
				'attr'  => '',
				'std'   => '',
				'ph'    => ''
			),
			'_width' => array(
				'type'  => 'text',
				'title' => __( 'Width', 'wpsl' ) . ' (' . wpsl_dimensions_unit( wpsl_opt( 'dimension_unit', 'mm' ) ) . ')',
				'desc'  => '',
				'attr'  => '',
				'std'   => '',
				'ph'    => ''
			),
			'_height' => array(
				'type'  => 'text',
				'title' => __( 'Height', 'wpsl' ) . ' (' . wpsl_dimensions_unit( wpsl_opt( 'dimension_unit', 'mm' ) ) . ')',
				'desc'  => '',
				'attr'  => '',
				'std'   => '',
				'ph'    => ''
			),
		)
	) );
	
	$tab[] = apply_filters( 'wpsl_product_metabox_tab_more', array(
		'icon'   => 'icon-sliders',
		'title'  => __( 'More', 'wpsl' ),
		'rel'    => '',
		'class'  => '',
		'fields' => array(
			'_purchase_note' => array(
				'type'  => 'textarea',
				'title' => __( 'A note to the product', 'wpsl' ),
				'desc'  => __( 'Additional information about the product. Displayed on the individual product page', 'wpsl' ),
				'attr'  => '',
				'std'   => '',
				'ph'    => ''
			),
		)
	) );
	
	$tab[] = apply_filters( 'wpsl_product_metabox_tab_seo', array(
		'icon'   => 'icon-globe',
		'title'  => __( 'SEO', 'wpsl' ),
		'rel'    => '',
		'class'  => '',
		'fields' => array(
			'_meta_title' => array(
				'type'  => 'text',
				'title' => __( 'Title of product', 'wpsl' ),
				'desc'  => __( 'Most search engines see only 60 characters', 'wpsl' ),
				'attr'  => '',
				'std'   => '',
				'ph'    => ''
			),
			'_keywords' => array(
				'type'  => 'text',
				'title' => __( 'Meta keywords', 'wpsl' ),
				'desc'  => __( 'A list of keywords separated by commas', 'wpsl' ),
				'attr'  => '',
				'std'   => '',
				'ph'    => ''
			),
			'_description' => array(
				'type'  => 'textarea',
				'title' => __( 'Meta description', 'wpsl' ),
				'desc'  => __( 'The recommended length is 160 characters', 'wpsl' ),
				'attr'  => '',
				'std'   => '',
				'ph'    => ''
			),
		)
	) );
	
	class_exists( 'WPSL_Meta_Boxes' ) && new WPSL_Meta_Boxes(
		array(
			'id'   => 'product',                    // ID метабокса, а также префикс названия произвольного поля
			'name' => __( 'Product card', 'wpsl' ), // заголовок метабокса
			'post' => array( 'product' ),           // типы постов для которых нужно отобразить метабокс
			'pos'  => 'normal',                     // расположение, параметр $context функции add_meta_box()
			'pri'  => 'high',                       // приоритет, параметр $priority функции add_meta_box()
			'cap'  => 'edit_posts',                 // какие права должны быть у пользователя
			'type' => 'tabs',
			'args' => $tab
		)
	);
	
	/**
	 * Метабокс для управления стоимостью доставки
	 *
	 * @author	wpStore
	 * @since	2.0.0
	 */
	$tab = array();
	$tab[] = apply_filters( 'wpsl_product_metabox_gallery',
		array(
			'icon'   => 'icon-settings',
			'title'  => __( 'General', 'wpsl' ),
			'rel'    => '',
			'class'  => '',
			'fields' => array(
				'delivery_price' => array(
					'type'  => 'number',
					'title' => __( 'Shipping price', 'wpsl' ),
					'desc'  => __( 'Fixed shipping cost', 'wpsl'),
					'attr'  => '',
					'std'   => '',
					'ph'    => ''
				),
			),
		)
	);
	class_exists( 'WPSL_Meta_Boxes' ) && new WPSL_Meta_Boxes(
		array( 
			'id'   => 'shipping',
			'post' => array( 'delivery' ),
			'name' => __( 'Details of shipping', 'wpsl' ),
			'pos'  => 'normal',
			'pri'  => 'high',
			'cap'  => 'edit_posts',
			'type' => 'tabs',
			'args' => $tab,
		)
	);
	 
	 
	/**
	 * wpStore
	 *
	 * Создаем метабокс для управления заказом
	 *
	 * @author	wpStore
	 * @since	2.0.0
	 */
	$shipping = new WPSL_Shipping();
	$payment  = new WPSL_Payment();
	$tab = array();
	$tab[] = apply_filters( 'wpsl_order_metabox_tab_base',
		array(
			'icon'   => 'icon-package',
			'title'  => __( 'Order', 'wpsl' ),
			'rel'    => '',
			'class'  => '',
			'fields' => array(
				'order' => array(
					'title'    => '',
					'type'     => 'custom',
					'callback' => 'wpsl_special_field_out_function',
					'std'      => '',
				),
				'status' => array(
					'type'     => 'custom',
					'callback' => 'wpsl_fill_status_order_metabox',
					'std'      => '',
				)
			)
		)
	);
	$tab[] = apply_filters( 'wpsl_order_metabox_tab_address',
		array(
			'icon'   => 'icon-user',
			'title'  => __( 'Client', 'wpsl' ),
			'rel'    => '',
			'class'  => '',
			'fields' => array(
				'name' => array(
					'type'  => 'text',
					'title' => __( 'Name', 'wpsl' ),
					'desc'  => __( 'The name of the buyer', 'wpsl' ),
					'attr'  => '',
					'std'   => '',
					'ph'    => __( 'The name of the buyer', 'wpsl' ),
				),
				'email' => array(
					'type'  => 'text',
					'title' => __( 'Email', 'wpsl' ),
					'desc'  => __( 'The email of the buyer', 'wpsl' ),
					'attr'  => '',
					'std'   => '',
					'ph'    => __( 'The email of the buyer', 'wpsl' ),
				),
				'phone' => array(
					'type'  => 'text',
					'title' => __( 'Phone', 'wpsl' ),
					'desc'  => __( 'The phone of the buyer', 'wpsl' ),
					'attr'  => '',
					'std'   => '',
					'ph'    => __( 'The phone of the buyer', 'wpsl' ),
				),
				'address' => array(
					'type'  => 'textarea',
					'title' => __( 'Address', 'wpsl' ),
					'desc'  => __( 'Where to send products', 'wpsl' ),
					'attr'  => '',
					'std'   => '',
					'ph'    => __( 'Where to send products', 'wpsl' ),
				),
				'payment' => array(
					'type'  => 'select',
					'title' => __( 'Type of payment', 'wpsl' ),
					'desc'  => __( 'How the order will be paid', 'wpsl' ),
					'attr'  => '',
					'std'   => '',
					'args'  => $payment->methods(),
				),
				'delivery_type' => array(
					'type'  => 'select',
					'title' => __( 'Shipping', 'wpsl' ),
					'desc'  => __( 'How to deliver products', 'wpsl' ),
					'attr'  => '',
					'std'   => '',
					'args'  => $shipping->get_shipping(),
				),
			)
		)
	);
	class_exists( 'WPSL_Meta_Boxes' ) && new WPSL_Meta_Boxes(
		array(
			'id'   => 'order',
			'name' => __( 'Order detail', 'wpsl' ),
			'post' => array( 'shop_order' ),
			'pos'  => 'normal',
			'pri'  => 'high',
			'cap'  => 'edit_posts',
			'type' => 'tabs',
			'args' => $tab
		)
	);
	
	// coupons
	$tab = array();
	$tab[] = apply_filters( 'wpsl_coupons_metabox_main',
		array(
			'icon'   => 'icon-settings',
			'title'  => __( 'General', 'wpsl' ),
			'rel'    => '',
			'class'  => '',
			'fields' => array(
				'discount_type' => array(
					'type'  => 'select',
					'title' => __( 'Discount type', 'wpsl' ),
					'desc'  => '',
					'attr'  => '',
					'std'   => '',
					'args'  => array(
						'percent'          => __( 'Percentage cart discount', 'wpsl' ),
						'fixed_cart'       => __( 'Fixed cart discount', 'wpsl' ),
						//'fixed_product'    => __( 'Fixed product discount', 'wpsl' ),
						'percent_quantity' => __( 'Percentage on the quantity of products', 'wpsl' ),
						'fixed_quantity'   => __( 'Fixed on the quantity of products', 'wpsl' ),
					),
				),
				'coupon_amount' => array(
					'type'  => 'text',
					'title' => __( 'Coupon amount', 'wpsl' ),
					'desc'  => __( 'Discount coupon. You can use formulas, for example: 5000:20,3000:10,1500:5', 'wpsl' ),
					'attr'  => '',
					'std'   => '',
					'ph'    => ''
				),
				'discount_apply' => array(
					'type'  => 'select',
					'title' => __( 'Apply', 'wpsl' ),
					'desc'  => __( 'How to apply discount', 'wpsl' ),
					'attr'  => '',
					'std'   => '',
					'args'  => array(
						'request'       => __( 'At request', 'wpsl' ),
						'automatically' => __( 'Automatically', 'wpsl' ),
					),
				),
				'date_expires' => array(
					'type'  => 'date',
					'title' => __( 'Coupon expiry date', 'wpsl' ),
					'desc'  => __( 'Set the end date of auction product', 'wpsl'),
					'attr'  => '',
					'std'   => '',
					'ph'    => ''
				),
				'send_coupon' => array(
					'type'  => 'select',
					'title' => __( 'Send coupon', 'wpsl' ),
					'desc'  => __( 'Specifies how to receive the coupon. The setting is not used if the coupon is applied automatically', 'wpsl'),
					'attr'  => '',
					'std'   => '',
					'args'  => apply_filters( 'wpsl_send_coupon', array(
						''            => __( 'Do not send coupon', 'wpsl' ),
						'first_order' => __( 'Send a coupon after the first order', 'wpsl' ),
					) ),
				),
			)
		)
	);
	$tab[] = apply_filters( 'wpsl_coupons_metabox_restriction',
		array(
			'icon'   => 'icon-shield',
			'title'  => __( 'Usage restriction', 'wpsl' ),
			'rel'    => '',
			'class'  => '',
			'fields' => array(
				'minimum_amount' => array(
					'type'  => 'text',
					'title' => __( 'Minimum spend', 'wpsl' ),
					'desc'  => __( 'Set the minimum total cart (subtotal) amount required to use the coupon', 'wpsl' ),
					'attr'  => '',
					'std'   => '',
					'ph'    => ''
				),
				'maximum_amount' => array(
					'type'  => 'text',
					'title' => __( 'Maximum spend', 'wpsl' ),
					'desc'  => __( 'Set the maximum total cart (subtotal) amount required to use the coupon', 'wpsl' ),
					'attr'  => '',
					'std'   => '',
					'ph'    => ''
				),
				'individual_use' => array(
					'class' => 'check',
					'type'  => 'checkbox',
					'title' => __( 'Individual use', 'wpsl' ),
					'desc'  => __( 'Check this box if the coupon cannot be used in conjunction with other coupons', 'wpsl' ),
					'attr'  => '',
					'std'   => ''
				),
				'exclude_sale_items' => array(
					'type'  => 'checkbox',
					'title' => __( 'Exclude sale items', 'wpsl' ),
					'desc'  => __( 'Check this box if the coupon should not apply to items with discount. Option to exclude discounted products from the calculation of the total discount regardless of the "Type of discount"', 'wpsl'),
					'attr'  => '',
					'std'   => '',
					'ph'    => ''
				),
				'users_limits' => array(
					'type'  => 'select',
					'title' => __( 'User limits', 'wpsl' ),
					'desc'  => __( 'Apply the coupon only to the specified group of users', 'wpsl'),
					'attr'  => '',
					'std'   => '',
					'args'  => apply_filters( 'wpsl_coupon_users_limits', array(
						'all' => __( 'All users', 'wpsl' ),
					) ),
				),
			)
		)
	);
	$tab[] = apply_filters( 'wpsl_coupons_metabox_limits',
		array(
			'icon'   => 'icon-lock',
			'title'  => __( 'Usage limits', 'wpsl' ),
			'rel'    => '',
			'class'  => '',
			'fields' => array(
				'usage_limit' => array(
					'type'  => 'number',
					'title' => __( 'Usage limit per coupon', 'wpsl' ),
					'desc'  => __( 'How many times this coupon can be used before it is void', 'wpsl' ),
					'attr'  => '',
					'std'   => '',
					'ph'    => ''
				),
				'usage_limit_per_user' => array(
					'type'  => 'number',
					'title' => __( 'Usage limit per user', 'wpsl' ),
					'desc'  => __( 'How many times this coupon can be used by an individual user. Uses billing email for guests, and user ID for logged in users', 'wpsl' ),
					'attr'  => '',
					'std'   => '',
					'ph'    => ''
				),
			)
		)
	);
	class_exists( 'WPSL_Meta_Boxes' ) && new WPSL_Meta_Boxes(
		array(
			'id'   => 'coupons',
			'name' => __( 'Coupon data', 'wpsl' ),
			'post' => array( 'shop_coupon' ),
			'pos'  => 'normal',
			'pri'  => 'high',
			'cap'  => 'edit_posts',
			'type' => 'tabs',
			'args' => $tab
		)
	);
}