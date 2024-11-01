<?php
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Check current theme support wpStore
 * If the theme supports the plugin and there is a single-product.php just returns the contents of the page
 * Otherwise, connect template from plugin
 */
add_filter( 'the_content', 'wpsl_single_product_content' );
function wpsl_single_product_content( $content ) {
	if( current_theme_supports( 'wpstore' ) && is_page_template( 'single-product.php' ) ) {
		return $content;
	} else {
		if( is_singular( 'product' ) ) {
			$content = wpsl_get_template_html( 'single', 'product' );
		}
	}
	return $content;
}


/**
 * Get template part (for templates like the shop-loop)
 *
 * @param mixed  $slug Template slug.
 * @param string $name Template name (default: '').
 */
add_filter( 'template_include', 'wpsl_template_loader' );
function wpsl_template_loader( $template ) {

	if( !current_theme_supports( 'wpstore' ) ) return $template; 

	if ( is_embed() ) {
		return $template;
	}
	
	if ( is_tax( get_object_taxonomies( 'product' ) ) ) {
		if ( is_tax( 'product_cat' ) || is_tax( 'product_tag' ) ) {
			if ( !file_exists( get_stylesheet_directory() . '/wpstore.php' ) ) {
				return $template;
			}
			$template = wpsl_override_template( 'wpstore' );
		}
	}
	
	return $template;
}


/**
 * Disable comment template in single product if theme not support wpStore
 * Comment form will be in tabs
 *
 * @author	wpStore
 * @since	2.7
 */
add_filter( 'comments_template', 'wpsl_disable_comments_template', 10, 1 );
function wpsl_disable_comments_template( $theme_template ) {
	if ( is_singular( 'product' ) && !defined( 'WPSL_SUPPORT' ) ) {
		$theme_template = WPSL_DIR . 'templates/empty-comment-template.php';
	}
	return $theme_template;
}


/**
 * Add new data on the product page to the javascript object
 *
 * @author	wpStore
 * @since	2.7
 */
add_filter( 'wpsl_add_data_to_localize', 'wpsl_add_variable_product_data', 10, 1 );
function wpsl_add_variable_product_data( $array ) {
	global $post;
	if ( is_singular( 'product' ) && get_post_meta( $post->ID, 'type-product', true ) == 'variable' && $variations = get_post_meta( $post->ID, '_product_variations', true ) ) {
		foreach( $variations as $var ) {
			$var = json_decode( $var );
			if ( $var->item_image != '' ) {
				$var->item_image = wp_get_attachment_image_url( $var->item_image, 'wpsl-big-thumb' );
			}
			$ele[] = $var;
		}
		$array['variable'] = $ele;
	}
	return $array;
}


/**
 * In the usual comments display only comments, exclude reviews
 *
 * @since	2.7
 */
add_filter( 'wp_list_comments_args', 'wpsl_list_comments_args', 10, 1 );
function wpsl_list_comments_args( $args ) {
	$args['type'] = 'comment';
	return $args;
}


/**
 * Change the count of regular comments
 *
 * @since	2.7
 */
add_filter( 'get_comments_number', 'wpsl_get_comments_number', 10, 2 );
function wpsl_get_comments_number( $count, $post_id ) {
	if ( $count > 0 ) {
		$comments = get_comments(
			array(
				'post_id'      => $post_id,
				'status'       => 'approve',
				'type__not_in' => 'review',
			)
		);
		$count = count( $comments );
	} else {
		$count = $count;
	}
	return apply_filters( 'wpsl_get_comments_number', $count, $post_id );
}


/**
 * The output of the thumbnail of the product
 *
 * If the theme does not support the wpStore plugin,
 * remove the thumbnail display on the product page.
 * Because the thumbnail is displayed in the product gallery.
 *
 * @since	2.7
 */
add_filter( 'has_post_thumbnail', 'wpsl_has_post_thumbnail', 10, 1 );
function wpsl_has_post_thumbnail( $has_thumbnail ) {
	if ( is_singular( 'product' ) && !current_theme_supports( 'wpstore' ) ) {
		return false;
	} else {
		return $has_thumbnail;
	}
}


/**
 * The output of the thumbnail of the product
 *
 * If the theme does not support the wpStore plugin,
 * remove the thumbnail display on the product page.
 * Because the thumbnail is displayed in the product gallery.
 *
 * @since	2.7
 */
