<?php
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Output product image gallery
 *
 * @author	wpStore
 * @since	2.7
 */
add_action( 'wpsl_single_product_gallery', 'wpsl_single_product_gallery_box', 10 );
function wpsl_single_product_gallery_box() {
	wpsl_get_template( 'single', 'product-image' );
}


/**
 * Output product attributes
 *
 * @author	wpStore
 * @since	2.7
 */
add_action( 'wpsl_single_product_attributes', 'wpsl_single_product_attributes_box', 20 );
function wpsl_single_product_attributes_box() {
	wpsl_get_template( 'single', 'product-side' );
}


/**
 * Output product tabs
 *
 * @author	wpStore
 * @since	2.7
 */
add_action( 'wpsl_single_product_tabs', 'wpsl_single_product_tabs_box', 30 );
function wpsl_single_product_tabs_box() {
	wpsl_get_template( 'single', 'product-tabs' );
}


/**
 * Output similar products on product page
 *
 * @author	wpStore
 * @since	2.7
 */
add_action( 'wpsl_single_product_similar', 'wpsl_single_product_similar_box', 40 );
function wpsl_single_product_similar_box() {
	wpsl_get_template( 'single', 'product-similar' );
}


/**
 * Output of product page
 *
 * @author	wpStore
 * @since	2.7
 */
add_action( 'wpsl_single_product', 'wpsl_single_product_page', 10 );
function wpsl_single_product_page() {
	do_action( 'wpsl_single_product_gallery' );
	do_action( 'wpsl_single_product_attributes' );
	do_action( 'wpsl_single_product_tabs' );
	do_action( 'wpsl_single_product_similar' );
}


/**
 * Show product icon new
 *
 * @since	2.7
 */
add_action( 'wpsl_product_icons', 'wpsl_new_product', 10 );
function wpsl_new_product() {
	if ( $post_time = wpsl_opt( 'newproduct' ) ) {
		$post_time = $post_time * 86400;                                  // продолжительность в секундах когда товар считаем новым
		$data_new_1 = strtotime( current_time( 'mysql' ) );               // получаем текущее время сайта и преобразовываем формат даты в секундах
		$data_new_2 = strtotime( get_post_time( 'd-m-Y H:s', true ) );    // получаем время поста и преобразовываем формат даты в секундах
		$data_new_3 = $data_new_1 - $data_new_2;                          // считаем разницу текущей даты и даты публикации записи

		if( $post_time > $data_new_3 ){
			echo '<span class="wpsl-icon__new" title="' . __( 'New product', 'wpsl' ) . '"><img width="35px" height="35px" src="' . wpsl_opt( 'new_icon', WPSL_URL . '/assets/img/new.svg' ) . '"></span>
';
		}
	}
}


/**
 * Show best seller icon
 *
 * @since	2.7
 */
add_action( 'wpsl_product_icons', 'wpsl_best_seller_product', 20 );
function wpsl_best_seller_product() {
	global $post;
	if ( get_post_meta( $post->ID, 'hit_product', true ) == 'on' ) {
		echo '<span class="wpsl-icon__hit" title="' . __( "Best seller", "wpsl" ) . '"><img width="35px" height="35px" src="' . wpsl_opt( 'best_icon', WPSL_URL . '/assets/img/bestseller.svg' ) . '"></span>';
	}
}


/**
 * Disable Emoji
 *
 * @since 2.0
 */
add_action( 'init', 'wpsl_disable_emoji' );
function wpsl_disable_emoji() {
	if ( wpsl_opt( 'emoji' ) != true ) return;
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	add_filter( 'emoji_svg_url', '__return_empty_string' );
}


/**
 * Leaves the user on the same page when entering an incorrect login/password in the authorization form wp_login_form()
 *
 * @since 2.7.0
 */
add_action( 'wp_login_failed', 'wpsl_front_end_login_fail' );
function wpsl_front_end_login_fail( $username ) {
	$referrer = $_SERVER['HTTP_REFERER'];

	// If there is a referrer and it is not a wp-login.php
	if( !empty($referrer) && !strstr($referrer,'wp-login') && !strstr($referrer,'wp-admin') ) {
		wp_redirect( add_query_arg( 'action', 'failed', $referrer ) );  // redirect and add a query parameter ?action=failed
		exit;
	}
}


