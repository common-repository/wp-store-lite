<?php
if ( ! defined( 'ABSPATH' ) ) exit;  


if ( !wp_doing_ajax() ) return;


/**
 * wpStore
 *
 * File for work ajax in frontend
 *
 * @author	wpStore
 * @since	2.1
 */
class WPSL_Ajax_Frontend {
	/**
	 * Class constructor
	 */
	public function __construct() {
		
		if( !wp_doing_ajax() ) return;

		add_action( 'wp_ajax_nopriv_check_phone',      array( &$this, 'check_phone' ) );
		add_action( 'wp_ajax_check_phone',             array( &$this, 'check_phone' ) );

		add_action( 'wp_ajax_nopriv_download',         array( &$this, 'product_download_count' ) );
		add_action( 'wp_ajax_download',                array( &$this, 'product_download_count' ) );

		add_action( 'wp_ajax_nopriv_smart_search',     array( &$this, 'smart_search' ) );
		add_action( 'wp_ajax_smart_search',            array( &$this, 'smart_search' ) );

		add_action( 'wp_ajax_nopriv_mobile_menu',      array( &$this, 'mobile_menu' ) );
		add_action( 'wp_ajax_mobile_menu',             array( &$this, 'mobile_menu' ) );

		add_action( 'wp_ajax_nopriv_product_count',    array( &$this, 'product_count' ) );
		add_action( 'wp_ajax_product_count',           array( &$this, 'product_count' ) );

		//add_action( 'wp_ajax_nopriv_filter_product_count', array( &$this, 'filter_product_count' ) );
		
		add_action( 'wp_ajax_nopriv_get_products',     array( &$this, 'get_products' ) );
		add_action( 'wp_ajax_get_products',            array( &$this, 'get_products' ) );
		
		add_action( 'wp_ajax_nopriv_filter_product_count', array( &$this, 'filter_products' ) );
		add_action( 'wp_ajax_filter_product_count',        array( &$this, 'filter_products' ) );
		
		add_action( 'wp_ajax_update_profile',          array( &$this, 'update_user_profile' ) );
		
		add_action( 'wp_ajax_select_order',            array( &$this, 'update_select_order' ) );
		
		add_action( 'wp_ajax_add_ticket',              array( &$this, 'add_ticket_for_technical_support' ) );
		
		add_action( 'wp_ajax_get_ticket',              array( &$this, 'get_ticket_details' ) );
		
		add_action( 'wp_ajax_regenerate_link',         array( &$this, 'regenerate_link_download_digital_product' ) );
		
		add_action( 'wp_ajax_get_order',               array( &$this, 'get_order_details' ) );
		
		add_action( 'wp_ajax_nopriv_contact_form',     array( &$this, 'send_message_from_contactform' ) );
		add_action( 'wp_ajax_contact_form',            array( &$this, 'send_message_from_contactform' ) );
		
		add_action( 'wp_ajax_nopriv_send-review',      array( &$this, 'send_product_review' ) );
		add_action( 'wp_ajax_send-review',             array( &$this, 'send_product_review' ) );
		
		add_action( 'wp_ajax_nopriv_change_cart',      array( &$this, 'change_cart' ) );
		add_action( 'wp_ajax_change_cart',             array( &$this, 'change_cart' ) );

		add_action( 'wp_ajax_nopriv_send_auction',     array( &$this, 'make_bid' ) );
		add_action( 'wp_ajax_send_auction',            array( &$this, 'make_bid' ) );

		add_action( 'wp_ajax_nopriv_send_simple',      array( &$this, 'add_simple_product_to_cart' ) );
		add_action( 'wp_ajax_send_simple',             array( &$this, 'add_simple_product_to_cart' ) );
		
		add_action( 'wp_ajax_nopriv_send_variable',    array( &$this, 'add_variable_product_to_cart' ) );
		add_action( 'wp_ajax_send_variable',           array( &$this, 'add_variable_product_to_cart' ) );
		
		add_action( 'wp_ajax_nopriv_send_sponsorship', array( &$this, 'sponsorship_product_to_cart' ) );
		add_action( 'wp_ajax_send_sponsorship',        array( &$this, 'sponsorship_product_to_cart' ) );
	}
	