add_filter( 'post_thumbnail_html', 'wpsl_post_thumbnail_html', 10, 5 );
function wpsl_post_thumbnail_html( $html, $post_id, $post_thumbnail_id, $size, $attr ) {
	if ( is_singular( 'product' ) && !current_theme_supports( 'wpstore' ) ) {
		return '';
	} else {
		return $html;
	}
}


/**
 * Hide the tab with technical support, if it is disabled in the admin panel
 *
 * @since 2.4.0
 */
add_filter( 'wpsl_counstructor_tabs', 'wpsl_remove_support_tab', 10, 1 );
function wpsl_remove_support_tab( $args ) {
	if ( wpsl_opt( 'support' ) == 1 ) {
		return $args;
	} else {
		unset( $args['support'] );
		return $args;
	}
}


/**
 * Get account fields
 *
 * @since 2.7.0
 */
add_filter( 'wpsl_get_account', 'wpsl_get_account_tabs_and_menu', 10, 1 );
function wpsl_get_account_tabs_and_menu( $fields = array() ) {
	$fields = apply_filters( 'wpsl_get_account_fields',
		array(
			'avatar' => array( 
				'name'         => __( 'Avatar', 'wpsl' ),
				'icon'         => 'icon-shopping-bag',
				'must_logged'  => false,
				'class'        => 'avatar',
				'fill'         => 'tab-avatar',
				'fill_in_menu' => true,
				'notif'        => '',
			),
			'orders' => array( 
				'name'         => __( 'My orders', 'wpsl' ),
				'icon'         => 'icon-shopping-bag',
				'must_logged'  => true,
				'class'        => 'active check',
				'fill'         => 'tab-orders',
				'fill_in_menu' => '',
				'notif'        => '',
			),
			'profile' => array( 
				'name'         => __( 'My profile', 'wpsl' ),
				'icon'         => 'icon-user',
				'must_logged'  => true,
				'class'        => 'check',
				'fill'         => 'tab-profile',
				'fill_in_menu' => '',
				'notif'        => '',
			),
			'support' => array( 
				'name'         => __( 'Support', 'wpsl' ),
				'icon'         => 'icon-life-buoy',
				'must_logged'  => true,
				'class'        => 'check',
				'fill'         => 'tab-support',
				'fill_in_menu' => '',
				'notif'        => '',
			),
			'ticket' => array( 
				'name'         => __( 'Create ticket', 'wpsl' ),
				'icon'         => 'icon-pen-tool',
				'must_logged'  => true,
				'class'        => 'check no-icon',
				'fill'         => 'tab-ticket',
				'fill_in_menu' => '',
				'notif'        => '',
			),
			'reviews' => array( 
				'name'         => __( 'My reviews', 'wpsl' ),
				'icon'         => 'icon-star',
				'must_logged'  => true,
				'class'        => 'check',
				'fill'         => 'tab-reviews',
				'fill_in_menu' => '',
				'notif'        => '<span class="notif">' . count( wpsl_get_user_review() ) . '<span>',
			),
			'login' => array( 
				'name'         => !is_user_logged_in() ? __( 'Login', 'wpsl' ) : __( 'Log out', 'wpsl' ),
				'icon'         => !is_user_logged_in() ? 'icon-log-in' : 'icon-log-out',
				'must_logged'  => false,
				'class'        => !is_user_logged_in() ? 'check active' : '',
				'fill'         => 'tab-login',
				'fill_in_menu' => is_user_logged_in() ? true : false,
				'notif'        => '',
			),
			'lostpassword' => array( 
				'name'         => !is_user_logged_in() ? __( 'Lost password', 'wpsl' ) : '',
				'icon'         => !is_user_logged_in() ? 'icon-settings' : '',
				'must_logged'  => false,
				'class'        => !is_user_logged_in() ? 'check no-icon' : '',
				'fill'         => 'tab-lostpassword',
				'fill_in_menu' => is_user_logged_in() ? true : false,
				'notif'        => '',
			),
			'registration' => array( 
				'name'         => __( 'Registration', 'wpsl' ),
				'icon'         => 'icon-key',
				'must_logged'  => false,
				'class'        => !is_user_logged_in() ? 'check' : '',
				'fill'         => 'tab-registration',
				'fill_in_menu' => is_user_logged_in() ? true : false,
				'notif'        => '',
			),
			'collapse' => array( 
				'name'         => __( 'Collapse', 'wpsl' ),
				'icon'         => 'icon-sidebar',
				'must_logged'  => false,
				'class'        => 'collapse',
				'fill'         => '',
				'fill_in_menu' => true,
				'notif'        => '',
			),
		)
	);
	return $fields;
}


