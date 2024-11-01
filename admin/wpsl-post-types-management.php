<?php
/**
 * Managment post types in admin panel
 *
 * @author	wpStore
 * @since	1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( !is_admin() ) return;

/**
 * Add columns to admin page for products
 *
 * @since	2.0.0
 */
add_filter( 'manage_edit-product_columns', 'wpsl_add_post_columns', 99999, 1 );
function wpsl_add_post_columns( $columns ) {
	
	$fields = array( 'taxonomy-product_cat', 'taxonomy-product_tag', 'product_tag', 'comments', 'date', '_sku', '_price', 'is_in_stock' );
	foreach ( $fields as $field ) {
		unset( $columns[$field] );
	}
	
	$columns = array_slice( $columns, 0, 3 ) + array( '_stock_status'        => __( 'Stock', 'wpsl' ) ) + array_slice( $columns, 3 );
	$columns = array_slice( $columns, 0, 4 ) + array( 'sku'                  => __( 'Sku', 'wpsl' ) ) + array_slice( $columns, 4 );
	$columns = array_slice( $columns, 0, 5 ) + array( 'price'                => __( 'Price', 'wpsl' ) ) + array_slice( $columns, 5 );
	$columns = array_slice( $columns, 0, 6 ) + array( 'product_cat'          => __( 'Categories', 'wpsl' ) ) + array_slice( $columns, 6 );
	$columns = array_slice( $columns, 0, 7 ) + array( 'taxonomy-product_tag' => __( 'Tags', 'wpsl' ) ) + array_slice( $columns, 7 );
	$columns = array_slice( $columns, 0, 8 ) + array( 'hit_product'          => __( 'Hit', 'wpsl' ) ) + array_slice( $columns, 8 );
	$columns = array_slice( $columns, 0, 9 ) + array( 'date'                 => __( 'Date', 'wpsl' ) ) + array_slice( $columns, 9 );

	return $columns;
}


/**
 * Fill columns to admin page for products
 *
 * @since	2.0.0
 */
add_action( 'manage_posts_custom_column', 'wpsl_fill_post_columns', 10, 1 );
function wpsl_fill_post_columns( $column ) {
	global $post;
	switch ( $column ) {
		case 'price':
			$type = get_post_meta( $post->ID, 'type-product', true );
			if ( $type == 'variable' ) {
				echo wpsl_get_price( $post->ID );
			} elseif ( $type == 'auction' ) {
				
			} elseif ( $type == 'sponsorship' ) {
				
			} else {
				echo wpsl_opt() . '<input type="number" min="0" data-decimals="' . wpsl_opt( 'currency_num_decimals', 2 ) . '" class="this_price wpsl-validate-price" data-id="' . $post->ID .'" value="' . get_post_meta( $post->ID, '_price', true ) . '" /><p></p>';
			}
			break;
		case 'hit_product':
			echo '<input ' . checked( get_post_meta( $post->ID, 'hit_product', true ), 'on', false ) . ' type="checkbox" class="this_hit" data-id="' . $post->ID .'" value="' . get_post_meta( $post->ID, 'hit_product', true ) . '" /><p></p>';
			break;
		case 'product_cat':
			echo the_terms( $post->ID, 'product_cat' );
			break;
		case 'product_views':
			echo get_post_meta( $post->ID, '_product_views', true );
			break;
		case 'sku':
			echo get_post_meta( $post->ID, '_sku', true );
			break;
		case '_stock_status':
			$color = '';
			$status = wpsl_post( '_stock_status' );
			switch ( $status ) {
				case 'instock':
					$color = '#8BC34A';
					break;
				case 'outofstock':
					$color = '#ef685e';
					break;
				case 'preorder':
					$color = '#44b9ef';
					break;
			}
			echo '<strong style="color: ' . $color . ';">' . wpsl_get_stock_status( $status ) . '</strong>';
			break;
	}
}


/**
 * Make sortable columns
 *
 * @since	2.0.0
 */
add_filter( 'manage_edit-product_sortable_columns', 'wpsl_add_sortable_colomns' );
function wpsl_add_sortable_colomns( $columns ) {
	$columns['price'] = '_price';
	$columns['sku']   = '_sku';
	return $columns;
}