/**
 * Filling details of auction product
 *
 * @since 2.7.0
 */
add_action( 'wpsl_before_excerpt_box', 'wpsl_before_excerpt_box_auction_details' );
function wpsl_before_excerpt_box_auction_details() {
	global $post;
	if ( get_post_meta( $post->ID, 'type-product', true ) == 'auction' ) {
		$leader = wpsl_get_priv_login( wpsl_get_bid_leader( $post->ID ) );
		?>
		<div class="wpsl-attr wpsl-bidlist">
			<div class="wpsl-bidlist__item"><i class="icon-users"></i><?php _e( 'Made bets', 'wpsl' ); ?>: <?php echo count( get_post_meta( $post->ID, 'bid_list' ) ); ?></div>
			<div class="wpsl-bidlist__item"><i class="icon-award"></i><?php _e( 'Now leading', 'wpsl' ); ?>: <?php echo $leader != '' ? $leader : '—'; ?></div>
		</div>
		<?php
	}
}


/**
 * Display a notification about the start or end of the auction
 *
 * @since 2.7.0
 */
add_action( 'wpsl_before_buy_box', 'wpsl_notif_about_start_or_end_auction', 10 );
function wpsl_notif_about_start_or_end_auction() {
	global $post;
	if ( is_singular( 'product' ) && get_post_meta( $post->ID, 'type-product', true ) == 'auction' ) {
		if ( wpsl_auction_is_ended() ) {
			$winner = wpsl_get_priv_login( wpsl_get_bid_leader( $post->ID ) );
			?>
			<div class="wpsl-sep"><span><?php _e( 'Auction is ended', 'wpsl' ); ?></span></div>
			<div class="wpsl-attr">
				<div class="<?php echo $winner != '' ? 'wpsl-winner' : 'wpsl-winner no'; ?>"><i class="icon-award"></i><?php _e( 'Winner', 'wpsl' ); ?>: <?php echo $winner != '' ? $winner : __( 'No winner', 'wpsl' ); ?></div>
			</div>
			<?php
		}
	}
}


/**
 * Confirm bid
 */
add_action( 'wpsl_single_product', 'wpsl_bid_confirmation', 1 );
function wpsl_bid_confirmation() {
	if( isset( $_GET['action'] ) && $_GET['action'] == 'bid_confirmation' && is_singular( 'product' ) ) {
		foreach ( $_GET as $k => $v ) {
			$key = wpsl_clean( $k );
			$get[$key] = wpsl_clean( $v );
		}
		$token = get_post_meta( wpsl_clean( (int)$get['product_id'] ), 'token' );
		if ( in_array( $get['token'], $token ) ) {
			$user_id = wpsl_get_user( $get['email'] );
			$old = (int)get_post_meta( (int)$get['product_id'], '_price', true );
			update_post_meta( (int)$get['product_id'], '_price', (int)$get['bid'], $old );
			add_post_meta( (int)$get['product_id'], 'bid_list', (int)$get['bid'] . ':' . $get['email'], false );
			delete_post_meta( (int)$get['product_id'], 'token', $get['token'] );
			?>
			<div class="wpsl-notify wpsl-hidden"><?php echo sprintf( __( 'The bet is made. You can log in to %s', 'wpsl' ), '<a href="' . get_permalink( wpsl_opt( 'pageaccount' ) ) . '" target="_blank">' . __( 'personal account', 'wpsl' ) . '</a>' ); ?></div>
			<?php
		}
	}
}


/**
 * Sending SMS after successful submission of the order
 *
 * @author   wpStore
 * @since    2.7
 */
add_action( 'wpsl_order_checkout _successful', 'wpsl_send_sms_after_order', 10, 1 );
function wpsl_send_sms_after_order( $order_id ) {
	// send sms to admin
	if ( wpsl_opt( 'smsinforming' ) == '1' ) {
		wpsl_send_sms( wpsl_opt( 'phone_admin' ), sprintf( __( '[%s] Received a new order №: %s', 'wpsl' ), wpsl_opt( 'email_from_name' ), get_the_title( $order_id ) ) );
	}

	// send sms to customer
	if ( $user_phone = get_post_meta( $order_id, 'phone', true ) && wpsl_opt( 'smstobuyer' ) == '1' ) {
		wpsl_send_sms( $user_phone, wpsl_opt( 'smstobuyer_text' ) );
	}
}