/**
 * Show errors in login page
 *
 * @since 2.7.0
 */
add_filter( 'wpsl_account_tab_login_before', 'custom_login_form_to_login_page' );
function custom_login_form_to_login_page() {

	$output = $message = '';
	if (! empty($_GET['action']) ) {
		if ( 'failed' == $_GET['action'] )
			$message = __( 'There was a problem with your username or password', 'wpsl' );
		elseif ( 'loggedout' == $_GET['action'] )
			$message = __( 'You are now logged out', 'wpsl' );
		elseif ( 'recovered' == $_GET['action'] )
			$message = __( 'Check your e-mail for the confirmation link', 'wpsl' );
	}

	if ( $message ){
		$output .= '<div class="wpsl-notify wpsl-hidden">' . $message;
		$output .= '<br><a href="' . wp_lostpassword_url( add_query_arg( 'action', 'recovered', get_permalink() ) ) .'" title="' . __( 'Recover Lost Password', 'wpsl' ) . '">' . __( 'Lost Password?', 'wpsl' ) . '</a>';
		$output .= '</div>';
	}

    echo $output;
}


/**
 * Redirect after successful registration
 *
 * @since 2.7.0
 */
add_filter( 'registration_redirect', 'wpsl_redirect_after_successful_registration' );
function wpsl_redirect_after_successful_registration() {
    return get_page_uri( wpsl_opt( 'pageaccount' ) );
}


/**
 * Redirect for lost password
 *
 * @since 2.7.0
 */
add_filter( 'lostpassword_url', 'wpsl_change_lostpassword_url', 10, 2 );
function wpsl_change_lostpassword_url( $url, $redirect ) {
	return add_query_arg( array( 'redirect' => $redirect ), get_page_uri( wpsl_opt( 'pageaccount' ) ) );
}


/**
 * Filling out a simple product form
 *
 * @since 2.7.0
 */
add_filter( 'wpsl_get_form_simple', 'wpsl_get_form_simple_product', 10, 1 );
function wpsl_get_form_simple_product( $fields ) {
	global $post;
	$fields = array_merge( $fields,
		array(
			array(
				'type'        => 'custom',
				'fill'        => '<div class="wpsl-sep"><span>' . __( 'Details', 'wpsl' ) . '</span></div>',
			),
			array(
				'type'        => 'cart',
				'name'        => 'count',
				'title'       => __( 'Select count', 'wpsl' ),
				'value'       => '1',
				'class'       => 'add-cart',
				'placeholder' => __( 'Select count', 'wpsl' ),
				'required'    => 1,
				'attr'        => 'step="1" min="1"', 
				'notice'      => __( 'Notice', 'wpsl' )
			),
			array(
				'type'        => 'hidden',
				'name'        => 'post_id',
				'value'       => $post->ID
			),
			array(
				'type'        => 'hidden',
				'name'        => 'saveguard',
				'value'       => ''
			)
		)
	);
	return $fields;
}


/**
 * Filling out a simple product form
 *
 * @since 2.7.0
 */
add_filter( 'wpsl_get_form_external', 'wpsl_get_form_external_product', 10, 1 );
function wpsl_get_form_external_product( $fields ) {
	global $post;
	$fields = array_merge( $fields,
		array(
			array(
				'type'        => 'custom',
				'fill'        => '<div class="wpsl-sep"><span>' . __( 'Details', 'wpsl' ) . '</span></div>',
			),
		)
	);
	return $fields;
}


/**
 * Change the form of the external product
 *
 * @since 2.7.0
 */
add_filter( 'wpsl_get_form_submit_external', 'wpsl_change_form_submit_external_pruduct', 10, 1 );
function wpsl_change_form_submit_external_pruduct( $args ) {
	global $post;
	$args = array(
		'submit'  => '<i class="icon-shopping-cart"></i> ' . wpsl_buy_caption(),
		'onclick' => 'order_' . get_post_meta( $post->ID, 'type-product', true ),
		'ajax'    => false,
		'action'  => get_post_meta( $post->ID, '_product_url', true ),
		'args'    => 'target="_blank"',
	);
	return $args;
}