/**
 * Change query for sortable columns
 *
 * @since	2.0.0
 */
add_action( 'pre_get_posts', 'wpsl_orderby_add_colomns' );
function wpsl_orderby_add_colomns( $query ) {
	global $current_screen;
	if( ! is_admin() || empty( $current_screen->post_type ) || $current_screen->post_type !== 'product' || $query->get( 'orderby' ) !== '_price' || $query->get( 'orderby' ) !== '_sku' ) return;
	
	$orderby = $query->get( 'orderby' );
	$query->set( 'meta_key', $orderby );
	switch( $orderby ) {
		case( '_price' ):
			$query->set( 'orderby', 'meta_value_num' );
			break;
		case( '_sku' ):
			$query->set( 'orderby', 'meta_value' );
			break;
	}
}


/**
 * wpStore
 *
 * Create filter in taxonomy in admin
 *
 * @author	https://ru.wordpress.org/plugins/admin-category-filter/
 * @since	2.4.0
 */
add_action( 'admin_print_scripts', 'wpsl_admin_term_filter', 99 );
function wpsl_admin_term_filter() {
	$screen = get_current_screen();

	if( 'post' !== $screen->base ) return;
	
	$terms = get_terms( 'product_cat', [
		'hide_empty' => false,
	] );
	if ( $terms && count( $terms ) >= 10 ) : ?>
	<script>
	jQuery( document ).ready( function( $ ){
		var $div = $( '.categorydiv' );
		$div.prepend( '<input type="search" class="fc-search-field" placeholder="<?php _e( 'Filter', 'wpsl' ); ?>" style="width:100%" />' );
		$div.on( 'keyup search', '.fc-search-field', function ( event ) {
			var searchTerm = event.target.value,
				$listItems = $( this ).parent().find( '.categorychecklist li' );

			if( $.trim( searchTerm ) ){
				$listItems.hide().filter( function () {
					return $( this ).text().toLowerCase().indexOf( searchTerm.toLowerCase() ) !== -1;
				} ).show();
			} else {
				$listItems.show();
			}
		} );
	} );
	</script>
	<?php endif; ?>
	<?php if ( strpos( get_option( 'product_permalink' ), '%categories%' ) !== false || strpos( get_option( 'tax_permalink' ), '%category%' ) !== false ) : ?>
		<script>
		jQuery( document ).ready( function( $ ){
			jQuery('#taxonomy-product_cat input[type="checkbox"]').change(function() {
				if ( jQuery(this).is(':checked') ) {
					// remove level
					jQuery(this).parents('li').siblings('li').find('input').prop('checked',false);
					
					jQuery(this).parents('.children').siblings('label').children('input').attr('checked','checked');
				} else {
					jQuery(this).parents('label').siblings('.children').find('input').prop('checked',false);
				}
			});
		} );
		</script>
		<?php
	endif;
}


/**
 * wpStore
 *
 * This function maintains the hierarchical order of categories list in Category tab under your WordPress admin post editor.
 *
 * @since	2.7.0
 */
add_filter( 'wp_terms_checklist_args', 'wpsl_change_taxonomy_checkbox_list', 10, 2 );
function wpsl_change_taxonomy_checkbox_list( $args, $post_id ) {
	if ( isset( $args['taxonomy'] ) ) {
		$args['checked_ontop'] = false;
	}
	return $args;
}


/**
 * wpStore
 *
 * Filter products by category in admin page
 *
 * @author	wpStore
 * @since	2.0.0
 */
