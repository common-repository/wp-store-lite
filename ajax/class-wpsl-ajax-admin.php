<?php

if ( ! defined( 'ABSPATH' ) ) exit;


if( !is_admin() ) return;


/**
 * Register ajax events in admin page
 */
class WPSL_Ajax_Admin {
	/**
	 * Class constructor
	 */
	public function __construct() {
		if ( is_admin() && wp_doing_ajax() ) {
			
			remove_filter( 'post_type_link', 'wpsl_get_permalink_change', 10 );
			
			add_action( 'wp_ajax_updatePrice',      array( &$this, 'update_price' ) );
			add_action( 'wp_ajax_updateHit',        array( &$this, 'update_bestsellers' ) );
			
			add_action( 'wp_ajax_save_option',      array( &$this, 'save_options' ) );
			
			add_action( 'wp_ajax_test_sms',         array( &$this, 'test_sms' ) );
			
			add_action( 'wp_ajax_save_attribute',   array( &$this, 'add_attribute' ) );
			add_action( 'wp_ajax_sort_attribute',   array( &$this, 'sort_attribute' ) );
			add_action( 'wp_ajax_remove_attribute', array( &$this, 'remove_attribute' ) );
			add_action( 'wp_ajax_edit_attribute',   array( &$this, 'edit_attribute' ) );
			
			add_action( 'wp_ajax_save_single_product_atts',   array( &$this, 'save_product_atts' ) );
			add_action( 'wp_ajax_create_product_atts',        array( &$this, 'add_product_atts' ) );
			
			add_action( 'wp_ajax_add_product_variations',     array( &$this, 'add_product_variations' ) );
			add_action( 'wp_ajax_save_product_variations',    array( &$this, 'save_product_variations' ) );
			
			add_action( 'wp_ajax_import_form',      array( &$this, 'get_import_form' ) );
			add_action( 'wp_ajax_import_products',  array( &$this, 'get_import_products' ) );
		}
	}
	
	/**
	 * Update "Price" meta field from product list
	 */
	function update_price() {
		update_post_meta( (int)$_POST['product_id'], '_price', esc_attr( $_POST['price_val'] ) );
		die();
	}

	/**
	 * Update "Best sellers" meta field from product list
	 */
	function update_bestsellers() {
		update_post_meta( (int)$_POST['product_id'], 'hit_product', esc_attr( $_POST['hit_val'] ) );
		echo '<span style="color:#8BC34A">' . __( 'Saved', 'wpsl' ) . '</span>';
		wp_die();
	}

	/**
	 * Add option page to admin
	 */
	public function save_options() {
		if ( !current_user_can( 'manage_options' ) ) return;
		$defaults = array();
		update_option( 'wpsl_option', wp_parse_args( $_POST['str'], $defaults ) );
		flush_rewrite_rules();
		wp_die();
	}

	/**
	 * Test of working sms service
	 */
	public function test_sms() {
		$result = wpsl_send_sms( wpsl_opt( 'phone_admin' ), __( 'SMS sending is works!', 'wpsl' ), true );
		wp_die( $result );
	}
	