/**
 * Filling out a variable product form
 *
 * @since 2.7.0
 */
add_filter( 'wpsl_get_form_variable', 'wpsl_get_form_variable_product', 10, 1 );
function wpsl_get_form_variable_product( $fields ) {
	global $post;
	$variations = get_post_meta( $post->ID, '_product_variations', true );
	$new_fields = array();
	if ( $variations ) {
		/**
		 * Show variations
		 */
		$new_fields[] = array(
			'type' => 'custom',
			'fill' => '<div class="wpsl-sep"><span>' . __( 'Select attributes', 'wpsl' ) . '</span></div>',
		);
		if ( $atts = get_post_meta( $post->ID, '_atts', true ) ) {
			$count = 0;
			foreach ( $atts as $attr ) {
				$attr = json_decode( $attr );
				if ( $attr->attribute_variable == 1 ) {
					$new_fields[] = array(
						'type'        => 'custom',
						'fill'        => '<div class="wpsl-attr next-part"><span class="wpsl-attr__name">' . __( 'Please, select', 'wpsl' ) . ' "' . $attr->attribute_name . '"</span></div>',
					);					
					$new_fields[] = array(
						'type'        => 'radio',
						'name'        => $attr->attribute_label,
						'value'       => '',
						'class'       => 'wpsl-variations',
						'required'    => 1,
						'attr'        => '',
						'args'        => explode( '|', $attr->attribute_value ),
						'notice'      => __( 'You forgot to select a feature', 'wpsl' ) . ' "' . $attr->attribute_name . '"',
					);
				}
			}
			$new_fields[] = array(
				'type'        => 'cart',
				'name'        => 'count',
				'title'       => __( 'Select count', 'wpsl' ),
				'value'       => '1',
				'class'       => 'add-cart',
				'placeholder' => __( 'Select count', 'wpsl' ),
				'required'    => 1,
				'attr'        => 'step="1" min="1"', 
				'notice'      => __( 'Notice', 'wpsl' )
			);
			$new_fields[] = array(
				'type'        => 'hidden',
				'name'        => 'saveguard',
				'value'       => ''
			);
			$new_fields[] = array(
				'type'        => 'hidden',
				'name'        => 'post_id',
				'value'       => $post->ID
			);
		}
	}
	$fields = array_merge( $fields, $new_fields );
	return $fields;
}


/**
 * Filling out a simple product form
 *
 * @since 2.7.0
 */
add_filter( 'wpsl_get_form_sponsorship', 'wpsl_get_form_sponsorship_product', 10, 1 );
function wpsl_get_form_sponsorship_product( $fields ) {
	global $post;
	$funded = get_post_meta( $post->ID, 'funded', true );
	$goal = get_post_meta( $post->ID, 'goal', true );
	$completion = get_post_meta( $post->ID, 'completion', true );
	$percent = round( (int)$funded / ( (int)$goal/100 ), 1 );
	$fields = array_merge( $fields,
		array(
			array(
				'type'        => 'custom',
				'fill'        => '<div class="wpsl-sep"><span>' . __( 'Details', 'wpsl' ) . '</span></div>',
			),
			array(
				'type'        => 'custom',
				'fill'        => '<div class="wpsl-attr"><span class="wpsl-attr__name">' . __( 'Minimum fee', 'wpsl' ) . '</span><span class="wpsl-attr__val">' . wpsl_price( get_post_meta( $post->ID, 'minimum_fee', true ) ) . '</span></div>',
			),
			array(
				'type'        => 'custom',
				'fill'        => '<div class="wpsl-attr"><span class="wpsl-attr__name">' . __( 'Funded', 'wpsl' ) . '</span><span class="wpsl-attr__val">' . wpsl_price( (int)$funded ) . '/' . $percent . '%<span class="wpsl-progress"><span class="wpsl-progress__bar" style="width: ' . $percent . '%"></span></span></span></div>',
			),
			array(
				'type'        => $completion != '' ? 'custom' : '',
				'fill'        => '<div class="wpsl-attr"><span class="wpsl-attr__name">' . __( 'Until the end of fundraising', 'wpsl' ) . '</span><span class="wpsl-attr__val">' . wpsl_diff_time( $post->post_date, get_post_meta( $post->ID, 'completion', true ) ) . '</span></div>',
			),
			array(
				'type'        => 'number',
				'name'        => '_price',
				'value'       => get_post_meta( $post->ID, 'minimum_fee', true ),
				'class'       => 'sponsorship',
				'required'    => 1,
				'placeholder' => '',
				'attr'        => 'min="' . get_post_meta( $post->ID, 'minimum_fee', true ) . '"',
				'notice'      => __( 'Notice', 'wpsl' ),
			),
			array(
				'type'        => 'hidden',
				'name'        => 'post_id',
				'value'       => $post->ID
			),
			array(
				'type'        => 'hidden',
				'name'        => 'saveguard',
				'value'       => ''
			)
		)
	);
	return $fields;
}