add_action( 'restrict_manage_posts', 'wpsl_taxonomy_filter' );
function wpsl_taxonomy_filter() {
	global $typenow;
	// фильтр товаров по категориям
	if( $typenow == 'product' ){ // для каких типов постов отображать
		$taxes = array( 'product_cat' ); // таксономии через запятую
		foreach ( $taxes as $tax ) {
			$current_tax = isset( $_GET[$tax] ) ? $_GET[$tax] : '';
			$tax_obj = get_taxonomy( $tax );
			$tax_name = mb_strtolower( $tax_obj->labels->name );
			$terms = get_terms( $tax );
			if( count( $terms ) > 0 ) {
				echo "<select name='$tax' id='$tax' class='postform'>";
				echo "<option value=''>" . __( 'All category of product', 'wpsl' ) . "</option>";
				foreach ( $terms as $term ) {
					echo '<option value='. $term->slug, $current_tax == $term->slug ? ' selected="selected"' : '','>' . $term->name .' ( ' . $term->count .' )</option>'; 
				}
				echo "</select>";
			}
		}
	}
	// фильтр заказов по статусу
	if( $typenow == 'shop_order' ){ // для каких типов постов отображать
		$taxes = array( 'wpsl_status' ); // таксономии через запятую
		foreach ( $taxes as $tax ) {
			$current_tax = isset( $_GET[$tax] ) ? $_GET[$tax] : '';
			$tax_obj = get_taxonomy( $tax );
			$tax_name = mb_strtolower( $tax_obj->labels->name );
			$terms = get_terms( $tax );
			if( count( $terms ) > 0 ) {
				echo "<select name='$tax' id='$tax' class='postform'>";
				echo "<option value=''>" . __( 'All status of order', 'wpsl' ) . "</option>";
				foreach ( $terms as $term ) {
					echo '<option value='. $term->slug, $current_tax == $term->slug ? ' selected="selected"' : '','>' . $term->name .' ( ' . $term->count .' )</option>'; 
				}
				echo "</select>";
			}
		}
	}
}


/**
 * Submenu in wpStore tab
 *
 * @author	wpStore
 * @since	2.0.0
 */
add_action( 'admin_menu', 'wpsl_get_admin_submenu', 20 );
function wpsl_get_admin_submenu() {
	if ( wpsl_opt( 'shipping' ) == '1' ) {
		add_submenu_page( 'wpsl_options', __( 'Shipping types', 'wpsl' ), __( 'Shipping', 'wpsl' ), 'install_plugins', 'edit.php?post_type=delivery', '' );
	}
	add_submenu_page( 'wpsl_options', __( 'Coupons', 'wpsl' ), __( 'Coupons', 'wpsl' ), 'install_plugins', 'edit.php?post_type=shop_coupon', '' );
}


/**
 * Add columns to admin page for products
 *
 * @author	wpStore
 * @since	2.0.0
 */
add_filter( 'manage_delivery_posts_columns', 'wpsl_get_delivery_columns_head' );
function wpsl_get_delivery_columns_head( $defaults ) {
	// New colomn
	$defaults['delivery_price'] = __( 'Price', 'wpsl' );

	// Убираем лишнее
	unset( $defaults['date'] );
	return $defaults;  
}  
// Вывод данных в таблице доставки 
add_action( 'manage_delivery_posts_custom_column', 'wpsl_show_delivery_columns', 10, 2 ); 
function wpsl_show_delivery_columns( $column_name, $postId ) {
	switch ( $column_name ) {
		case 'delivery_price':
			echo wpsl_get_meta( $postId, 'delivery_price' ) . ' ' . wpsl_opt();
			break;
	}
}
// Свойства доставки ( произвольные поля ) по умолчанию
add_action( 'wp_insert_post', 'wpsl_set_shipping_default' );
function wpsl_set_shipping_default( $postId ) {
	if ( isset( $_GET['post_type'] ) && $_GET['post_type'] == 'delivery' ) {
		add_post_meta( $postId, 'delivery_price', '0', true ) 
			or update_post_meta( $postId, 'delivery_price', '0' );
	}
	return true;
}


/**
 * Orders
 * Display the order details on the edit page
 *
 * @since	2.4.0
 */
