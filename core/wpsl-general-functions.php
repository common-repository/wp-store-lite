<?php 
/**
 * wpStore
 *
 * All frontent functions of plugin
 *
 * @author	wpStore
 * @since	2.3.0
 */


/**
 * We make arguments for transfer to WP_Query from the parameters passed by POST or GET
 */
function wpsl_query_args() {
	$query = $_GET;
	$page = is_front_page() ? 'page' : 'paged';
	
	$args = array( 
		'post_type'      => 'product',
		'post_status'    => 'publish',
		'posts_per_page' => wpsl_opt( 'productcount', 3 ) * wpsl_opt( 'productrows', 4 ),
		'paged'          => get_query_var( $page ) ? get_query_var( $page ) : 1,
	);
	
	if ( is_tax( get_object_taxonomies( 'product' ) ) && ( is_tax( 'product_cat' ) || is_tax( 'product_tag' ) ) ) {
		$queried_object = get_queried_object();
		$args['tax_query'] = array(
			array(
				'taxonomy' => $queried_object->taxonomy,
				'field'    => 'term_id',
				'terms'    => $queried_object->term_id,
			)
		);
	}
	
	if ( isset( $query['order'] ) ) {
		$args['order'] = wpsl_clean( $query['order'] );
	}
	if ( isset( $query['orderby'] ) && ( $query['orderby'] == 'title' || $query['orderby'] == 'date' ) ) {
		$args['orderby'] = wpsl_clean( $query['orderby'] );
	}
	if ( isset( $query['orderby'] ) && ( $query['orderby'] == '_price' || $query['orderby'] == '_product_views' ) ) {
		$args['orderby'] = 'meta_value_num';
		$args['meta_key'] = wpsl_clean( $query['orderby'] );
	}
	if ( isset( $query['term_id'] ) && $query['term_id'] != '' ) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'product_cat',
				'field'    => 'term_id',
				'terms'    => (int)$query['term_id']
			)
		);
	}
	
	// exclude non meta fields
	$params = array( 'order', 'orderby', 'term_id', 'action' );
	foreach ( $params as $param ) {
		unset( $query[$param] );
	}
	
	$args['meta_query']['relation'] = 'AND';
	foreach( $query as $k => $v ) {
		if ( $k == '_price' ) {
			$price = explode( ',', $v );
			$args['meta_query'][] = array(
				'key'     => $k,
				'value'   => array( (int)trim( $price[0] ), (int)trim( $price[1] ) ),
				'type'    => 'numeric',
				'compare' => 'BETWEEN'
			);
		} else {
			$args['meta_query'][] = array(
				'key'     => $k,
				'value'   => $v
			);
		}
	}
	return $args;
}


/**
 * Clean variables using sanitize_text_field. Arrays are cleaned recursively.
 * Non-scalar values are ignored.
 *
 * @param string|array $var Data to sanitize.
 * @return string|array
 */
function wpsl_clean( $var ) {
	if ( is_array( $var ) ) {
		return array_map( 'wpsl_clean', $var );
	} else {
		return is_scalar( $var ) ? sanitize_text_field( $var ) : $var;
	}
}


/**
 * Get custom fields
 */
function wpsl_get_meta( $id, $field ) {
	$value = get_post_meta( $id, $field, true );
	if ( $value === '' ) {
		return 0;
	} else {
		return $value;
	}
}


/**
 * Options of plugin
 * With cache the function is 5-10 times faster
 *
 * @param string $opt          current option (default - currency_symbol)
 *        string $default      The option value if it is empty
 */
function wpsl_opt( $opt = '', $default = '' ) {
	
	$cache_key = 'wpsl_opt';
	if ( empty( $opt ) ) {
		$opt = 'currency_symbol';
	}
	
	if ( $cache = wp_cache_get( $cache_key ) ) {
		if ( isset( $cache[$opt] ) ) {
			return $cache[$opt];
		} else {
			return $default;
		}
	} else {
		$options = get_option( 'wpsl_option' );
		
		// set cache
		wp_cache_set( $cache_key, $options );
		if ( isset( $options[$opt] ) ) {
			return $options[$opt];
		} else {
			return $default;
		}
	}
	
}


/**
 * Get all order statuses
 * @speed - 0.0015-0.0025 sec
 *
 * @param int     $order_id         order id
 *        string  $meta_value       statuse type
 */
function wpsl_get_statuses( $meta_value = '', $order_id = '' ) {
	$args = array(
		'taxonomy'   => 'wpsl_status',
		'hide_empty' => false,
	);
	if ( isset( $meta_value ) ) {
		$args['meta_key'] = 'status_type';
		$args['meta_value'] = $meta_value;
	}
	if ( isset( $order_id ) ) {
		$args['object_ids'] = (int)$order_id;
	}
	$terms = get_terms( $args );
	return $terms;
}


/**
 * Check order status
 */
function wpsl_is_status( $order_id, $order_status ) {
	$s = array();
	if ( $statuses = wpsl_get_statuses( '', (int)$order_id ) ) {
		foreach ( $statuses as $status ) {
			$s[] = $status->slug;
		}
	}
	if ( in_array( $order_status, $s ) ) {
		return true;
	}
	return false;
}


/**
 * Set order statuses
 *
 * The function replaces the order status according to the group.
 * If you specify the status of only one group, the other groups will remain unchanged.
 *
 * @return        Returns nothing
 * @param int     $order_id       Order ID
 *        array   $statuses       An array of statuses by groups, where the key is a group and the value is a status
 *      Example:  $statuses = array(
 *					'order'    => 'completed',
 *					'payment'  => 'paid',
 *					'delivery' => 'delivered',
 *				  );
 */
function wpsl_set_statuses( $order_id, $statuses ) {
	if ( $order_id && $statuses ) {
		foreach ( $statuses as $group => $status ) {
			$old_statuses = wpsl_get_statuses( $group, (int)$order_id );
			
			$ids = array();
			array_walk( $old_statuses, function( $s ) use ( &$ids ) {
				return $ids[] = $s->term_id;
			} );
			
			// remove statuses from current group
			wp_remove_object_terms( (int)$order_id, $ids, 'wpsl_status' );
			
			wp_set_post_terms( (int)$order_id, $status, 'wpsl_status', true );
		}
	}
}
 