	/*
	 * Add product attributes
	 * @since 2.7
	 */
	public function add_attribute() {
		$attr = (array) json_decode( wp_unslash( $_POST['json'] ) );
		if ( $attr && $_POST['term_id'] ) {
			$id = (int) $attr['attribute_id'];
			if ( empty( $attr['attribute_label'] ) ) {
				$attr['attribute_label'] = $attr['attribute_name'];
			}
			$attr_id = wpsl_insert_attr( $attr );
			if ( empty( $id ) ) {
				$query = new WPSL_Product_Attributes();
				$attr = (array) $query->get( "attribute_id=$attr_id" );
				$attr = (array) $attr[0];
			}

			$filterable = ( $attr['attribute_filterable'] == 1 ) ? __( 'Yes', 'wpsl' ) : __( 'No', 'wpsl' );
			$variable   = ( $attr['attribute_variable'] == 1 ) ? __( 'Yes', 'wpsl' ) : __( 'No', 'wpsl' );
			$html = '
			<tr class="column-row" data-id="' . $attr['attribute_id'] . '">
				<td class="column-drag"><i class="dashicons dashicons-editor-justify"></i></td>
				<td class="column-name">
					<div class="row-name">' . $attr['attribute_name'] . '</div>
					<div class="row-actions">
						<span class="edit-attr" data-id="' . $attr['attribute_id'] . '" data-term_id="' . (int) $_POST['term_id'] . '">' . __( 'Edit', 'wpsl' ) . ' | </span>
						<span class="delete-attr" data-id="' . $attr['attribute_id'] . '">' . __( 'Delete', 'wpsl' ) . '</span>
					</div>
				</td>
				<td class="column-label">' . $attr['attribute_label'] . '</td>
				<td class="column-measure">' . $attr['attribute_measure'] . '</td>
				<td class="column-desc">' . $attr['attribute_desc'] . '</td>
				<td class="column-filterable">' . $filterable . '</td>
				<td class="column-variable">' . $variable . '</td>
			</tr>';
			$data = array(
				"html"            => $html,
				"status"          => (int) $id == '' ? 'add' : 'update',
				"attribute_id"    => (int) $attr['attribute_id'],
				"attribute_label" => $attr['attribute_label'],
			);
			echo json_encode( $data );
		}
		wp_die();
	}
	
	/**
	 * Sort common attributes
	 * @since 2.7
	 */
	public function sort_attribute() {
		if ( isset( $_POST['sortable'] ) ) {
			$atts = json_decode( $_POST['sortable'] );
			if ( is_array( $atts ) ) {
				foreach ( $atts as $key => $attr_id ) {
					$result = wpsl_update_attr_param( $attr_id, array( 'attribute_position' => $key ) );
				}
				wp_die( true );
			}
		}
	}
	
	/**
	 * Remove product attributes
	 * @since 2.7
	 */
	public function remove_attribute() {
		if ( isset( $_POST['attr_id'] ) ) {
			wpsl_remove_attr( (int) $_POST['attr_id'] );
		}
		wp_die();
	}
	
	/**
	 * Edit product attributes
	 * @since 2.7
	 */
	public function edit_attribute() {
		if ( isset( $_POST['attr_id'] ) ) {
			$product_id = isset( $_POST['product_id'] ) ? (int)$_POST['product_id'] : '';
			$new = new WPSL_Product_Attributes_Common();
			echo $new->fill_constructor( (int)$_POST['term_id'], (int)$_POST['attr_id'], $product_id );
		}
		wp_die();
	}
	
	/**
	 * Save single product attributes
	 * @since 2.7
	 */
	public function save_product_atts() {
		if ( isset( $_POST['post_id'] ) ) {
			$post_id = (int)$_POST['post_id'];
			$old = $new = array();
			// get old atts	
			$old_atts = get_post_meta( $post_id, '_atts', true );
			if ( is_array( $old_atts ) ) {
				foreach ( $old_atts as $attr ) {
					$attr = json_decode( $attr );
					$old[$attr->attribute_label] = $attr->attribute_value;
				}
			}
			
			// get new atts
			if ( !isset( $_POST['atts'] ) || $_POST['atts'] == '' ) {
				delete_post_meta( $post_id, '_atts' );
				foreach( $old as $key => $val ) {
					delete_post_meta( $post_id, $key );
				}
			} else {
				update_post_meta( $post_id, '_atts', $_POST['atts'] );
				$new_atts = get_post_meta( $post_id, '_atts', true );
				foreach ( $new_atts as $attr ) {
					$attr = json_decode( $attr );
					$new[$attr->attribute_label] = $attr->attribute_value;
				}
				
				// get deleted atts and delete him
				$deleted = array_diff( $old, $new );
				foreach ( $deleted as $key => $val ) {
					delete_post_meta( $post_id, $key );
				}
				
				// update atts
				foreach ( $new as $key => $val ) {
					update_post_meta( $post_id, $key, $val );
				}
			}
		}
		wp_die();
	}
	