function wpsl_special_field_out_function(){
	ob_start();
	$order = get_post();
	//$content = $order->post_content;
	$products = get_post_meta( $order->ID, 'detail', true );
	$del_id = (int)wpsl_get_meta( $order->ID, 'delivery_type' );
	$del_price = wpsl_get_meta( $del_id, 'delivery_price' );
	$total_price = $del_price + wpsl_get_meta( $order->ID, 'summa' );
	$content = '';
	$content .= '<table class="wp-list-table widefat fixed striped posts" style="width: 100%;">
						<thead>
							<tr>
								<td class="photo" style="width: 50px; overflow: hidden; white-space: nowrap; box-sizing: border-box;">' . __( 'Photo', 'wpsl' ) . '</td>
								<td class="title">' . __( 'Title', 'wpsl' ) . '</td>
								<td class="quo" style="width: 80px; overflow: hidden; white-space: nowrap; box-sizing: border-box;"">' . __( 'Quo', 'wpsl' ) . '</td>
								<td class="price" style="width: 65px; overflow: hidden; white-space: nowrap; box-sizing: border-box;"">' . __( 'Price', 'wpsl' ) . '</td>
								<td class="summ" style="width: 80px; overflow: hidden; white-space: nowrap; box-sizing: border-box;"">' . __( 'Summ', 'wpsl' ) . '</td>
							</tr>
						</thead>
						<tbody>';
	if ( $products ) {
		foreach ( $products as $id => $detail ) {
			$summ = $detail['WPSL_QUO'] * $detail['WPSL_PRICE'];
			$content .= '	<tr data-product-id="' . $id . '">
								<td class="photo" style="width: 50px; overflow: hidden; white-space: nowrap; box-sizing: border-box;">' . get_the_post_thumbnail( $id, array( 30, 30 ) ) . '</td>
								<td class="title"><a href="' . wpsl_get_permalink( $id ) . '" target="_blank">' . get_the_title( $id ) . '</a></td>
								<td class="quo" data-value="' . $detail['WPSL_QUO'] . '" data-id="' . $id . '">' . $detail['WPSL_QUO'] . '</td>
								<td class="price" data-value="' . $detail['WPSL_PRICE'] . '">' . $detail['WPSL_PRICE'] . ' ' . wpsl_opt() . '</td>
								<td class="summ" data-value="' . $summ . '">' . $summ . ' ' . wpsl_opt() . '</td>
							</tr>';
		}
		if ( wpsl_opt( 'support' ) == '1' ) {
		$content .= '
							<tr>
								<td class="photo"></td>
								<td class="title">' . __( 'Shipping', 'wpsl' ) . ': ' . get_the_title( $del_id ) . '</td>
								<td class="quo"></td>
								<td class="price"></td>
								<td class="summ">' . $del_price . ' ' . wpsl_opt() . '</td>
							</tr>';
		}
	}
	$content .= '
						</tbody>';
	$content .= '		<tfoot>
							<tr>
								<td class="photo"></td>
								<td class="title">' . __( 'Total price', 'wpsl' ) . '</td>
								<td class="quo"></td>
								<td class="price"></td>
								<td class="summ"><strong>' . $total_price . ' ' . wpsl_opt() . '</strong></td>
							</tr>
						<tfoot>
					</table>';
	echo $content;
	return ob_get_clean();
}


/**
 * wpStore
 * Filling metabox statuses
 *
 * @author	wpStore
 * @since	2.7.0
 */
function wpsl_fill_status_order_metabox(){
	// terms of order
	$content = '';
	$order = get_post();
	$order_terms = json_decode( json_encode( get_the_terms( $order->ID, 'wpsl_status' ) ), true );
	
	// statuses
	$statuses = wpsl_order_statuses();
	foreach ( $statuses as $key => $val ) {
		$order_status = wpsl_get_statuses( $key );
		$content .= '<label for="' . $key . '">' . $val . '</label>';
		$content .= '<div class="statuses">';
		if ( $order_status ) {
			foreach ( $order_status as $status ) {
				$checked = $order_terms && in_array( $status->term_id, array_column( $order_terms, 'term_id' ) ) == true ? 'checked' : '';
				$content .= '<label for="wpsl_' . $status->slug . '"><input type="radio" id="wpsl_' . $status->slug . '" name="wpsl_' . $key . '" value="' . $status->slug . '" ' . $checked . '>' . $status->name . '</label>';
			}
		} else {
			$content .= __( 'You must create new statuses and attach them to the type', 'wpsl' ) . ' "' . $val . '"';
		}
		$content .= '</div>';
	}
	return $content;
}