	/**
	 * Check phone number
	 */
	public function check_phone() {
		// check phone
		if ( isset( $_POST['phone'] ) ) {
			
			// change code
			$options = maybe_unserialize( get_option( 'wpsl_option' ) );
			$code = rand( 1000, 9999 );
			foreach( $options as $key ){
				$options['sms_code'] = $code;
			}
			update_option( 'wpsl_option', $options );
			
			// send sms
			$result = wpsl_send_sms( $_POST['phone'], sprintf( __( 'Your verification code: %s', 'wpsl' ), $code ), true );
		} else {
			$result = __( 'The password is not sent. Check that the phone number is entered correctly.', 'wpsl' );
		}
		wp_die( $result );
	}
	
	/**
	 * Counting the number of product downloads
	 */
	public function product_download_count() {
		$count = get_post_meta( (int)$_REQUEST['post_id'], 'product_download', true );
		$count++;
		update_post_meta( (int)$_REQUEST['post_id'], 'product_download', $count );
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) { 
			echo $count;
			die();
		} else {
			wp_redirect( get_permalink( (int)$_REQUEST['post_id'] ) );
			exit();
		}
	}
	
	/**
	 * Smart search
	 */
	public function smart_search() {
		global $post;
		echo '<li>' . __( 'Products', 'wpsl' ) . ': <span class="wpsl-searchbox__close"><i class="icon-x"></i></span></li>';
		$query = new WP_Query(
			array( 
				'post_type'        => 'product', 
				'post_status'      => 'publish', 
				'order'            => 'DESC', 
				'orderby'          => 'date', 
				's'                => $_POST['term'], 
				'posts_per_page'   => 7
			)
		); 
		if( $query->have_posts() ) {
			while ( $query->have_posts() ) {
			$query->the_post();
			?>
			<li>
				<span class="wpsl-thumb">
					<?php echo wpsl_get_thumbnail( $post->ID, 'wpsl-small-thumb' ); ?>
				</span>
				<span class="wpsl-result">
					<span class="wpsl-result__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></span>
					<span class="wpsl-result__price"><?php echo get_post_meta( $post->ID, '_price', true ) . ' '; echo wpsl_opt(); ?></span>
					<span class="wpsl-result__button"><?php echo wpsl_get_buy_button(); ?></span>
				</span>
			</li>
			<?php
			}
		} else {
			echo '<li>' . __( 'Nothing found, try another query', 'wpsl' ) . '</li>';
		} exit;
	}
	

	/**
	 * Mobile menu
	 *
	 * @since 2.3
	 */
	public function mobile_menu(){
		switch( $_POST['type'] ) {
		case 'search':
			the_widget( 'WPSL_Widget_Search' );
			break;
		case 'filter':
			the_widget( 'WPSL_Widget_Filter' );
			break;
		case 'address':
			$post_id = get_post( wpsl_opt( 'contacts' ) );
			echo '<div class="wpsl-content">' . apply_filters( 'the_content', $post_id->post_content ) . '</div>';
			break;
		}
		wp_die();
	}

	/**
	 * Count of products, output in the mini-cart
	 *
	 * @since 2.3
	 */
	function product_count(){
		if ( !session_id() ) {
			@session_start();
		}
		if ( isset( $_SESSION['WPSL_ITEMS'] ) ) {
			$count = count( $_SESSION['WPSL_ITEMS'] );
			if ( $count > 0 ) {
				echo '<span class="wpsl-count-product">' . $count . '</span>';
			}
		}
		wp_die();
	}
	
	/**
	 * Count of products from the filter
	 *
	 * Collect values to form a query via WP Query
	 * Pure SQL query get by WP Query is used in the class-wpsl-ajax-shortinit.php
	 * This method is reserved only for debugging pure SQL used in the file class-wpsl-ajax-shortinit.php
	 * And it is not used for the ordinary work of the plugin
	 *
	 * @since 2.3
	 */
	public function filter_product_count() {
		
		wpsl_exec_time();
		parse_str( $_POST['str'], $args );
		
		if ( $args ) {
			/**
			 * Collect custom meta fields and add to request
			 */
			$items['meta_query']['relation'] = 'AND';
			foreach( $args as $k => $v ) {
				if ( $k == '_price' ) {
					$price = explode( ',', $v );
					$items['meta_query'][] = array(
						'key'     => $k,
						'value'   => array( (int)trim( $price[0] ), (int)trim( $price[1] ) ),
						'type'    => 'numeric',
						'compare' => 'BETWEEN'
					);
				} elseif ( $k != '_price' && $k != 'term_id' ) {
					$items['meta_query'][] = array(
						'key'     => $k,
						'value'   => $v
					);
				}
			}
			
			/**
			 * Add product category id to the request
			 */
			if ( $args['term_id'] ) {
				$items['tax_query'] = array( 
					array( 
						'taxonomy' => 'product_cat',
						'field'    => 'term_id',
						'terms'    => array( $args['term_id'] ),
						'operator' => 'IN'
					)
				);
			}
			
			$filter_query = array_merge(
				array( 
					'post_type'              => 'product',
					'post_status'            => array( 'publish', 'private' ),
					'posts_per_page'         => -1,
					'no_found_rows'          => true,
					'update_post_term_cache' => false,
					'update_post_meta_cache' => false,
					'cache_results'          => false
				),
				$items
			);
			
/* 			echo '<pre>';
			print_r( $filter_query );
			echo '</pre>'; */
		}
		
		// отключаем объектное кеширование
		// запомним текущее состояние
		// отключаем кэширование
		$was_suspended = wp_suspend_cache_addition();
		wp_suspend_cache_addition( true );
		
		$res_search = new WP_Query( $filter_query );
		
		//print_r( $res_search->request );
		//$count = $res_search->post_count;
		// сбрасываем в исходное состояние
		wp_reset_query();
		
		// вернем состояние кэша обратно
		wp_suspend_cache_addition( $was_suspended );
		
		echo '<div class="wpsl-filter__result_box">';
		echo '<div>' . sprintf( __( 'Found: %d', 'wpsl' ), count( $values ) ) . '</div>';
		echo '<input type="submit" class="wpsl-filter__submit_btn" value="' . __( 'Show', 'wpsl' ) . '">';
		//echo wpsl_exec_time('get');
		echo '</div>';
		wp_die();		
	}
	
	/**
	 * Sort products by price, popularity, date and name
	 */
	public function get_products() {
		$products = new WP_Query( wpsl_query_args() );
		if ( $products->have_posts() ) {
			while ( $products->have_posts() ) {
				$products->the_post();
				wpsl_get_template( 'loop', 'product' );
			}
		} else {
			wpsl_get_template( 'loop', 'no-products-found' );
		}
		
		wp_reset_postdata();
		
		set_query_var( 'products', $products );
		
		wpsl_get_template( 'loop', 'product-pagi' );

		wp_reset_query();
		
		wp_die();
	}
	
	/**
	 * Count of products from the filter
	 *
	 * Collect values to form a query via WP Query
	 * Pure SQL query get by WP Query is used in the class-wpsl-ajax-shortinit.php
	 * This method is reserved only for debugging pure SQL used in the file class-wpsl-ajax-shortinit.php
	 * And it is not used for the ordinary work of the plugin
	 *
	 * @since 2.3
	 */
	public function filter_products() {
		wpsl_exec_time();
		
		foreach ( $_GET as $k => $val ) {
			$args[$k] = wpsl_clean( $val );
		}
		$values = array();
		
		if ( $args ) {
			global $wpdb;
			$price_query = $meta_query = $meta_inner = '';
			
			$i = 1;
			foreach( $args as $k => $v ) {
				if ( $k == '_price' ) {
					$price = explode( ',', $v );
					$price_query .= "( {$wpdb->postmeta}.meta_key = '_price' AND CAST( {$wpdb->postmeta}.meta_value AS SIGNED ) BETWEEN '" . $price[0] . "' AND '" . $price[1] . "' )";
				} elseif ( $k != '_price' && $k != 'term_id' && $k != 'action' ) {
					$meta_inner .= "INNER JOIN {$wpdb->postmeta} AS mt{$i} ON ({$wpdb->posts}.ID = mt{$i}.post_id) ";
					$meta_query .= "AND (
					  mt{$i}.meta_key = '{$k}' AND mt{$i}.meta_value IN('" . implode( "','", $v ) . "')
					)";
					$i++;
				}
			}

			$sql = "SELECT {$wpdb->posts}.* FROM {$wpdb->posts}
				LEFT JOIN {$wpdb->term_relationships} ON ( {$wpdb->posts}.ID = {$wpdb->term_relationships}.object_id )
				INNER JOIN {$wpdb->postmeta} ON ( {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id ) 
				{$meta_inner} 
				WHERE
				  1 = 1 AND (
					{$wpdb->term_relationships}.term_taxonomy_id IN(" . $args['term_id'] . ")
				  ) AND (
					{$price_query} {$meta_query} 
				  ) AND {$wpdb->posts}.post_type = 'product' AND ( ( {$wpdb->posts}.post_status = 'publish' OR {$wpdb->posts}.post_status = 'private' ) ) GROUP BY {$wpdb->posts}.ID ORDER BY {$wpdb->posts}.post_date DESC";

			$values = $wpdb->get_results( $sql );
			/* print_r( $price_query ); */
			
		}
		

		//print_r( $values );
		echo '<div class="wpsl-filter__result_box">';
		echo '<div>' . sprintf( __( 'Found: %d', 'wpsl' ), count( $values ) ) . '</div>';
		echo '<input type="submit" class="wpsl-filter__submit_btn" value="' . __( 'Show', 'wpsl' ) . '">';
		//echo wpsl_exec_time('get');
		echo '</div>';
		wp_die();
	}
	
	/**
	 * AJAX data update in your account
	 */
	public function update_user_profile() {

		// разбираем полученные данные из формы
		parse_str( $_POST['str'], $collection );
		
		if ( !wp_verify_nonce( $_POST['nonce'], 'wpsl-nonce' ) ) exit;

		// теперь возьмем все поля и рассуем по переменным
		$user_email   = $collection['user_email'];
		$first_name   = $collection['first_name'];
		$user_phone   = $collection['phone'];
		$user_address = $collection['address'];
		$uzer_zip     = $collection['zip'];
		$description  = $collection['description'];


		// теперь проверим обязательные поля на заполненность и валидность - у нас это только поле с почтой
		if ( !$user_email ) {
			$html = '<div class="error">' . __( 'Email field must be filled', 'wpsl' ) . '</div>';
		} else {
			if ( !preg_match( "|^[-0-9a-z_\.]+@[-0-9a-z_^\.]+\.[a-z]{2,6}$|i", $user_email ) ) {
				$html = '<div class="error">' . __( 'Invalid email format', 'wpsl' ) . '</div>';
			} else {
				global $current_user;
				if ( $current_user->user_email == $user_email && get_user_by( 'email', $user_email ) ) {
					$fields = array();
					$fields['ID'] = $current_user->ID;
					$fields['user_email'] = $user_email;
					$fields['first_name'] = $first_name;

					// обновляем данные юзера
					$update_user = wp_update_user( $fields );
					update_user_meta( $current_user->ID, 'phone', $user_phone );
					update_user_meta( $current_user->ID, 'address', $user_address );
					update_user_meta( $current_user->ID, 'zip', $uzer_zip );
					update_user_meta( $current_user->ID, 'description', $description );

					if ( is_wp_error( $update_user ) ) $html = 'Системная ошибка: ' . $update_user->get_error_code();

					$html .= '<div class="success">' . __( 'Saved', 'wpsl' ) . '</div>';
				} else {
					$html = '<div class="error">' . __( 'This email is busy', 'wpsl' ) . '</div>';
				}
			}
		}
		wp_die( $html );
	}
	
	/**
	 * We load the list of products when choosing an order to form a request to technical support
	 */
	public function update_select_order() {
		if ( is_user_logged_in() ) {
			$select = __( 'Select product', 'wpsl' );
			$select .= '<select name="ticket-product" id="ticket-product" class="ticket-product" value="">';
			$posts = get_post_meta( $_POST['order'], 'detail', true );
			$select .= '<option value="0">' . __( 'Ticket for the whole order', 'wpsl' ) . '</option>';
			foreach( $posts as $id => $val ){
				if ( wpsl_opt( 'taboo' ) == '1' && $val['WPSL_PRICE'] == 0 ) {
					
				} else {
					$select .= '<option value="' . $id . '">' . $val['WPSL_TITLE'] . ' ( ' . $val['WPSL_PRICE'] . ' ' . wpsl_opt( 'currency_symbol' ) . ' )</option>';
				}
			}
			wp_reset_postdata();
			$select .= '</select>';
			echo $select;
			wp_die();
		}
	}
	
	/**
	 * Send a request to technical support
	 */
	public function add_ticket_for_technical_support() {
		if ( is_user_logged_in() ) {
			parse_str( $_POST['str'], $collection );
			//print_r( $collection );
			$post_id = wp_insert_post(
				array( 
					'post_title'   => $collection['ticket-title'],
					'post_content' => $collection['ticket-content'],
					'post_status'  => 'publish',
					'post_author'  => $collection['ticket-author'],
					'post_type'    => 'support'
				)
			);
			if ( $post_id ) {
				update_post_meta( $post_id, 'ticket-order', $collection['ticket-order'] );
				update_post_meta( $post_id, 'ticket-product', $collection['ticket-product'] );
				
				echo '<div class="wps-no-tickets">' . __( 'Your ticket has been successfully sent. Wait for the operator"s response', 'wpsl' ) . '</div>';
			}
			wp_die();
		}
	}
	
	/**
	 * Get ticket details
	 */
	public function get_ticket_details() {
		$ticket_id = ( int ) $_POST['ticket_id'];
		$user_id = ( int ) $_POST['user_id'];
		if ( is_user_logged_in() && $user_id == get_current_user_id() ) {
			$ticket = get_post( $ticket_id );
			?>
			<div class="ticket-details">
				<div class="title"><?php echo get_the_title( $ticket->ID ); ?></div>
				<div class="desc">
					<?php echo $ticket->post_content; ?>
				</div>
				<div class="comments">
					<ul>
					<?php 
					$args = array( 
						'post_id' => $ticket->ID,
						'number'  => '',
						'orderby' => 'comment_date',
						'order'   => 'DESC',
						'status'  => 'approve',
						'type'    => 'comment', // только комментарии, без пингов и т.д...
					 );
					if( $comments = get_comments( $args ) ){
						foreach( $comments as $comment ){
							?>
							<li class="<?php echo $comment->user_id == get_current_user_id() ? 'customer' : 'operator'; ?>">
								<div class="author"><?php echo $comment->user_id == get_current_user_id() ? __( 'You', 'wpsl' ) : __( 'Operator', 'wpsl' ); echo ' ' . $comment->comment_date; ?></div>
								<div class="comment"><?php echo $comment->comment_content; ?></div>
							</li>
							<?php
						}
					}
					?>
					</ul>
					<form id="create-ticket" name="create-ticket" method="post" action="" class="create-ticket">
						<label for="ticket-title">
							<?php _e( 'Text', 'wpsl' ); ?>
							<textarea class="wp-editor-area" rows="20" autocomplete="off" cols="40" name="ticket-content" required="" ></textarea>
						</label>
						
						<input type="hidden" value="<?php echo get_current_user_id(); ?>" name="ticket-author" id="ticket-author" class="ticket-author" />
						<input class="wps-add-ticket" type="submit" value="<?php _e( 'Send', 'wpsl' ); ?>" id="submit" name="submit" />
					</form>
				</div>
			</div>
			<?php
			wp_die();
		}
	}

	/**
	 * Re-create a link to download a digital product
	 */
	public function regenerate_link_download_digital_product() {
		if ( is_user_logged_in() ) {
			$prod_id = (int)$_POST['id'];
			// проверим, есть ли в заказе товар
			if ( $products = get_post_meta( (int)$_POST['order'], 'detail', true ) ) {
				foreach ( $products as $id => $val ) {
					if ( $id == $prod_id ) {
						update_post_meta( $prod_id, 'token', md5( uniqid( rand(), 1 ) ) );
						echo '<a href="' . wpsl_get_permalink( $prod_id ) . '?action=download&id=' . $prod_id . '&token=' . get_post_meta( $prod_id, 'token', true ) . '">' . __( 'Download', 'wpsl' ) . '</a>';
					}
				}
			}
			wp_die();
		}
	}

	/**
	 * Details of order
	 */
	public function get_order_details() {
		$order_id = ( int ) $_POST['order_id'];
		$user_id = ( int ) $_POST['user_id'];
		// check user
		if ( $user_id == get_current_user_id() ) {
		?>
		<div class="wps-order-list">
			<div class="wps-order wps-order-date"><?php _e( 'Date of order', 'wpsl' ); ?></div>
			<div class="wps-order wps-order-summ"><?php _e( 'Summa', 'wpsl' ); ?></div>
			<?php 
			foreach ( wpsl_order_statuses() as $key => $val ) {
				echo '<div class="wps-order wps-order-' . $key . '">' . $val . '</div>';
			}
			?>
		</div>
		<div class="wps-order-list">
			<div class="wps-order-id" data-order-id="<?php echo $order_id; ?>">
				<div>
					<div class="wps-order wps-order-date"><?php echo get_the_date( 'j.n.Y', $order_id ); ?></div>
					<div class="wps-order wps-order-summ"><?php echo get_post_meta( $order_id, 'summa', true ) . ' ' . wpsl_opt(); ?></div>
					<?php 
					foreach ( wpsl_order_statuses() as $key => $val ) {
						$term = wpsl_get_statuses( $key, $order_id );
						echo '<div class="wps-order wps-order-' . $key . '">' . $term[0]->name . '</div>';
					}
					?>
				</div>
				<div class="wps-order-box">
					<div><?php _e( 'Details of order', 'wpsl' ); ?></div>
					<div class="product-item">
						<div class="title"><?php _e( 'Name', 'wpsl' ); ?></div>
						<div class="price"><?php _e( 'Price', 'wpsl' ); ?></div>
						<div class="quo"><?php _e( 'Quo', 'wpsl' ); ?></div>
					</div>
					<?php 
					$products = get_post_meta( $order_id, 'detail', true );
					$status = wpsl_get_statuses( 'payment', $order_id );
					foreach ( $products as $product_id => $val ) {
						echo '<div class="product-item">';
						echo '<div class="title"><a href="' . wpsl_get_permalink( $product_id ) . '" target="_blank">' . get_the_title( $product_id ) . '</a>';
						if ( get_post_meta( $product_id, '_digital', true ) == '1' && $status[0]->slug == 'paid' ) {
							echo '<div class="download" data-order="' . $order_id . '" data-id="' . $product_id . '">' . __( 'Regenerate download link', 'wpsl' ) . '</div>';
						}
						echo '</div>';
						echo '<div class="price">' . $val['WPSL_PRICE'] . ' ' . wpsl_opt() . '</div>';
						echo '<div class="quo">' . $val['WPSL_QUO'] . '</div>';
						echo '</div>';
					}
					?>
				</div>
			</div>
		</div>
		<?php
		}
		wp_die();
	}
	
	/**
	 * Send massage
	 */
	public function send_message_from_contactform(){

		$str = $_POST['str'];
		wp_parse_str( $str, $array );
		
		if( $str && ! $array['saveguard'] ) {
			//print_r($array);
			$name = htmlspecialchars( trim( $array['name'] ) );
			$email = htmlspecialchars( trim( $array['email'] ) );
			$message = htmlspecialchars( trim( $array['message'] ) );
			$error = '';
			// Check errors
			if( !$name ) {
				$error .= __( 'Enter your name', 'wpsl' ) . '</br>';
			}
			if ( !$email ) {
				$error .= __( 'Enter your email', 'wpsl' ) . '</br>';
			}
			if ( !$message ) {
				$error .= __( 'Enter your message', 'wpsl' ) . '</br>';
			}
			// Send message to admin
			if( $error == '' ) {
				$to = get_option( 'admin_email' );
				$from = get_option( 'blogname' );
				$subject = '[' . $from . '] ' . __( 'Request from contact form', 'wpsl' ) ;
				$comment = __( 'Received an application from the site with a request to call back', 'wpsl' ) . '<br>';
				$comment .= __( 'Name', 'wpsl' ) . ': ' . $name . '<br>';
				$comment .= __( 'Email', 'wpsl' ) . ': ' . $email . '<br>';
				$comment .= __( 'Comment', 'wpsl' ) . ': ' . $message;
				$headers = array(
					__( 'From', 'wpsl' ) . get_option( 'blogname' ),
					'content-type: text/html',
				);
				if ( wpsl_mail( $to, $subject, $comment, $headers ) ) {
					echo '<div class="success">' . __( 'The message was successfully sent to admin', 'wpsl' ) . '</div>';
				} else {
					echo '<div class="error">' . __( 'Something went wrong. Click "Submit" again', 'wpsl' ) . '</div>';
				}
			} else {
				echo '<div class="error">' . $error . '</div>';
			}
		} else {
			echo '<div class="error">' . __( 'No spam', 'wpsl' ) . '</div>';
		}
		wp_die();
	}

	/**
	 * Send product review
	 */
	public function send_product_review() {

		// check nonce code, if the test is not passed interrupt processing
		check_ajax_referer( 'wpsl-nonce', 'nonce' );

		wp_parse_str( $_POST['str'], $array );
		
		if( $_POST['str'] && ! $array['saveguard'] ) {
			// Check errors
			$error = '';
			if( !$name = htmlspecialchars( trim( $array['name'] ) ) ) {
				$error .= __( 'Enter your name', 'wpsl' ) . '</br>';
			}
			if ( !$email = htmlspecialchars( trim( $array['email'] ) ) ) {
				$error .= __( 'Enter your email', 'wpsl' ) . '</br>';
			}
			if ( !$assessment = htmlspecialchars( trim( $array['assessment'] ) ) ) {
				$error .= __( 'Enter your assessment', 'wpsl' ) . '</br>';
			}
			if ( !$plus = htmlspecialchars( trim( $array['plus'] ) ) ) {
				$error .= __( 'Enter product advantages', 'wpsl' ) . '</br>';
			}
			if ( !$minus = htmlspecialchars( trim( $array['minus'] ) ) ) {
				$error .= __( 'Enter product disadvantages', 'wpsl' ) . '</br>';
			}
			
			// no errors
			if( $error == '' ) {

				// find user
				$user_id = wpsl_get_user( $email );
				
				// add review
				$data = array(
					'comment_post_ID'      => (int)$array['post_id'],
					'comment_author'       => $name,
					'comment_author_email' => $email,
					'comment_content'      => htmlspecialchars( trim( $array['comment'] ) ),
					'comment_type'         => 'review',
					'user_id'              => $user_id,
					'comment_date'         => current_time( 'mysql' ),
					'comment_approved'     => 1,
					'comment_meta'         => array(
						'assessment' => $assessment,
						'plus'       => $plus,
						'minus'      => $minus,
					),
				);
				$comment = wp_insert_comment( wp_slash( $data ) );
				if ( !$comment ) {
					echo '<div class="error">' . __( 'Something went wrong, send review again', 'wpsl' ) . '</div>';
				} else {
					echo '<div class="success">' . __( 'Review was sended', 'wpsl' ) . '</dev>';
				}
				
				update_post_meta( (int)$array['post_id'], '_rate', wpsl_get_average_mark( (int)$array['post_id'] ) );
				
			} else {
				echo '<div class="error">' . $error . '</div>';
			}
		} else {
			echo '<div class="error">' . __( 'No spam', 'wpsl' ) . '</div>';
		}
		wp_die();
	}
	
	/**
	 * Change the count of items in the cart without reloading
	 */
	public function change_cart() {
		
		if ( !session_id() ) {
			@session_start();
		}
		
		wp_parse_str( wpsl_clean( $_POST['str'] ), $products );
		
		foreach( $products as $id => $quo ) {
			if ( array_key_exists( $id, $_SESSION['WPSL_ITEMS'] ) ) {
				// если количество товаров равно 0, то удаляем из сессии товар с этим id
				if ( (int)$quo == 0 ){
					unset( $_SESSION['WPSL_ITEMS'][$id] );
				} else {
					if ( wpsl_opt( 'cart_single' ) != '1' ) {
						$_SESSION['WPSL_ITEMS'][$id]['WPSL_QUO'] = (int)$quo;
					} else {
						$_SESSION['WPSL_ITEMS'][$id]['WPSL_QUO'] = 1;
					}
				}
			}
		}
		
		if ( isset( $products['coupon_code'] ) && $products['coupon_code'] != '' ) {
			$discount = new WPSL_Discount( $products['coupon_code'] );
			//print_r( $discount );
		}
		
		// обработка формы заказа
		$order = new WPSL_Order();
		$order->handle();
		
		// покажем корзину
		$cart = WPSL_Cart::create();
		$data['cart'] = $cart->getHTML();
		
		if ( isset( $cart->items ) ) {
			$data['count'] = '<span class="wpsl-count-product">' . count( $cart->items ) . '</span>';
		}
		
		$data['total'] = $cart->getTotal();
		
		echo json_encode( $data );
			
		wp_die();
	}
	
	/**
	 * Make bid
	 */
	public function make_bid() {
		
		if ( !wp_verify_nonce( $_GET['nonce'], 'wpsl-nonce' ) ) exit;
		
		wp_parse_str( $_GET['str'], $args );
		if ( $args['saveguard'] == '' ) {
			$product_id = (int)$args['post_id'];
			$old = (int)get_post_meta( $product_id, '_price', true );
			if ( (int)$args['bid'] > (int)$old ) {
				if ( is_user_logged_in() ) {
					global $current_user;
					$ele = update_post_meta( $product_id, '_price', (int)$args['bid'], $old );
					add_post_meta( $product_id, 'bid_list', (int)$args['bid'] . ':' . $current_user->data->user_login, false );
					echo '<div class="wpsl-notify">' . __( 'Your bet is made', 'wpsl' ) . '</div>';
				} else {
					$link = __( 'A link to confirm your bet has been sent to your e-mail', 'wpsl' );
					if ( $email = wpsl_mail_services( $args['email'] ) ) {
						$link .= '<br><br>' . __( 'Go to the mail service', 'wpsl' ) . sprintf( ': <a href="%s" target="_blank">%s</a>', $email[1], $email[0] );

						$msg = __( 'You have placed a bid on the purchase of the auction product', 'wpsl' );
						$msg .= sprintf( '<br><br><a href="%s" target="_blank">' . __( 'Confirm bid', 'wpsl' ) . '</a>', $confirm_link = get_permalink( $product_id ) . '?action=bid_confirmation&product_id=' . $product_id . '&token=' . wpsl_add_token( $product_id ) . '&' . $_GET['str'] );
						wpsl_mail( $args['email'], __( 'Bid confirmation', 'wpsl' ), $msg );
					}
					echo '<div class="wpsl-notify">' . $link . '</div>';
				}
				
				/**
				 * Triggering an event after a successful bid
				 */
				do_action( 'wpsl_make_bid', $args );
			} else {
				echo '<div class="wpsl-notify">' . __( 'Your bid cannot be lower than the current price', 'wpsl' ) . '</div>';
			}
		}
		wp_die();
	}
	
	/**
	 * Add simple product to cart
	 */
	public function add_simple_product_to_cart() {
		
		if ( !wp_verify_nonce( $_GET['nonce'], 'wpsl-nonce' ) ) exit;
		
		wp_parse_str( $_GET['str'], $args );
		
		if ( isset( $args['saveguard'] ) && $args['saveguard'] == '' ) {
			$cart = WPSL_Cart::create();
			$get['id'] = (int)$args['post_id'];
			$count = ( isset( $args['count'] ) && $args['count'] ) ? (int)$args['count'] : 1;
			$cart->add( $get, $count );
			echo wpsl_get_template_html( 'cart', 'proceed-to-checkout-button' );
		}
		wp_die();
	}
	
	/**
	 * Add variable product to cart
	 */
	public function add_variable_product_to_cart() {
		
		if ( !wp_verify_nonce( $_GET['nonce'], 'wpsl-nonce' ) ) exit;
		
		wp_parse_str( $_GET['str'], $args );
		
		/* print_r( $args ); */
		
		if ( isset( $args['saveguard'] ) && $args['saveguard'] == '' ) {
			
			$variations = get_post_meta( (int)$args['post_id'], '_product_variations', true );
			if ( !$variations ) return;
			
			foreach ( $variations as $variation ) {
				$var = json_decode( $variation );
				if ( !$var ) continue;
				
				$arr = array_diff( (array)$var->item_variation, $args );
				if ( empty( $arr ) ) {
					$v = $var;
				/* 	echo '<pre>';
					print_r( $var );
					echo '</pre>'; */
				}
			}
			
			if ( $v ) {
				$cart = WPSL_Cart::create();
				$get['id'] = (int)$v->item_id;
				$count = ( isset( $args['count'] ) && $args['count'] ) ? (int)$args['count'] : 1;
				$cart->add( $get, $count );
				echo wpsl_get_template_html( 'cart', 'proceed-to-checkout-button' );
			}
		}
		wp_die();
	}
	
	/**
	 * Add sponsorship product to cart
	 */
	public function sponsorship_product_to_cart() {
		if ( !wp_verify_nonce( $_GET['nonce'], 'wpsl-nonce' ) ) exit;
		
		wp_parse_str( $_GET['str'], $args );
		
		if ( isset( $args['saveguard'] ) && $args['saveguard'] == '' ) {
			$cart = WPSL_Cart::create();
			$get['id']     = (int)$args['post_id'];
			$get['_price'] = (int)$args['_price'];
			$count = ( isset( $args['count'] ) && $args['count'] ) ? (int)$args['count'] : 1;
			$cart->add( $get, $count );
			echo wpsl_get_template_html( 'cart', 'proceed-to-sponsoring-button' );
		}
		wp_die();
	}
}
$WPSL_Front = new WPSL_Ajax_Frontend();