/**
 * Filling out a auction product form
 *
 * @since 2.7.0
 */
add_filter( 'wpsl_get_form_auction', 'wpsl_form_auction_product', 10, 1 );
function wpsl_form_auction_product( $fields ) {
	global $post, $current_user;
	$blitz_price = get_post_meta( $post->ID, 'blitz_price', true );
	$fields = array_merge( $fields,
		array(
			array(
				'type'        => 'custom',
				'fill'        => '<div class="wpsl-sep"><span>' . __( 'Until the auction ends', 'wpsl' ) . '</span></div>',
			),
			array(
				'type'        => 'custom',
				'fill'        => '<div class="wpsl-attr">' . wpsl_get_timer( $post->post_date, get_post_meta( $post->ID, 'auction_end_date', true ) ) . '</div>',
			),
			array(
				'type'        => 'number',
				'name'        => 'bid',
				'title'       => __( 'Please, make a bid', 'wpsl' ),
				'value'       => wpsl_get_bid( $post->ID ),
				'class'       => '',
				'placeholder' => __( 'Please, make a bid', 'wpsl' ),
				'required'    => 1,
				'attr'        => 'step="' . (int)get_post_meta( $post->ID, 'bid_increment', true ) . '" min="' . wpsl_get_bid( $post->ID ) . '"', 
				'notice'      => __( 'Please, make a bid', 'wpsl' )
			),
			array(
				'type'        => !is_user_logged_in() ? 'email' : 'hidden',
				'name'        => 'email',
				'title'       => !is_user_logged_in() ? __( 'Enter your email', 'wpsl' ) : '',
				'value'       => is_user_logged_in() ? $current_user->data->user_email : '',
				'class'       => '',
				'placeholder' => __( 'Enter your email', 'wpsl' ),
				'required'    => 1,
				'notice'      => __( 'Enter your email', 'wpsl' )
			),
			array(
				'type'        => get_post_meta( $post->ID, 'blitz_price', true ) != '' ? 'custom' : '',
				'fill'        => '<div class="wpsl-attr"><a href="javascript:void(0)" onclick="document.getElementById(\'bid\').value=\'' . $blitz_price . '\'">' . __( 'Buy for blitz price', 'wpsl' ) . ': ' . $blitz_price . ' ' . wpsl_opt() . '</a></div>',
			),
			array(
				'type'        => 'hidden',
				'name'        => 'post_id',
				'value'       => $post->ID
			),
			array(
				'type'        => 'hidden',
				'name'        => 'saveguard',
				'value'       => ''
			)
		)
	);
	
	// if the auction is over, or made a blitz bet, remove the form
	if ( wpsl_auction_is_ended() ) {
		if ( $current_user->data->user_login == wpsl_get_bid_leader( $post->ID ) ) {
			$fields = array(
				array(
					'type'        => 'hidden',
					'name'        => '_price',
					'value'       => get_post_meta( $post->ID, '_price', true )
				)
			);
		} else {
			$fields = '';
		}
	}
	return $fields;
}


/**
 * Change the form of the auction product
 *
 * @since 2.7.0
 */
add_filter( 'wpsl_get_form_submit_auction', 'wpsl_change_form_submit_auction_pruduct', 10, 1 );
function wpsl_change_form_submit_auction_pruduct( $args ) {
	global $current_user, $post;
	if ( wpsl_auction_is_ended() ) {
		if ( $current_user->data->user_login == wpsl_get_bid_leader( $post->ID ) ) {
			$args['submit']  = '<i class="icon-shopping-cart"></i>' . __( 'Place an order and pay' , 'wpsl' );
			$args['onclick'] = 'order_' . get_post_meta( $post->ID, 'type-product', true );
			$args['ajax']    = false;
			$args['action']  = get_permalink( wpsl_opt( 'order_page' ) );
		}
	}
	return $args;
}