	/**
	 * Create attributes group in single product
	 * @since 2.0
	 */
	public function add_product_atts() {
		$html = '';
		$row = new WPSL_Product_Attributes_Single();
		$atts = wpsl_sort_atts_by( wpsl_get_atts_by_term_id( (int)$_POST['term_id'] ), 'attribute_position' );
		foreach ( $atts as $attr ) {
			$html .= $row->fill_row( $attr );
		}
		wp_die( $html );
	}
	
	/**
	 * Create variations from attributes
	 * @since 2.0
	 */
	public function add_product_variations() {
		if ( !empty( (int)$_POST['post_id'] ) ) {
			$variations = new WPSL_Product_Variations();
			$html = $variations->fill_row( (int)$_POST['post_id'] );
			wp_die( $html );
		}
	}
	
	/**
	 * Save product variations
	 * @since 2.0
	 */
	public function save_product_variations() {
		if ( !empty( (int)$_POST['product_id'] ) ) {
			$variations = new WPSL_Product_Variations();
			$html = $variations->save_variations( (int)$_POST['product_id'], wp_unslash( $_POST['variations'] ) );
			wp_die( $html );
		}
	}
	
	/**
	 * Get product import form
	 * @since 2.7
	 */
	public function get_import_form() {
		if( empty( $_FILES ) ) {
			return wp_send_json_error( __( 'Files not found', 'wpsl' ) );
		}
		
		require( WPSL_DIR . 'core/wpsl-import-functions.php' );
		
		// фильтр допустимых типов файлов: разрешим только csv
		add_filter( 'upload_mimes', function( $mimes ){
			return [
				'csv' => 'text/csv',
			];
		});

		$uploaded_imgs = array();
		foreach( $_FILES as $file_id => $data ){
			$attach_id = media_handle_upload( wpsl_clean( $file_id ), 0 );
			// ошибка
			if( is_wp_error( $attach_id ) ) {
				$uploaded_imgs[] = 'Ошибка загрузки файла `'. wpsl_clean( $data['name'] ) .'`: '. $attach_id->get_error_message();
			} else {
				$uploaded_imgs[] = wp_get_attachment_url( $attach_id );
			}
		}
		
		$delimiter = ';'; // разделитель поля
		$enclosure = '"'; // символ ограничителя поля
		$examples = wpsl_get_examples( $attach_id, $delimiter, $enclosure );
/* 		echo '<pre>';
		print_r( $examples );
		echo '</pre>'; */
		?>
		<header>
			<h2><?php _e( 'Binding a column to a field', 'wpsl' ); ?></h2>
			<p><?php _e( 'Select the columns of the CSV file to assign the product field, or to ignore them.', 'wpsl' ); ?></p>
		</header>
		<form class="wpsl-import__form" action="" method="POST">
			<table class="widefat striped">
				<thead>
					<tr>
						<th><?php _e( 'Column', 'wpsl' ); ?></th>
						<th width="150px;"><?php _e( 'Set field', 'wpsl' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php echo wpsl_import_form( $examples ); ?>
				</tbody>
				<tfoot>
					<tr>
						<th><?php _e( 'ID of attachment', 'wpsl' ); ?></th>
						<th><input type="text" name="attach_id" value="<?php echo $attach_id; ?>"></th>
					</tr>
					<tr>
						<th><?php _e( 'Delimetr', 'wpsl' ); ?></th>
						<th><input type="text" name="delimetr" value="<?php echo $delimiter; ?>"></th>
					</tr>
				</tfoot>
			</table>
			<button type="submit" class="button button-primary"><?php _e( 'Import', 'wpsl' ); ?></button>
		</form>
		<?php
		wp_die();
	}
	
	/**
	 * File for import products from csv file
	 *
	 * This is beta version!
	 *
	 * @author	wpStore
	 * @since	2.1
	 */
	public function get_import_products() {
		$list = '<pre>';
		wp_parse_str( $_POST['str'], $params );
/* 		echo '<pre>';
		print_r( $params );
		echo '</pre>'; */
		

		// старт счетчика времени импорта всех товаров
		$start = microtime(true);
		$isv_file = fopen( wp_get_attachment_url( $params['attach_id'] ), 'r' );		
		if ( $isv_file !== FALSE ) {
			
			require( WPSL_DIR . 'core/wpsl-import-functions.php' );
			
			global $wpdb;
					
			// $was_suspended = wp_suspend_cache_addition();
			// wp_suspend_cache_addition( true );
			

			$list .= '<table class="wpsl-import__table">';
			$list .= '<tr><td>' . __( 'Line', 'wpsl' ) . '</td><td>' . __( 'Product', 'wpsl' ) . '</td><td>' . __( 'Categories', 'wpsl' ) . '</td><td>' . __( 'Attributes', 'wpsl' ) . '</td><td>' . __( 'Total time', 'wpsl' ) . '</td></tr>';
			
			
			/**
			 * Проходим по всем строкам в csv файле и собираем в массив все глобальные атрибуты.
			 * Очищаем массив от дублирующихся значений.
			 * Перебираем массив в цикле и ищем текущий атрибут по названию через wpsl_get_attr_by_name() без кеша (так получаем свежие данные).
			 * Существующие атрибуты пропускаем, несуществующие загоняем в базу данных.
			 */
			$global_atts = array();
			while( false !== ( $row = fgetcsv( $isv_file, 1000, $params['delimetr'], $enclosure = '"' ) ) ) {
 				if ( ( isset( $params['global'] ) && !$params['global'] ) || empty( $params['global'] ) ) continue;

				foreach ( $params['global'] as $k => $param ) {
					if ( !$row[$k] ) continue;

					$global_atts[] = array(
						'attribute_name'    => wpsl_clean( $param['attribute_name'] ),
						'attribute_label'   => _truncate_post_slug( sanitize_title( $param['attribute_name'] ) ),
						'attribute_measure' => wpsl_clean( $param['attribute_measure'] ),
					);
				}
			}
			
			$global_atts = array_unique( $global_atts, SORT_REGULAR );
			foreach ( $global_atts as $attr ) {
				$data = array(
					'attribute_name'    => $attr['attribute_name'],
					'attribute_label'   => $attr['attribute_label'],
					'attribute_measure' => $attr['attribute_measure'],
				);
				
				if ( $attr = wpsl_get_attr_by_name( $attr['attribute_name'], false ) ) {
					$data['attribute_id'] = $attr->attribute_id;
				}
				wpsl_insert_attr( $data );
			}
			wp_cache_delete( 'wpsl_attributes' );
			wp_cache_flush();
			
			$global_atts = array();
			if ( $atts = wpsl_get_atts() ) {
				foreach ( $atts as $attr ) {
					$global_atts[$attr->attribute_id] = $attr->attribute_name;
				}
			}
			
			
			/**
			 * Прогоняем все строки начиная с первой.
			 */
			$user_id = get_current_user_id();
			$from_cache = false;
			$line = 1;
			$insert_products = $insert_cats = $add_post_meta_time = 0;
			$isv_file = fopen( wp_get_attachment_url( $params['attach_id'] ), 'r' );
			while( false !== ( $row = fgetcsv( $isv_file, 1000, $params['delimetr'], $enclosure = '"' ) ) ) {
				
				$product = array();
				foreach ( $params['fields'] as $k => $v ) {
				if ( $v && ( $v != 'individual' || $v != 'global' ) ) {
						$product[$v] = $row[$k];
					}
				}
				
				if ( empty( $product['post_title'] ) ) {
					continue;
				}
				
				// запуск таймера, отслеживаем скорость импорта
				$insert = microtime(true);
				
				// $row - отдельная импортируемая строка в виде числового массива
				// перебираем каждый элемент массива
				/**
				 * Добавляем параметры товара "по умолчанию" если они не переданы:
				 * Тип товара - "Простой"
				 * Запасы     - "В наличии"
				 */
				if ( empty( $product['type-product'] ) ) {
					$product['type-product'] = 'simple';
				}
				if ( empty( $product['_stock_status'] ) ) {
					$product['_stock_status'] = 'instock';
				}
				
				// set thumbnail
				if ( isset( $product['_thumbnail_id'] ) && $product['_thumbnail_id'] ) {
					if ( $_thumbnail_id = wpsl_insert_thumb( $product['_thumbnail_id'] ) ) {
						$product['_thumbnail_id'] = $_thumbnail_id;
					} else {
						unset( $product['_thumbnail_id'] );
					}
				}
				if ( isset( $product['product_thumbs'] ) && $product['product_thumbs'] ) {
					// сначала определим количество дополнительных миниатюрок и в цикле их все обрабатываем
					$thumbs = array();
					foreach ( $product['product_thumbs'] as $thumb ) {
						$thumbs[] = wpsl_insert_thumb( $thumb );
					}
					$product['product_thumbs'] = implode( ',', $thumbs );
				}
				

				// собрали базовые данные, теперь создаем товар
				// сначала проверим товар по названию, если товар с таким названием существует, то обновляем
				// если не существует, то создаем новый
				$wp_insert_start = microtime(true);
				
				// здесь wpdb работает в 5 раз быстрее get_posts
				$cache_key = 'old_product_titles';
				$old_product_titles = wp_cache_get( $cache_key );
				if ( false === $old_product_titles ) {
					$old_product_titles = $wpdb->get_results( "SELECT ID, post_title FROM $wpdb->posts WHERE post_type = 'product'" );
					
					wp_cache_set( $cache_key, $old_product_titles );
				}
				
/* 				echo '<pre>';
				var_dump( $from_cache );
				print_r( $old_product_titles );
				echo '<br>';
				echo '</pre>'; */
 				
				$old_product_id = $product_id = '';
				if ( $old_product_titles && is_array( $old_product_titles ) ) {
					foreach ( $old_product_titles as $old_product_title ) {
						if( $old_product_title->post_title === $product['post_title'] ) {
							$old_product_id = $old_product_title->ID;
							break;
						}
					}
				}
				
 				/*$post_title = $product['post_title'];
				$old_product_id = $wpdb->get_var( "SELECT ID FROM $wpdb->posts WHERE post_type = 'product' AND post_title = '$post_title'" );
				echo '<pre>ID: ';
				print_r( $old_product_id );
				echo '</pre>'; */
				
				if ( $old_product_id ) {
					// update product
					$product_id = $old_product_id;
					$product_data = array(
						'ID'            => $product_id,
						'post_title'    => $product['post_title'],
						'post_author'   => isset( $product['post_author'] ) ? $product['post_author'] : $user_id,
						'post_content'  => isset( $product['post_content'] ) ? $product['post_content'] : '',
					);
					// Вставляем данные в БД и одновременно получаем id созданной записи
					wpsl_insert_post( $product_data, true );
					$from_cache = true;
				} else {
					// add product
					$product_data = array(
						'post_title'    => $product['post_title'],
						'post_author'   => isset( $product['post_author'] ) ? $product['post_author'] : $user_id,
						'post_content'  => isset( $product['post_content'] ) ? $product['post_content'] : '',
					);
					$product_id = wpsl_insert_post( $product_data, true );
					$from_cache = false;
				}
				if( is_wp_error( $product_id ) ){
					$list .= '<td style="color: #ff0000;">Error: ' . $product_id->get_error_message() . '</td>';
				} else {
					$error = '';
					if ( $old_product_id != '' ) {
						$error = 'style="background-color: #ffcc00; padding: 1px 3px; border-radius: 2px;" title="' . __( 'The product exists, the data is updated', 'wpsl' ) . '"';
					} else {
						$error = 'style="background-color: #cddc39; padding: 1px 3px; border-radius: 2px;" title="' . __( 'The product is successfully created', 'wpsl' ) . '"';
					}
				}
				$wp_insert_end = round( ( microtime(true) - $wp_insert_start ), 4 );
				$insert_products += ( microtime(true) - $wp_insert_start );
				
				

				/**
				 * присваиваем созданной записи указанные категории
				 * среднее время выполнения - 0.008 сек
				 * поэтому, присвоение категорий и тегов занимает не более 10% времени
				 */
				$wp_insert_cats_start = microtime(true);
				if ( isset( $product['product_cat'] ) && $product['product_cat'] ) {
					$pc_ids = array();
					foreach ( (array)$product['product_cat'] as $cat ) {
						$pc_ids[] = wpsl_set_object_terms( $product_id, $cat, 'product_cat' );
					}
 					/* echo '<pre>ID: ';
					print_r( $pc_ids );
					echo '</pre>'; */
				}
				// присваиваем созданной записи указанные теги
				if ( isset( $product['product_tag'] ) && $product['product_tag'] ) {
					$tags = (array)$product['product_tag'];
					foreach ( $tags as $tag ) {
						wpsl_set_object_terms( $product_id, $tag, 'product_tag' );
					}
				}
				$wp_insert_cats_end = round( ( microtime(true) - $wp_insert_cats_start ), 4 );
				$insert_cats += ( microtime(true) - $wp_insert_cats_start );


				/**
				 * Загоняем данные в произвольные поля
				 */
				$update_post_meta = microtime(true);
				$defaults = array(
					'post_author',
					'post_content',
					'post_title',
					'post_excerpt',
					'post_status',
					'post_type',
					'comment_status',
					'post_password',
					'pinged',
					'post_parent',
					'menu_order',
					'guid',
					'import_id',
					'context',
					'product_cat',
					'product_tag',
					'individual',
					'global',
				);
				foreach ( $defaults as $field ) {
					unset( $product[$field] );
				}
				
				// set price
				if ( isset( $product['_regular_price'] ) && $product['_regular_price'] ) {
					$product['_price'] = $product['_regular_price'];
				}
				if ( isset( $product['_sale_price'] ) && $product['_sale_price'] ) {
					$product['_price'] = $product['_sale_price'];
				}
				
				$INSERT = $UPDATE = $key_value = $key_metaid = array();
				// получаем все метаполя товара и проверяем: переданные данные - для обновления или вставки
				$fields = $wpdb->get_results( "SELECT meta_id, meta_key, meta_value FROM $wpdb->postmeta WHERE post_id = '{$product_id}'", ARRAY_A );
				foreach ( $fields as $k => $v ) {
					$key_value[$v['meta_key']] = $v['meta_value'];
					$key_metaid[$v['meta_key']] = $v['meta_id'];
				}
				
				foreach ( $product as $key => $val ) {
					// если есть элемент с таким ключом, значит обновляем произвольное поле
					if ( array_key_exists( $key, $key_metaid ) ) {
						$UPDATE[] = $wpdb->prepare( '( %d, %d, %s, %s )', $key_metaid[$key], $product_id, $key, $val );
					} else {
						if ( $val ) {
							$INSERT[] = $wpdb->prepare( '( %d, %s, %s )', $product_id, $key, $val );
						}
					}
				}
				
				// create individual and global attributes
				if ( ( isset( $params['individual'] ) && $params['individual'] ) || ( isset( $params['global'] ) && $params['global'] ) ) {
					$atts = array();
					$attributes = ( isset( $params['individual'] ) ? $params['individual'] : array() ) + ( isset( $params['global'] ) ? $params['global'] : array() );
					foreach ( $attributes as $k => $param ) {
						if ( $row[$k] ) {
							$slug = _truncate_post_slug( sanitize_title( $param['attribute_name'] ) );
							$atts[] = json_encode( array(
								'attribute_name'     => wpsl_clean( $param['attribute_name'] ),
								'attribute_label'    => $slug,
								'attribute_value'    => $row[$k],
								'attribute_measure'  => wpsl_clean( $param['attribute_measure'] ),
								'attribute_variable' => 0,
							) );
						}
						// add attribute to single product
						if ( isset( $key_metaid ) && $key_metaid && array_key_exists( $slug, $key_metaid ) ) {
							$UPDATE[] = $wpdb->prepare( '( %d, %d, %s, %s )', $key_metaid[$slug], $product_id, wpsl_clean( $param['attribute_name'] ), wpsl_clean( $row[$k] ) );
						} else {
							if ( $row[$k] && $slug ) {
								$INSERT[] = $wpdb->prepare( '( %d, %s, %s )', $product_id, $slug, wpsl_clean( $row[$k] ) );
							}
						}
					}
					if ( $atts ) {
						if ( isset( $key_metaid ) && $key_metaid && array_key_exists( '_atts', $key_metaid ) ) {
							$UPDATE[] = $wpdb->prepare( '( %d, %d, %s, %s )', $key_metaid['_atts'], $product_id, '_atts', serialize( $atts ) );
						} else {
							$INSERT[] = $wpdb->prepare( '( %d, %s, %s )', $product_id, '_atts', serialize( $atts ) );
						}
					}
				}
				
				// add global attributes
 				if ( isset( $params['global'] ) && $params['global'] ) {
					foreach ( $params['global'] as $k => $param ) {
						if ( $row[$k] ) {
							$data = array(
								'attribute_name'    => wpsl_clean( $param['attribute_name'] ),
								'attribute_label'   => _truncate_post_slug( sanitize_title( $param['attribute_name'] ) ),
								'attribute_measure' => wpsl_clean( $param['attribute_measure'] ),
							);
							if ( $pc_ids ) {
								foreach ( $pc_ids[0] as $id ) {
									$data['attribute_term_id'] = $id;
								}
							}
							
							if ( in_array( wpsl_clean( $param['attribute_name'] ), $global_atts ) ) {
								$data['attribute_id'] = array_search( wpsl_clean( $param['attribute_name'] ), $global_atts );
							}
							wpsl_insert_attr( $data );
						}
					}
				}
				
			 	//echo '<pre>';
				//print_r( $UPDATE );
				//print_r( $INSERT );
				//echo '</pre>';

				/**
				 * Попробуем загнать все метаполя через глобальную wpdb
				 * Если использовать update_post_meta, 30% времени уходит на вставку метаполей
				 * По результатам тестов вставка через wpdb быстрее update_post_meta пропорционально количеству произвольных полей
				 */
				if ( $INSERT ) {
					$wpdb->query("INSERT INTO $wpdb->postmeta ( post_id, meta_key, meta_value ) VALUES " . implode( ',', $INSERT ) . " " );
				}
				
				if ( $UPDATE ) {
					$wpdb->query("INSERT INTO $wpdb->postmeta ( meta_id, post_id, meta_key, meta_value ) VALUES " . implode( ',', $UPDATE ) . " ON DUPLICATE KEY UPDATE meta_value = VALUES(meta_value); ");
				}
				// завершаем подсчет скорость выполнения скрипта для одного товара
				$add_post_meta_time += ( microtime(true) - $update_post_meta );
				
				
				
				$list .= '<tr><td>' . $line . '</td><td><span ' . $error . '>' . $wp_insert_end . ' sec.</span></td><td>' . round( $wp_insert_cats_end, 4 ) . ' sec.</td><td>' . round( ( microtime(true) - $update_post_meta ), 4 ) . ' sec.</td><td>' . round( ( microtime(true) - $insert ), 4 ) . ' sec.</td></tr>';
				
				$line++;
				
			}
			
			// завершаем подсчет скорость выполнения всего скрипта
			$end = ( microtime(true) - $start );
			
			$list .= '<tr><td></td><td>' . round( $insert_products, 3 ) . ' sec.</td><td>' . round( $insert_cats, 3 ) . ' sec.</td><td>' . round( $add_post_meta_time, 3 ) . ' sec.</td><td>' . round( $end, 3 ) . ' sec.</td></tr>';
			
			$list .= '</table>';
			
			// закрываем файл импорта
			fclose( $isv_file );
			// wp_suspend_cache_addition( false );
			
		} else {
			$list .= 'Проблема с загрузкой CSV файла, обновите страницу и повторите еще раз';
		}
		echo $list;

		// после завершения импорта, удалим csv файл из вложений
		wp_delete_attachment( $params['attach_id'], true );
		$list .= '</pre>';
		
		// пересчёт количества товаров по атрибутам
		if( ! wp_next_scheduled( 'wpsl_update_all_atts' ) ) {
			wp_schedule_single_event( time() + 10, 'wpsl_update_all_atts' ); // 1 раз через 10 секунд с текущего момента
		}
		
		wp_die();

	}
}
$wpslAPI = new WPSL_Ajax_Admin();