/**
 * Save metabox statuses
 *
 * @since	2.7.0
 */
add_action( 'save_post', 'wpsl_save_status_order_metabox', 10, 3 );
function wpsl_save_status_order_metabox( $post_id, $post, $update ) {
	
	if( ! current_user_can( 'edit_order' ) ) return;
	
	// only orders post type
	$slug = 'shop_order';
	if ( isset( $_POST['post_type'] ) && $slug != $_POST['post_type'] )
		return;

	// remove old relationships
	if ( taxonomy_exists( 'wpsl_status' ) ) {
		wp_delete_object_term_relationships( $post_id, 'wpsl_status' );
	
		// update statuses
		foreach ( wpsl_order_statuses() as $key => $val ) {
			if ( isset( $_REQUEST['wpsl_' . $key] ) ) {
				wp_set_object_terms( $post_id, wpsl_clean( $_REQUEST['wpsl_' . $key] ), 'wpsl_status', true );
			}
		}
	}
}


/**
 * wpStore
 *
 * Считаем количество необработанных заказов и выводим их
 * в меню админки в виде оранжевого кружочка
 *
 * @since	1.5.0
 */
add_action( 'admin_menu', 'wpsl_new_orders_count_bubble' );
function wpsl_new_orders_count_bubble(){
	global $menu;
	$args = array( 
		'numberposts' => -1,
		'wpsl_status' => 'new',
		'post_type'   => 'shop_order',
	);
	$new_orders = get_posts( $args );
	$count = count( $new_orders );
	if( $count ){
		foreach( $menu as $key => $value ){
			if( $menu[$key][2] == 'edit.php?post_type=shop_order' ){
				$menu[$key][0] .= ' <span class="awaiting-mod"><span class="pending-count">' . $count . '</span></span>';
				break;
			}
		}
	}
}


/**
 * wpStore
 *
 * Создаем колонку со статусом оплаты и заполняем её
 *
 * @since	2.4.0
 */
add_filter( 'manage_shop_order_posts_columns', 'wpsl_get_order_columns_head' );
function wpsl_get_order_columns_head( $defaults ) {
	// Изменяем существующие колонки
	$defaults['title']  = __( 'Order Code', 'wpsl' );
	$defaults['author'] = __( 'Customer', 'wpsl' );
	// Добавляем новые колонки  
	$defaults['summa'] = __( 'Summa', 'wpsl' );
	foreach ( wpsl_order_statuses() as $key => $val ) {
		$defaults[$key] = $val;
	}
	
	if ( wpsl_opt( 'shipping' ) != '1' && $key == 'delivery' ) {
		unset( $defaults['delivery'] );
	}
	
	unset( $defaults['taxonomy-wpsl_status'] );
	return $defaults;
}

// Fill new columns in orders
add_filter( 'manage_shop_order_posts_custom_column', 'wpsl_fill_payment_status_column', 5, 2 );
function wpsl_fill_payment_status_column( $column_name, $post_id ){
	//print_r( wpsl_get_statuses() );
	$statuses = wpsl_get_statuses();
	switch ( $column_name ) {
		case 'summa':
			echo wpsl_get_meta( $post_id, 'summa' ) . ' ' . wpsl_opt();
			break;
	}
	foreach ( wpsl_order_statuses() as $key => $val ) {
		if ( $column_name == $key ) {
			$post_terms = get_the_terms( $post_id, 'wpsl_status' );
			if ( $post_terms ) {
				foreach ( $post_terms as $post_term ) {
					if ( esc_attr( get_term_meta( $post_term->term_id, 'status_type', 1 ) ) == $key ) {
						echo $post_term->name;
					}
				}
			}
		}
	}
}