/**
 * Counting the number of visits to the product pages
 *
 * Есть какая-то проблема с обновлением произвольного поля через update_post_meta: работает слишком медленно
 * Разработчик, исправь меня!
 *
 * @since	1.3
 */
//add_action( 'wp_head', 'wpsl_product_views_counter' );
function wpsl_product_views_counter() {
	$meta_key     = '_product_views';    // Metakey
	$who_count    = 0;                   // Whose visits count? 0 - All. 1-guests only. 2 - for registered users Only.
	$exclude_bots = 1;                   // Exclude bots, robots, spiders and other : )? 0-no, let them count too. 1-Yes, exclude from counting.

	global $user_ID, $post;
	if( is_singular( 'product' ) ) {
		$id = ( int )$post->ID;
		static $post_views = false;
		if( $post_views ) return true;
		$post_views = ( int )get_post_meta( $id,$meta_key, true );
		$should_count = false;
		switch( ( int )$who_count ) {
		case 0: $should_count = true;
			break;
		case 1:
			if( ( int )$user_ID == 0 )
				$should_count = true;
			break;
		case 2:
			if( ( int )$user_ID > 0 )
				$should_count = true;
			break;
		}
		if( ( int )$exclude_bots==1 && $should_count ){
			$useragent = $_SERVER['HTTP_USER_AGENT'];
			$notbot = "Mozilla|Opera";
			$bot = "Bot/|robot|Slurp/|yahoo";
				if ( !preg_match( "/$notbot/i", $useragent ) || preg_match( "!$bot!i", $useragent ) ){
					$should_count = false;
				}
		}

		if( $should_count ) {
			update_post_meta( $id, $meta_key, ( $post_views + 1 ) );
		}
	}
	return true;
}


/**
 * We make actions with products in the order at change of the status of the order
 *
 * @since	2.7
 */
add_action( 'edit_post_orders', 'wpsl_change_order', 10, 2 );
add_action( 'save_post_orders', 'wpsl_change_order', 10, 2 );
function wpsl_change_order( $order_ID, $post ) {
	if( !is_admin() ) return;
	do_action( 'wpsl_change_order_product', $order_ID, $post );
}


/**
 * Create mobile menu
 *
 * @since	1.5
 */
add_action( 'wp_footer', 'wpsl_create_mobile_menu', 10 );
function wpsl_create_mobile_menu() {
	if ( wpsl_opt( 'mobile_menu' ) == '1' && $vals = wpsl_opt( 'mobile' ) ) : ?>
	<div class="wpsl-mobile">
		<div class="wpsl-mobile__box" style="display: none;">
			<div class="wpsl-mobile__box_head"><span class="wpsl-header"><h3></h3></span><i class="wpsl-close icon-x"></i></div>
			<div class="wpsl-mobile__box_body"></div>
		</div>
		<div class="wpsl-mobile__menu">
			<?php
			foreach ( $vals as $key ) {
				switch ( $key ) {
					case( 'search' ):
						echo '<div class="wpsl-mobile__menu_item active" data-type="search" data-title="' . __( 'Search of products', 'wpsl' ) . '"><i class="icon-search"></i></div>';
						break;
					case( 'filter' ):
						if ( is_active_widget( 0, 0, 'wpsl_product_filter' ) ) {
							echo '<div class="wpsl-mobile__menu_item active" data-type="filter" data-title="' . __( 'Filter of products', 'wpsl' ) . '"><i class="icon-filter"></i></div>';
						}
						break;
					case( 'account' ):
						if ( !is_user_logged_in() ) {
							echo '<a class="wpsl-mobile__menu_item" href="' . wp_login_url() . '" rel="nofollow noindex"><i class="icon-log-in"></i></a>';
						} else {
							echo '<a class="wpsl-mobile__menu_item" href="' . get_permalink( wpsl_opt( 'pageaccount' ) ) . '"><i class="icon-users"></i></a>';
						}
						break;
					case( 'address' ):
						echo '<div class="wpsl-mobile__menu_item active" data-type="address" data-title="' . __( 'Our addresses', 'wpsl' ) . '"><i class="icon-map-pin"></i></div>';
						break;
					case( 'phone' ):
						if ( wpsl_opt( 'phone' ) != '' ) {
							echo '<a class="wpsl-mobile__menu_item icon-phone-outline" href="tel:' . wpsl_opt( 'phone' ) . '" rel="nofollow noindex"><i class="icon-phone"></i></a>';
						}
						break;
					case( 'cart' ):
						echo '<a class="wpsl-mobile__menu_item" href="' . get_permalink( wpsl_opt( 'cart_page' ) ) . '" rel="nofollow noindex"><i class="icon-shopping-cart"></i><span class="wpsl-count-box"></span></a>';
						break;
				}
			}
			?>
			<a class="wpsl-mobile__menu_item" href="<?php home_url(); ?>"><i class="icon-home"></i></a>
		</div>
	</div>
	<?php endif;
}