/*
 * Add all product tags in taxonomy product_cat
 *
 * @param $cats - id taxonomy of products (string)
 */
function wpsl_get_taxonomy_tags( $cats ) {
    global $wpdb;
    $tags = $wpdb->get_results ("SELECT DISTINCT terms2.term_id as tag_id, terms2.name as tag_name, t2.count as posts_count, null as tag_link
        FROM
            wp_posts as p1
            LEFT JOIN wp_term_relationships as r1 ON p1.ID = r1.object_ID
            LEFT JOIN wp_term_taxonomy as t1 ON r1.term_taxonomy_id = t1.term_taxonomy_id
            LEFT JOIN wp_terms as terms1 ON t1.term_id = terms1.term_id,
 
            wp_posts as p2
            LEFT JOIN wp_term_relationships as r2 ON p2.ID = r2.object_ID
            LEFT JOIN wp_term_taxonomy as t2 ON r2.term_taxonomy_id = t2.term_taxonomy_id
            LEFT JOIN wp_terms as terms2 ON t2.term_id = terms2.term_id
        WHERE
            t1.taxonomy = 'product_cat' AND p1.post_status = 'publish' AND terms1.term_id IN (". $cats .") AND
            t2.taxonomy = 'product_tag' AND p2.post_status = 'publish'
            AND p1.ID = p2.ID
        ORDER by tag_name");
    return $tags;
}
 

/*
 * Gets the minimum and maximum price of the all products in the category
 *
 * @since	2.7.0
 * @param  $term_id - ID taxonomy of products (int)
 */
function wpsl_get_minmax_prices( $term_id ) {
    global $wpdb;
	$sql = "
	SELECT MIN(CAST(meta_value as UNSIGNED)) as min_price, MAX(CAST(meta_value as UNSIGNED)) as max_price
	FROM {$wpdb->posts} 
	INNER JOIN {$wpdb->term_relationships} ON ({$wpdb->posts}.ID = {$wpdb->term_relationships}.object_id)
	INNER JOIN {$wpdb->postmeta} ON ({$wpdb->posts}.ID = {$wpdb->postmeta}.post_id) 
	WHERE 
	( {$wpdb->term_relationships}.term_taxonomy_id IN (%d) ) 
	AND {$wpdb->posts}.post_type = 'product' 
	AND {$wpdb->posts}.post_status = 'publish' 
	AND {$wpdb->postmeta}.meta_key = '_price'
	";

	$result = $wpdb->get_results( $wpdb->prepare( $sql, (int)$term_id ) );

	return $result[0];
}
 

/*
 * Get attribute values of all products in category
 *
 * @since	2.7.0
 * @param  $term_id - id taxonomy of products (int)
 * 		   $attr    - label of attribute (string)
 */
function wpsl_get_category_atts( $term_id, $attr ) {
	
	$values = '';
	
	$cache_key = 'wpsl_category_atts_' . $term_id . '_' . $attr;
	if ( $cache = wp_cache_get( $cache_key ) ) {
		return $cache;
	} else {
		global $wpdb;
		$sql = "
		SELECT meta_value
		FROM {$wpdb->posts} 
		INNER JOIN {$wpdb->term_relationships} ON ({$wpdb->posts}.ID = {$wpdb->term_relationships}.object_id)
		INNER JOIN {$wpdb->postmeta} ON ({$wpdb->posts}.ID = {$wpdb->postmeta}.post_id) 
		WHERE 
		( {$wpdb->term_relationships}.term_taxonomy_id IN (%d) ) 
		AND {$wpdb->posts}.post_type = 'product' 
		AND {$wpdb->posts}.post_status = 'publish' 
		AND {$wpdb->postmeta}.meta_key = '%s'
		";

		if ( $values = $wpdb->get_results( $wpdb->prepare( $sql, $term_id, $attr ) ) ) {
			$values = array_column( $values, 'meta_value' );
		}
		sort( $values );
		
		// set cache
		wp_cache_set( $cache_key, $values );
		
		return $values;
	}
}


/**
 * Create statuses
 *
 * @since	2.6.2
 */
function wpsl_order_statuses() {
	$statuses = apply_filters( 'wpsl_order_statuses',
		array(
			'order'     => __( 'Order status', 'wpsl' ),
			'payment'   => __( 'Payment status', 'wpsl' ),
			'delivery'  => __( 'Delivery status', 'wpsl' ),
		)
	);
	return $statuses;
}

 
/**
 * Set order name
 *
 * @since	2.7.0
 */
function wpsl_set_order_name( $user_id = '' ) {
	$type = wpsl_opt( 'numeration', 'default' );
	$args = array(
		'post_type'   => array( 'shop_order' ),
		'numberposts' => -1,
		'post_status' => 'publish',
	);
	switch ( $type ) {
		case 'default':
			$name = current_time( 'Ymd-Hi' );
			break;
		case 'start':
			$name = count( get_posts( $args ) ) + 1000 + 1;
			break;
		case 'user':
			$args['author'] = $user_id;
			$name = $user_id . '_' . ( count( get_posts( $args ) ) + 1 );
			break;
		case 'random':
			$name = substr( str_shuffle( '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ' ), 0, 8 );
			if ( get_page_by_title( $name ) ) {
				$name .= $name . substr( str_shuffle( '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ' ), 0, 2 );
			}
			break;
		case 'day':
			$args['day'] = current_time( 'd' );
			$name = current_time( 'dmY' ) . '/' . ( count( get_posts( $args ) ) + 1 );
			break;
		case 'month':
			$args['monthnum'] = current_time( 'n' );
			$name = current_time( 'mY' ) . '/' . ( count( get_posts( $args ) ) + 1 );
			break;
		case 'year':
			$args['year'] = current_time( 'Y' );
			$name = current_time( 'Y' ) . '/' . ( count( get_posts( $args ) ) + 1 );
			break;
		default:
			$name = current_time( 'Ymd-Hi' );
			break;
	}
	return apply_filters( 'wpsl_set_order_name', $name, $user_id );
}


/**
 * Get product data
 *
 * @since	2.7.0
 */
function wpsl_post( $field = '', $post_id = '', $single = true ) {
	if ( $post_id == '' ) {
		global $post;
		$post_id = $post->ID;
	}
	$metas = wp_cache_get( $post_id, 'post_meta' );
	if ( $field == '' ) {
		return $metas;
	} else {
		if ( isset( $metas[ $field ] ) ) {
			if ( $single ) {
				return maybe_unserialize( $metas[$field][0] );
			} else {
				return array_map( 'maybe_unserialize', $metas[$field] );
			}
		}
	}
}


/**
 * Get product ID
 *
 * @since	2.8.0
 */
function wpsl_product_id( $product_id ) {
	if ( $parent_id = get_post_meta( $product_id, '_parent_id', true ) ) {
		return (int)$parent_id;
	}
	return (int)$product_id;
}


/**
 * Get different time
 *
 * @since	2.7.0
 */
function wpsl_diff_time( $start_time, $end_time ) {
	$v = round( ( strtotime( $end_time ) - current_time( 'timestamp' ) ) / 86400 );
	return sprintf( _n( '%s day', '%s days', $v, 'wpsl' ), $v );
}

 
/**
 * Get different time by pecent
 *
 * @since	2.7.0
 */
function wpsl_diff_time_by_percent( $start_time, $end_time ) {
	return round( ( current_time( 'timestamp' ) - strtotime( $start_time ) ) / ( strtotime( $end_time ) - strtotime( $start_time ) ) * 100 );
}


/**
 * Seconds to time
 *
 * @since	2.7.0
 */
function wpsl_seconds2times( $seconds ) {

	$periods = array(
		'day'  => 86400,
		'hour' => 3600,
		'min'  => 60
	);
	
	$times = $new = array();
	foreach ( $periods as $k => $v ) {
		$times[$k] = floor( $seconds/$v );
		$seconds -= floor( $seconds/$v ) * $v;
	}
	$times['sec'] = $seconds;
	
	// localize
	foreach ( $times as $k => $v ) {
		switch( $k ) {
			case( 'day' ):
				$new[$k] = array( $v, trim(  preg_replace( '/\d/', '', sprintf( _n( '%s day', '%s days', $v, 'wpsl' ), $v ) ) ) );
				break;
			case( 'hour' ):
				$new[$k] = array( $v, trim(  preg_replace( '/\d/', '', sprintf( _n( '%s hour', '%s hours', $v, 'wpsl' ), $v ) ) ) );
				break;
			case( 'min' ):
				$new[$k] = array( $v, trim( preg_replace( '/\d/', '', sprintf( _n( '%s minute', '%s minutes', $v, 'wpsl' ), $v ) ) ) );
				break;
			case( 'sec' ):
				$new[$k] = array( $v, trim( preg_replace( '/\d/', '', sprintf( _n( '%s second', '%s seconds', $v, 'wpsl' ), $v ) ) ) );
				break;
		}
	}
	return $new;
}


/**
 * Get timer
 *
 * @since	2.7.0
 */
function wpsl_get_timer( $start_time, $end_time ) {
	$countdown = $html = '';
	if ( (int)current_time( 'timestamp' ) < strtotime( $end_time ) ) {
		if ( $countdown = wpsl_seconds2times( strtotime( $end_time ) - current_time( 'timestamp' ) ) ) {
			foreach ( $countdown as $k => $v ) {
				$html .= '<span class="wpsl-countdown">' . $v[0] . '<sub><small>' . $v[1] . '</small></sub></span>';
			}
		}
		
		$html .= '<span class="wpsl-timer"><span class="wpsl-timer__bar" style="width: ' . wpsl_diff_time_by_percent( $start_time, $end_time ) . '%"></span></span>';
	} else {
		$html .= '<span class="wpsl-timer">' . __( 'Auction is ended', 'wpsl' ) . '</span>';
	}
	
	return apply_filters( 'wpsl_get_timer', $html, $start_time, $end_time, $countdown );
}


/**
 * Show buy button
 *
 * @since	2.7.0
 */
function wpsl_get_bid( $post_id ) {
	if ( $current_price = (int)get_post_meta( $post_id, '_price', true ) ) {
		$step = $current_price + (int)get_post_meta( $post_id, 'bid_increment', true );
	} elseif ( $bid_list = get_post_meta( $post_id, 'bid_list', true ) ) {
		$step = (int)get_post_meta( $post_id, 'start_price', true ) + (int)get_post_meta( $post_id, 'bid_increment', true );
	} else {
		$step = (int)get_post_meta( $post_id, 'start_price', true );
	}
	return $step;
}


/**
 * Get auction liader
 *
 * @since	2.7.0
 */
function wpsl_get_bid_leader( $post_id, $return = 'leader' ) {
	$array = $list = array();
	if ( $bids = get_post_meta( $post_id, 'bid_list' ) ) {
		foreach ( $bids as $bid ) {
			$bid = explode( ':', $bid );
			$array[] = array( 'bid' => $bid[0], 'name' => $bid[1] );
		}
		$max = max( array_column( $array, 'bid' ) );
		$bids = array_column( $array, 'name', 'bid' );
		
		if ( $return == 'leader' ) {
			$list = $bids[$max];
		} else {
			$list = $bids;
		}
	}
	return apply_filters( 'wpsl_get_bid_leader', $list );
}


/**
 * Get auction liader
 *
 * @since	2.7.0
 */
function wpsl_auction_is_ended() {
	global $post;
	$price = get_post_meta( $post->ID, '_price', true );
	if ( $price && get_post_meta( $post->ID, 'blitz_price', true ) == $price || current_time( 'timestamp' ) > strtotime( get_post_meta( $post->ID, 'auction_end_date', true ) ) ) {
		return true;
	} else {
		return false;
	}
}


/**
 * Check product have attributes
 *
 * @since	2.7.0
 */
function wpsl_is_has_atts() {
	$default_atts = array( '_sku', '_length', '_width', '_height', '_atts' );
	foreach ( $default_atts as $attr ) {
		if ( wpsl_post( $attr ) ) {
			return true;
		}
	}
	return false;
}


/**
 * Get private login
 *
 * @since	2.7.0
 */
function wpsl_get_priv_login( $login, $first = 2, $last = 1 ) {
	
	if ( empty( $login ) ) return;
	
	if ( mb_strlen( $login ) > 8 ) {
		$first = 3;
		$last = 2;
	}
	$q1 = substr( $login, 0, $first ) . preg_replace( '/[\w]/', '*', substr( $login, $first, -$last ) ) . substr( $login, mb_strlen( $login ) - $last, $last );
	return $q1;
}


/**
 * Creating a unique token
 *
 * @since	2.7.0
 *
 * @param int    $post_id Post ID.
 */
function wpsl_add_token( $post_id ) {
	$token = md5( uniqid( rand(), 1 ) );
	add_post_meta( $post_id, 'token', $token, false );
	return $token;
}


/**
 * Get user id by email
 *
 * @since	2.7.0
 *
 * @param    $email
 */
function wpsl_get_user( $email ) {
	$user = get_user_by( 'email', $email );
	if ( !$user ) {
		// Такого пользователя нет, добавляем!
		$user_id = wp_create_user( $email, wp_generate_password( 8 ), $email );
	} else {
		$user_id = $user->ID;
	}
	return $user_id;
}


/**
 * Get product type
 *
 * @since	2.7.0
 */
function wpsl_product_type() {
	global $post;
	$type = get_post_meta( $post->ID, 'type-product', true );
	return $type != '' ? $type : 'simple';
}


/**
 * Show buy caption
 *
 * @since	2.7.0
 */
function wpsl_buy_caption() {
	global $post;
	$type = get_post_meta( $post->ID, 'type-product', true );
	$captions = apply_filters( 'wpsl_buy_caption',
		array(
			'simple'      => __( 'Buy', 'wpsl' ),
			'external'    => __( 'Buy', 'wpsl' ),
			'variable'    => __( 'Select', 'wpsl' ),
			'auction'     => __( 'Make a bid', 'wpsl' ),
			'sponsorship' => __( 'Fund', 'wpsl' ),
		)
	);
	if ( empty( $type ) ) {
		$type = 'simple';
	}
	return $captions[$type];
}


/**
 * Show buy caption
 *
 * @since	2.7.0
 */
function wpsl_type_product_list() {
	$types = apply_filters( 'wpsl_type_product_list',
		array( 
			'simple'      => __( 'Simple product', 'wpsl' ),
			'external'    => __( 'External/Affiliate product', 'wpsl' ),
			'variable'    => __( 'Variable product', 'wpsl' ),
			'auction'     => __( 'Auction product', 'wpsl' ),
			'sponsorship' => __( 'Sponsorship product', 'wpsl' ),
			//'configurable' => __( 'Configurable product', 'wpsl' ),
		)
	);
	return $types;
}


/**
 * Show buy button url
 *
 * @since	2.7.0
 */
function wpsl_buy_url() {
	global $post;
	$type = get_post_meta( $post->ID, 'type-product', true );
	$urls = apply_filters( 'wpsl_buy_url',
		array(
			'simple'      => get_permalink( wpsl_opt( 'cart_page' ) ) . '?id=' . $post->ID,
			'external'    => wpsl_get_permalink(),
			'variable'    => wpsl_get_permalink(),
			'auction'     => wpsl_get_permalink(),
			'sponsorship' => wpsl_get_permalink(),
		)
	);
	$post_type = $type != '' ? $type : 'simple';
	return $urls[$post_type];
}


/**
 * Show buy button
 *
 * @since	1.5
 */
function wpsl_get_buy_button() {
	global $post;
	$type = get_post_meta( $post->ID, 'type-product', true );
	echo apply_filters( 'wpsl_get_buy_button',
		sprintf( '<a rel="nofollow" href="%s" data-id="%d" data-click="%s" class="%s" >%s</a>',
			esc_url( wpsl_buy_url() ),
			esc_attr( $post->ID ),
			esc_attr( __( 'Ordering', 'wpsl' ) ),
			esc_attr( $type == 'simple' ? 'wpsl-tocart wpsl-add-to-cart' : 'wpsl-tocart' ),
			esc_html( wpsl_buy_caption() )
		),
	$post );
}


/**
 * Return an array of product variation data
 */
function wpsl_get_variations( $id ) {
	return array_map( 'json_decode', get_post_meta( $id, '_product_variations', true ) );
}


/**
 * Return an array variation's prices of product
 * Where key is id of variation, and val - is price of variation
 */
function wpsl_get_variations_prices( $id ) {
	if ( get_post_meta( $id, '_product_variations', true ) && $variations = array_map( 'json_decode', get_post_meta( $id, '_product_variations', true ) ) ) {
		foreach ( $variations as $variation ) {
			$prices[] = (int)$variation->item_price;
		}
	}
	return $prices;
}


/**
 * Formats a number with group separation
 *
 * @since	2.7.0
 */
function wpsl_get_num( $value, $precision = 2, $delimetr = '.', $thousands_sep = '' ) {
	return number_format( round( $value, $precision ), $precision, $delimetr, $thousands_sep );
}


/**
 * Format the price with a currency symbol.
 *
 * @param  float $price  Raw price.
 * @param  bool  $simbol Show simbol.
 * @param  array $args  Arguments to format a price {
 *     Array of arguments.
 *     Defaults to empty array.
 *
 *     @type string $currency           Currency code.
 *     @type string $decimal_separator  Decimal separator.
 *     @type string $thousand_separator Thousand separator.
 *     @type string $num_decimals       Number of decimals.
 *     @type string $currency_position  Currency position
 * }
 * @return string
 */
function wpsl_price( $price, $show_simbol = true ) {
	$args = array(
		'currency'           => wpsl_opt(),
		'decimal_separator'  => wpsl_opt( 'currency_decimal_sep', ',' ),
		'thousand_separator' => wpsl_opt( 'currency_thousand_sep', '' ),
		'num_decimals'       => wpsl_opt( 'currency_num_decimals', 2 ),
		'currency_position'  => wpsl_opt( 'currency_position', 'right_space' ),
	);
	
	$new_price = number_format( $price, $args['num_decimals'], $args['decimal_separator'], $args['thousand_separator'] );
	
	switch( $args['currency_position'] ) {
		case 'left':
			$return = $args['currency'] . $new_price;
			break;
		case 'right':
			$return = $new_price . $args['currency'];
			break;
		case 'left_space':
			$return = $args['currency'] . ' ' . $new_price;
			break;
		case 'right_space':
			$return = $new_price . ' ' . $args['currency'];
			break;
	}
	
	if ( $show_simbol == false ) {
		$return = $new_price;
	}

	/**
	 * Filters the string of price markup.
	 *
	 * @param string $return            Formatted price.
	 * @param string $price             Simple price.
	 * @param array  $args              Pass on the args.
	 */
	return apply_filters( 'wpsl_price', $return, $price, $args );
}


/**
 * Return min price of variable product
 */
function wpsl_get_min_price( $id ) {
	if ( get_post_meta( $id, 'type-product', true ) == 'variable' ) {
		return min( wpsl_get_variations_prices( $id ) );
	}
}


/**
 * Return max price of variable product
 */
function wpsl_get_max_price( $id ) {
	if ( get_post_meta( $id, 'type-product', true ) == 'variable' ) {
		return max( wpsl_get_variations_prices( $id ) );
	}
}


/**
 * Get product price
 *
 * @param	    id - id of product, required param
 * @param	   min - String to output only the minimum price
 * @param	   tag - String to tag-wrapper for price
 * @param	  from - String to text before price
 * @param	    to - String to text between price
 * @param currency - Boolean to show national currency
 * @param	 class - String to classes for tag
 */
function wpsl_get_price( $id, $min = false, $from = '', $to = '-', $tag = 'span', $currency = true, $class = '' ) {
	
	$price = get_post_meta( $id, '_price', true );
	$html = '';
	if ( empty( $currency ) || $currency == false ) {
		$currency = '';
	} elseif ( $currency == true ) {
		$currency = '<span class="currency">' . wpsl_opt() . '</span>';
	}
	
	if ( get_post_meta( $id, '_price', true ) == '0' && get_post_meta( $id, '_digital', true ) == '1' ) {
		
		$html .= '<' . $tag . ' class="price ' . $class . '">' . __( 'Free', 'wpsl' ) . '</' . $tag . '>';
		
	} else {
		
		if ( get_post_meta( $id, 'type-product', true ) == 'simple' ) {
			
			$html = '<' . $tag . ' class="price ' . $class . '">' . $price . '</' . $tag . '>' . $currency;
			
		} elseif ( get_post_meta( $id, 'type-product', true ) == 'variable' ) {
			
			$html = '<span class="prices">';
			$html .= $from;
			$html .= '<' . $tag . ' class="min-price ' . $class . '">' . wpsl_get_min_price( $id ) . '</' . $tag . '>';
			if ( $min == false ) {
				$html .= $to;
				$html .= '<' . $tag . ' class="max-price ' . $class . '">' . wpsl_get_max_price( $id ) . '</' . $tag . '>';
			}
			$html .= $currency;
			$html .= '</span>';
			
		}
		
	}
	return $html;
	
}


/**
 * Get prices steps
 */
function wpsl_get_price_step( $min_price, $max_price, $step = 4 ) {
	$num = ( $max_price - $min_price ) / $step;
	$number = $max_price > 1000 ? round( $num ) : round( $num, 1 );
	$steps = array(
		$min_price,
		$min_price + $number,
		$min_price + ( $number * 2 ),
		$min_price + ( $number * 3 ),
		$max_price
	);
	return $steps;
}


/**
 * Get widget option
 */
function wpsl_get_widget_opt( $widget, $option ) {
	if ( $opts = get_option( $widget ) ) {
		foreach ( $opts as $opt ) {
			if ( is_array( $opt ) && array_key_exists( $option, $opt ) ) {
				return $opt[$option];
			}
		}
	}
}


/**
 * Displaying a list of reviews
 *
 * @param	  post_id - product id
 */	
function wpsl_get_reviews( $post_id, $meta_key = '', $meta_val = '' ) {
	$args = array(
		'post_id'   => $post_id,
		'status'    => 'approve',
		'type__in'  => 'review',
	);
	if ( $meta_key != '' && $meta_val != '' ) {
		$args['meta_query'] = array(
			'relation' => 'AND',
			array(
				'key'     => $meta_key,
				'value'   => $meta_val,
			)
		);
	}
	if( $comments = get_comments( $args ) ){
		return $comments;
	} else {
		return array();
	}
}


/**
 * Get average mark
 * Do not use this function directly to display the average rating of an item. Use get_post_meta( $post, '_rate', true )
 *
 * @param	  reviews - assessment of product
 */	
function wpsl_get_average_mark( $post_id ) {
	$cache_key = 'wpsl_average_mark_' . $post_id;
	
	if ( $cache = wp_cache_get( $cache_key ) ) {
		return $cache;
	} else {
		global $wpdb;
		$average_mark = $wpdb->get_var( $wpdb->prepare( "       
			SELECT AVG(meta_value) 
			FROM $wpdb->commentmeta
			WHERE meta_key = 'assessment'
			AND comment_id IN (
				SELECT comment_id
				FROM $wpdb->comments
				WHERE comment_post_ID = %d
				AND comment_approved = 1
			)
		", $post_id ) );
		
		wp_cache_set( $cache_key, $average_mark );
		return round( $average_mark, 1 );
	}
}


/**
 * Displaying a rating
 *
 * @param	  assessment - assessment of product
 */	
function wpsl_get_rating( $assessment ) {
	$html = '';
	if( ! $assessment ) {
		$assessment = 0;
	}
	$names = apply_filters( 'wpsl_rating_names',
		array(
			'1' => __( 'Very poor', 'wpsl' ),
			'2' => __( 'Not that bad', 'wpsl' ),
			'3' => __( 'Average', 'wpsl' ),
			'4' => __( 'Good', 'wpsl' ),
			'5' => __( 'Perfect', 'wpsl' ),
		)
	);
	$title = $assessment > 0 ? __( 'Product assessment', 'wpsl' ) . ': ' . $assessment : __( 'No assessments', 'wpsl' );
	$html .= '<div class="wpsl-rate" title="' . $title . '">';
	foreach ( $names as $k => $v ) {
		$class = $k < $assessment || $k == $assessment ? ' flash' : '';
		$html .= '<i class="wpsl-rate__icon icon-star' . $class . '"></i>';
	}
	$html .= '</div>';

	return $html;
}


/**
 * Get similar products
 *
 * @param	  product_id - product id
 */	
function wpsl_get_similar( $product_id, $per_page = '5' ) {
	$category_ids = array();
	if ( $categories = get_the_category( $product_id ) ) {
		foreach( $categories as $individual_category ) {
			$category_ids[] = $individual_category->term_id;
		}
	}
	
	$products = new WP_Query(
		apply_filters( 'wpsl_get_similar',
			array(
				'category__in'     => $category_ids,
				'post__not_in'     => array( $product_id ),
				'posts_per_page'   => $per_page,
				'orderby'          => 'rand',
				'post_type'        => 'product',
			)
		)
	);
	return $products;
	
}


/**
 * Get product permalink
 */	
function wpsl_get_permalink( $product_id = '' ) {
	global $post;
	// get product id
	if ( $product_id ) {
		$id = $product_id;
	} else {
		$id = $post->ID;
	}
		
	// get product permalink
	if ( strpos( get_option( 'product_permalink' ), '%category%' ) !== false || strpos( get_option( 'product_permalink' ), '%categories%' ) !== false ) {
		if ( $product = get_post( $id ) ) {
			return $product->guid;
		} else {
			return $post->guid;
		}
	} else {
		return get_permalink( $id );
	}
}


/**
 * Get product thumbnail url
 *
 * @param	  product_id - product id
 */	
function wpsl_get_thumbnail_url( $product_id, $thumb_size = 'wpsl-medium-thumb' ) {
	if ( $thumb_id = get_post_meta( $product_id, '_thumbnail_id', true ) ) {
	 	$uploads = wp_upload_dir( null, false );
		$thumb = get_post_meta( $thumb_id );
		$array = maybe_unserialize( $thumb['_wp_attachment_metadata'][0] );
		if ( isset( $array['sizes'][$thumb_size] ) && $array['sizes'][$thumb_size] ) {
			return $uploads['baseurl'] . '/' . substr( $array['file'], 0, 8 ) . $array['sizes'][$thumb_size]['file'];
		} else {
			return $uploads['baseurl'] . '/' . $array['file'];
		}
	} else {
		return wpsl_opt( 'placeholder_image', WPSL_URL . '/assets/img/no-photo.png' );
	}
}


/**
 * Get product thumbnail
 *
 * @param	  product_id - product id
 */	
function wpsl_get_thumbnail( $product_id, $thumb_size = 'wpsl-medium-thumb', $attr = '' ) {
	return '<a target="_blank" href="' . wpsl_get_permalink( $product_id ) . '"><img src="' . wpsl_get_thumbnail_url( $product_id, $thumb_size ) . '" alt="' . get_the_title() . '" ' . $attr . ' /></a>';
}


/**
 * Get category thumbnail
 *
 * @param	  term_id - category id
 */	
function wpsl_cat_thumbnail( $term_id, $thumb_size = 'wpsl-medium-thumb' ) {
	if ( $image_url = wp_get_attachment_image_url( $term_id, $thumb_size ) ) {
		$url = $image_url;
	} else {
		$url = wpsl_opt( 'placeholder_image', WPSL_URL . '/assets/img/no-photo.png' );
	}
	return $url;
}


/**
 * Get sortbox
 *
 * @param	  product_id - product id
 */	
function wpsl_get_sort_form() {
	if ( $sorting = wpsl_opt( 'sorting_fields' ) ) {
		$fields = apply_filters( 'wpsl_get_sort_form',
			array(
				'_price' => array(
					'title'   => _x( 'Price', 'sort', 'wpsl' ),
					'class'   => 'wpsl-sort__price',
					'order'   => 'ASC',
				),
				'title' => array(
					'title'   => _x( 'Title', 'sort', 'wpsl' ),
					'class'   => 'wpsl-sort__name',
					'order'   => 'ASC',
				),
				'date' => array(
					'title'   => _x( 'Date', 'sort', 'wpsl' ),
					'class'   => 'wpsl-sort__date',
					'order'   => 'ASC',
				),
				'_product_views' => array(
					'title'   => _x( 'Popularity', 'sort', 'wpsl' ),
					'class'   => 'wpsl-sort__popularity',
					'order'   => 'DESC',
				)
			)
		);
		$taxonomy = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
		$term = $taxonomy != '' ? $taxonomy->term_id : '';
		$html = '<div id="wpsl-sort__form">';
		foreach ( $fields as $key => $arr ){
			$html .= '<span class="' . $arr['class'] . ' wpsl-sort__ajax sort" data-orderby="' . $key . '" data-order="' . $arr['order'] . '" data-default="' . $arr['order'] . '" data-term="' . $term . '">' . $arr['title'] . '</span>';
		}
		$html .= '</div>';
	}
	echo $html;
}


/**
 * Get product excerpt
 */
function wpsl_get_excerpt( $count = 15 ) {
	global $post;
	if ( $txt = get_post_meta( $post->ID, '_purchase_note', true ) ){
		$excerpt = $txt;
	} else {
		$article = get_post( $post->ID );
		$excerpt = wp_trim_words( $article->post_content, $count, '' );
	}
	return $excerpt;
}


/**
 * Show tab constructor
 *
 * @since	2.7.0
 */
function wpsl_counstructor_tabs( $args ) {
	
	$args = apply_filters( 'wpsl_counstructor_tabs', $args );
	
	if ( $args ) {
		// menu
		$tabs = '<div class="wpsl-tabs">';
		$tabs .= '<ul class="wpsl-tabs__menu">';
		$i = 0;
		foreach ( $args as $arg ) {
			if ( isset( $arg['fill'] ) && $arg['fill'] != '' ) {
				$class = $i == 0 ? 'active ' : '';
				if ( isset( $arg['class'] ) && $arg['class'] != '' ) {
					$class .= $arg['class'];
				}
				$tabs .= '<li class="' . $class . '"><i class="' . $arg['icon'] . '"></i>' . $arg['name'] . '</li>';
				$i++;
			}
		}
		$tabs .= '</ul>';
		
		// content
		$i = 0;
		$tabs .= '<div class="wpsl-tabs__content">';
		foreach ( $args as $arg ) {
			if ( isset( $arg['fill'] ) && $arg['fill'] != '' ) {
				$class = $i == 0 ? 'active' : '';
				$tabs .= '<div class="wpsl-tabs__content_item ' . $class . '">';
				if ( isset( $arg['callback'] ) && $arg['callback'] != '' ) {
					$tabs .= $arg['callback']();
				} else {
					$tabs .= $arg['fill'];
				}
				$tabs .= '</div>';
				$i++;
			}
		}
		$tabs .= '</div>';
		$tabs .= '</div>';
		
		return $tabs;
	}
}


/**
 * Email service
 *
 * @since	2.7.0
 */
function wpsl_mail_services( $email ) {
	$email = explode( '@', $email );
	$services = apply_filters( 'wpsl_mail_services',
		array(
			'gmail.com'   => array( 'Gmail', 'https://gmail.com' ),
			'yandex.ru'   => array( 'Яндекс.Почта', 'https://mail.yandex.ru' ),
			'ya.ru'       => array( 'Яндекс.Почта', 'https://mail.yandex.ru' ),
			'yandex.ua'   => array( 'Яндекс.Почта', 'https://mail.yandex.ua' ),
			'yandex.by'   => array( 'Яндекс.Почта', 'https://mail.yandex.by' ),
			'yandex.kz'   => array( 'Яндекс.Почта', 'https://mail.yandex.kz' ),
			'yandex.com'  => array( 'Yandex.Mail', 'https://mail.yandex.com' ),
			'mail.ru'     => array( 'Mail.ru', 'https://e.mail.ru' ),
			'outlook.com' => array( 'Outlook.com', 'https://outlook.live.com' ),
			'hotmail.com' => array( 'Outlook.com', 'https://outlook.live.com' ),
			'yahoo.com'   => array( 'Yahoo! Mail', 'https://mail.yahoo.com' ),
			'icloud.com'  => array( 'iCloud Mail', 'http://icloud.com/' ),
			'me.com'      => array( 'iCloud Mail', 'http://icloud.com/' ),
			'rambler.ru'  => array( 'Rambler.ru', 'https://mail.rambler.ru' ),
			'ukr.net'     => array( 'Почта ukr.net', 'https://mail.ukr.net/' ),
			'i.ua'        => array( 'Почта I.UA', 'http://mail.i.ua/' ),
		)
	);
	if ( isset( $email ) && $email[1] != '' ) {
		return $services[$email[1]];
	}
}


/**
 * Weight unit
 *
 * @since	2.7.0
 */
function wpsl_weight_unit( $unit = '' ) {
	$values = apply_filters( 'wpsl_weight_unit',
		array(
			'kg'  => __( 'kg', 'wpsl' ),
			'g'   => __( 'g', 'wpsl' ),
			'lbs' => __( 'lbs', 'wpsl' ),
			'oz'  => __( 'oz', 'wpsl' ),
		)
	);
	if ( $unit != '' ) {
		return $values[$unit];
	}
	return $values;
}


/**
 * Dimensions unit
 *
 * @since	2.7.0
 */
function wpsl_dimensions_unit( $unit = '' ) {
	$values = apply_filters( 'wpsl_dimensions_unit',
		array(
			'm'  => __( 'm', 'wpsl' ),
			'cm' => __( 'cm', 'wpsl' ),
			'mm' => __( 'mm', 'wpsl' ),
			'in' => __( 'in', 'wpsl' ),
			'yd' => __( 'yd', 'wpsl' ),
		)
	);
	if ( $unit != '' ) {
		return $values[$unit];
	}
	return $values;
}


/**
 * Get taxonomy herarical
 *
 * @since	2.7.0
 */
function wpsl_get_terms_hierarchicaly( $product_id, $tax_name ) {
 	if ( $terms = wp_get_object_terms( $product_id, $tax_name ) ) {
		$parent_id = '';
		$hierarchicaly = array();
		foreach ( $terms as $i => $array ) {
			if ( $array->parent == 0 ) {
				$parent_id = $array->term_id;
				$hierarchicaly[] = $array;
			}
		}
		foreach ( $terms as $i => $array ) {
			if ( $array->parent == $parent_id ) {
				$hierarchicaly[] = $array;
				$parent_id = $array->term_id;
			}
		}
		return $hierarchicaly;
	}
}


/**
 * Gets a list of all products purchased by the current user
 *
 * Returns an array of IDs of purchased or ordered, but not paid goods of the current user
 *
 * @params all - bool    If "false", then the id is a variation, returns the id of the parent product
 *
 * @since	2.7.0
 */
function wpsl_get_user_products( $all = true ) {
	$products = array();
	$orders = get_posts(
		array(
			'numberposts' => -1,
			'orderby'     => 'date',
			'order'       => 'DESC',
			'post_type'   => 'shop_order',
			'post_status' => 'publish',
			'author'      => get_current_user_id(),
		)
	);
	if ( $orders ) {
		foreach ( $orders as $order ) {
			$o = get_post_meta( $order->ID, 'detail', true );
			foreach ( $o as $id => $val ) {
				if ( $all == false && $parent = get_post_meta( $id, '_parent_id', true ) ) {
					$products[] = $parent;
				} else {
					$products[] = $id;
				}
			}
		}
		$products = array_unique( $products );
	}
	return $products;
}


/**
 * Get user review list
 *
 * @since	2.7.0
 */
function wpsl_get_user_review( $fields = array() ) {
	$reviews = array();
	$args = array(
		'numberposts' => -1,
		'orderby'     => 'date',
		'order'       => 'DESC',
		'type'        => 'review',
		'post_status' => 'publish',
		'user_id'     => get_current_user_id(),
	);
	
	if ( $fields ) {
		$args = array_merge( $args, $fields );
	}
	
	if ( $comments = get_comments( $args ) ) {
		return $comments;
	}
	return $reviews;
}


/**
 * Get user review list
 *
 * @since	2.7.0
 */
function wpsl_get_user_reviews() {
	
	$list = $products = array();
	if ( $comments = wpsl_get_user_review() ) {
		$products = wpsl_get_user_products( false );
		foreach ( $comments as $comment ) {
			$list[$comment->comment_post_ID] = array(
				'comment_ID' => $comment->comment_ID,
				'real_buyer' => in_array( $comment->comment_post_ID, $products ) ? true : false,
			);
			if( ( $key = array_search( $comment->comment_post_ID, $products ) ) !== FALSE ){
				 unset( $products[$key] );
			}
		}
	}
	
	if ( $products ) {
		foreach ( $products as $product ) {
			$list[$product] = array(
				'comment_ID' => '',
				'real_buyer' => '',
			);
		}
	}
	return $list;
}


/**
 * Get review
 *
 * @since	2.7.0
 */
function wpsl_get_review( $comment_id ) {
	return sprintf( '%s %s %s', get_comment_text( $comment_id ), get_comment_meta( $comment_id, 'plus', true ), get_comment_meta( $comment_id, 'minus', true ) );
}


/**
 * Gets the amount of all paid orders
 *
 * @since	2.7.0
 */
function wpsl_orders_total() {
	$orders = get_posts(
		array( 
			'numberposts' => -1,
			'wpsl_status' => 'paid',
			'post_type'   => 'shop_order',
		)
	);
	$total = 0;
	if ( $orders ) {
		foreach ( $orders as $order ) {
			$price = get_post_meta( $order->ID, 'summa', true );
			$total += $price;
		}
	}
	return $total;
}


/**
 * Recursively sort an array of taxonomy terms hierarchically. Child categories will be
 * placed under a 'children' member of their parent term.
 * @param Array   $cats     taxonomy term objects to sort
 * @param Array   $into     result array to put them in
 * @param integer $parentId the current parent ID to put them in
 */
function wpsl_sort_terms_hierarchicaly( & $cats, & $into, $parentId = 0 ){
	foreach( $cats as $i => $cat ){
		if( $cat->parent == $parentId ){
			$into[ $cat->term_id ] = $cat;
			unset( $cats[$i] );
		}
	}

	foreach( $into as $top_cat ){
		$top_cat->children = array();
		wpsl_sort_terms_hierarchicaly( $cats, $top_cat->children, $top_cat->term_id );
	}
}


/**
 * wpStore
 *
 * Counts PHP code execution time (in seconds)
 *
 * wpsl_exec_time();
 * // code
 * echo wpsl_exec_time('get'); //> 0.03654 sec
 *
 * @param string $phase       get/getall/pause/clear
 *                            get    - получает разницу, между предыдущим вызовом функции.
 *                            getall - получает разницу, между первым вызовом функции (run).
 *                            pause  - временная остановка подсчета. exec_time() для продолжения.
 *                            clear  - полностью очищает результат. exec_time() для начала нового подсчета.
 * @param int    $round       До скольки знаков после запятой округлять результат.
 *
 * @return float
 *
 * @ver: 3.3
 * @author: https://wp-kama.ru/question/izmerenie-skorosti-rabotyi-php-sripta
 */
function wpsl_exec_time( $phase = 'run', $round = 8 ){
	static $prev_time, $collect;

	$exectime = $memory = '';
	
	if( $phase === 'clear' )
		return $collect = $prev_time = 0;

	list( $usec, $sec ) = explode(' ', microtime() );
	$microtime = bcadd( $usec, $sec, 8 );

	if( $prev_time ){
		$exectime = bcsub( $microtime, $prev_time, 8 );
		$collect  = bcadd( $collect, $exectime, 8 );
	}
	$prev_time = $microtime;

	if( $phase === 'pause' )
		$prev_time = 0;

	if( $phase === 'get' )
		if ( function_exists('memory_get_usage') ) {
			$memory = round( memory_get_usage()/1024/1024, 2 ) . 'MB';
		}
		return round( $exectime, $round ) . ' sec / ' . $memory;

	if( $phase === 'getall' )
		return round( $collect, $round );
}


/**
 * Returns an array of product availability statuses
 */
function wpsl_get_stock_status( $status = '' ){
	$statuses = apply_filters( 'wpsl_get_stock_status',
		array(
			'instock'    => __( 'In stock', 'wpsl' ),
			'outofstock' => __( 'Out of stock', 'wpsl' ),
			'preorder'   => __( 'Pre order', 'wpsl' ),
		)
	);
	if ( $status ) {
		return $statuses[$status];
	}
	return $statuses;
}