add_filter( 'post_row_actions', 'wpsl_remove_property', 10, 2 );
add_filter( 'product_row_actions', 'wpsl_remove_property', 10, 2 );
function wpsl_remove_property( $actions, $post ) {
	if ( current_user_can( 'edit_posts' ) && isset( get_current_screen()->parent_file ) && get_current_screen()->parent_file == 'edit.php?post_type=shop_order' ) {
		unset( $actions['inline hide-if-no-js'] );
	}
	return $actions;
}


// Свойства заказа ( произвольные поля ) по умолчанию при ручном добавлении
add_action( 'wp_insert_post', 'wpsl_set_order_defaults' );
function wpsl_set_order_defaults( $postId ) {
	if ( isset( $_GET['post_type'] ) && $_GET['post_type'] == 'shop_order' ) {
		add_post_meta( $postId, 'summa', '0', true );
	}
	return true;
}


$taxname = 'wpsl_status';
// Add new custom fields
add_action( "{$taxname}_add_form_fields", 'wpsl_add_status_custom_fields' );
function wpsl_add_status_custom_fields( $taxonomy_slug  ){
	?>
	<div class="form-field">
		<label for="tag-status_type"><?php _e( 'Status type', 'wpsl' ); ?></label>
		<select name="extra[status_type]" required="" aria-required="true">
			<?php
			foreach ( wpsl_order_statuses() as $key => $val ) {
				echo '<option value="' . $key . '">' . $val . '</option>';
			}
			?>
		</select>
		<p><?php _e( 'Groups status type', 'wpsl' ); ?></p>
	</div>
	<?php
}

// Edit wpsl_status
add_action("{$taxname}_edit_form_fields", 'wpsl_edit_status_custom_fields');
function wpsl_edit_status_custom_fields( $term ) {
	?>
		<tr class="form-field">
			<th scope="row" valign="top"><label><?php _e( 'Status type', 'wpsl' ); ?></label></th>
			<td>
				<select name="extra[status_type]" required="" aria-required="true">
					<?php
					echo '<option value="">' . __( 'Select type status', 'wpsl' ) . '</option>';
					foreach ( wpsl_order_statuses() as $key => $val ) {
						echo '<option value="' . $key . '" ' . selected( $key, esc_attr( get_term_meta( $term->term_id, 'status_type', 1 ) ), false ) . '>' . $val . '</option>';
					}
					?>
				</select><br />
				<span class="description"><?php _e( 'Groups status type', 'wpsl' ); ?></span>
			</td>
		</tr>
	<?php
}

// Save wpsl_status
add_action( "create_{$taxname}", 'wpsl_save_status_custom_field' );
add_action( "edited_{$taxname}", 'wpsl_save_status_custom_field' );
function wpsl_save_status_custom_field( $term_id ) {
	if ( ! isset($_POST['extra']) ) return;
	if ( ! current_user_can('edit_term', $term_id) ) return;
	if (
		! wp_verify_nonce( $_POST['_wpnonce'], "update-tag_$term_id" ) && // wp_nonce_field( 'update-tag_' . $tag_ID );
		! wp_verify_nonce( $_POST['_wpnonce_add-tag'], "add-tag" ) // wp_nonce_field('add-tag', '_wpnonce_add-tag');
	) return;

	// Все ОК! Теперь, нужно сохранить/удалить данные
	$extra = wp_unslash($_POST['extra']);

	foreach( $extra as $key => $val ){
		// проверка ключа
		$_key = sanitize_key( $key );
		if( $_key !== $key ) wp_die( 'bad key'. esc_html($key) );

		// cleaning
		$val = sanitize_text_field( $val );

		// saving
		if( ! $val )
			delete_term_meta( $term_id, $_key );
		else
			update_term_meta( $term_id, $_key, $val );
	}

	return $term_id;
}


/**
 * wpStore
 *
 * File for create support tickets
 *
 * @author	wpStore
 * @since	2.4
 */
add_action( 'admin_menu', 'wpsl_support_in_menu', 20 );
function wpsl_support_in_menu() {
	if ( wpsl_opt( 'support' ) == '1' ) {
		add_submenu_page( 'wpsl_options', __( 'Support tickets', 'wpsl' ), __( 'Support', 'wpsl' ), 'install_plugins', 'edit.php?post_type=support', '' ); 
	}
}