/**
 * Notification if a link to a digital product is not found
 *
 * @since	2.7
 */
function wpsl_file_not_found( $content ) {
	$content = __( 'File not found, please contact the seller', 'wpsl' ) . $content;
	return $content;
}


/**
 * Notification if token is ivalid
 *
 * @since	2.7
 */
function wpsl_invalid_token( $content ) {
	$content = __( 'Invalid token, please contact the seller', 'wpsl' ) . $content;
	return $content;
}


/**
 * File for generate unique one time url for download digital products
 *
 * @since	2.3
 */
add_action( 'init', 'wpsl_download_file', 99999 );
function wpsl_download_file() {
	// Показываем сообщение, если неправильный токен
	if ( isset( $_GET['download'] ) && isset( $_GET['token'] ) && in_array( wpsl_clean( $_GET['token'] ), get_post_meta( wpsl_clean( (int)$_GET['download'] ), 'token' ) ) ) {
		if ( $file = get_post_meta( wpsl_clean( (int)$_GET['download'] ), '_upload_file', true ) ) {
			header( 'Content-Description: File Transfer' );
			header( 'Content-Type: application/octet-stream' );
			header( 'Content-Disposition: attachment; filename="' . basename( $file ) . '"' );
			header( "Content-Transfer-Encoding: binary" );
			header( 'Pragma: no-cache');
			header( 'Expires: 0');
			header( 'Content-Length: ' . filesize( $file ) );
			header( 'Accept-Ranges: bytes' );
			header( 'Content-Type: application/octet-stream' );
			readfile( $file );
			delete_post_meta( wpsl_clean( (int)$_GET['download'] ), 'token', wpsl_clean( $_GET['token'] ) );
			exit;
		} else {
			//add_filter( 'the_content', 'wpsl_file_not_found', 1, 1 );
		}
	} else {
		//add_filter( 'the_content', 'wpsl_invalid_token', 1, 1 );
	}
}


/**
 * We make actions with products in the order at change of the status of the order
 *
 * @since	2.7.0
 */
add_action( 'wpsl_change_order_product', 'wpsl_change_order_product', 10, 2 );
function wpsl_change_order_product( $order_ID, $post ) {

	if ( empty( $order_ID ) ) return;

	// Get order status
	$order_statuses = wpsl_get_statuses( null, $order_ID );
	$order_products = get_post_meta( $order_ID, 'detail', true );
	$slugs = array();
	array_walk( $order_statuses, function( $s ) use ( &$slugs ) {
		return $slugs[] = $s->slug;
	} );

	// If the item has moved to the paid status
	if ( in_array( 'paid', $slugs ) && $order_products ) {
		foreach( $order_products as $id => $product ) {
			// если спонсорский товар
			if ( isset( $product_id ) && get_post_meta( wpsl_product_id( $product_id ), 'type-product', true ) == 'sponsorship' ) {
				$old = get_post_meta( wpsl_product_id( $product_id ), 'funded', true );
				update_post_meta( wpsl_product_id( $product_id ), 'funded', (int)$old + (int)$product['WPSL_PRICE'] );
			}
		}
	}
}