/**
 * Crate notifications after cart form
 * Minimum order amount notified
 *
 * @author   wpStore
 * @since    2.2
 */
add_filter( 'wpsl_before_cart_form', 'wpsl_before_cart_minimal_amount', 10, 1 );
function wpsl_before_cart_minimal_amount( $output ) {
	$cart = WPSL_Cart::create();
	if ( $cart->getTotal() < (int)wpsl_opt( 'cart_minimum' ) ) {
		$output .= '<div class="wpsl-warning">' . __( 'Minimum order amount', 'wpsl' ) . ': ' . wpsl_price( (int)wpsl_opt( 'cart_minimum' ) ) . '. ' . __( 'Please order something else', 'wpsl' ) . '.</div>';
		return $output;
	}
}


/**
 * Displays details of a variable item in the shopping cart
 *
 * @lang: Выводит в корзине детали вариативного товара
 *
 * @since    2.7
 */
add_filter( 'wpsl_show_product_details', 'wpsl_show_variable_product_details', 10, 3 );
function wpsl_show_variable_product_details( $html, $id, $item ) {
	$parent_id = get_post_meta( $id, '_parent_id', true );
	if ( get_post_type( $id ) == 'product_variation' && $variations = wpsl_get_variations( $parent_id ) ) {
		$html .= '<div class="wpsl-detail">';
	
		foreach ( $variations as $variation ) {
			if ( $variation->item_id == $id ) {
				$details = $variation;
				break;
			}
		}
		
		$atts = array_map( 'json_decode', get_post_meta( $parent_id, '_atts', true ) );
		foreach ( $details->item_variation as $k => $v ) {
			foreach ( $atts as $attr ) {
				if ( $attr->attribute_label == $k ) {
					$html .= '<span class="wpsl-detail__item ' . $k . '"><span>' . $attr->attribute_name . ': </span><span>' . $v . '</span></span>';
					break;
				}
			}
		}			
		$html .= '</div>';
	}
	return $html;
}


/**
 * Replace text with icons in menu
 *
 * @since	2.3
 */
add_filter( 'nav_menu_link_attributes', 'wpsl_menu_override', 10, 3 );
function wpsl_menu_override( $atts, $item, $args ) {
	if ( stristr( $atts['href'], '#cart#' ) !== FALSE ) {
		$atts['href']  = get_permalink( wpsl_opt( 'cart_page' ) );
		$atts['class'] = 'wpsl-menu-item wpsl-cart icon-shopping-cart';
		$atts['title'] = $item->title;
		$item->title = $item->title . '<span class="wpsl-count-box"></span>';
	} elseif ( stristr( $atts['href'], '#loginlogout#' ) !== FALSE ) {
		$link = '';
		if ( is_user_logged_in() ) {
			$link .= wp_logout_url();
			$newtitle = __( 'Logout', 'wpsl');
			$atts['class']  = 'wpsl-menu-item wpsl-logout icon-logout';
		} elseif ( !is_user_logged_in() ) {
			$link .= wp_login_url();
			$newtitle = __( 'Login', 'wpsl');
			$atts['class']  = 'wpsl-menu-item wpsl-login icon-log-in';
		}

		$atts['href']  = str_replace( '#loginlogout#', $link, $atts['href'] );
		$atts['title'] = str_replace( 'Login / Logout', $newtitle, $item->title );
		$item->title   = str_replace( 'Login / Logout', $newtitle, $item->title );
	} elseif ( stristr( $atts['href'], '#account#' ) !== FALSE ) {
		$atts['class'] = 'wpsl-menu-item wpsl-account icon-users';
		$atts['href']  = get_permalink( wpsl_opt( 'pageaccount' ) );
		$atts['title'] = $item->title;
	}
	return $atts;
}


/**
 * @since	2.8
 */
add_filter( 'post_type_link', 'wpsl_get_permalink_change', 10, 4 );
function wpsl_get_permalink_change( $post_link, $post, $leavename, $sample ){
	if ( isset( $post->guid ) && $post->guid && $post->post_type == 'product' && ( strpos( get_option( 'product_permalink' ), '%category%' ) !== false || strpos( get_option( 'product_permalink' ), '%categories%' ) !== false ) ) {
		return $post->guid;
	}
	return $post_link;
}