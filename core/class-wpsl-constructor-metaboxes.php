<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if ( !is_admin() ) return;

/**
 * wpStore
 *
 * Class for create metaboxes
 * 
 * @inspiration	https://wp-kama.ru/id_6732/kama_post_meta_box-sozdaem-metapolya-dlya-zapisej.html
 * @fork	    wpStore
 * @since	    2.1.0
 */
if( ! class_exists( 'WPSL_Meta_Boxes' ) ) {
	class WPSL_Meta_Boxes {
		
		/**
		 * Конструктор
		 * @param array $options Опции по которым будет строиться метаблок
		 */
		function __construct( $options ) {
			$this->options = $options;
			$this->prefix = $this->options['id'] .'_';
			add_action( 'add_meta_boxes', array( &$this, 'create' ) );
			add_action( 'save_post', array( &$this, 'save' ), 1, 2 );
		}
		
		/**
		 * Конструктор
		 */
		function create() {
			foreach ( $this->options['post'] as $post_type ) {
				if ( current_user_can( $this->options['cap'] ) ) {
					add_meta_box( $this->options['id'], $this->options['name'], array( &$this, 'fill' ), $post_type, $this->options['pos'], $this->options['pri'] );
				}
			}
		}
		
		/**
		 * Собираем элементы управления
		 */
		function get_controls( $params ) {
			$html = '';
			global $post;
			foreach ( $params as $k => $elem ) {
				$value = get_post_meta( $post->ID, $k, true ) != '' ? get_post_meta( $post->ID, $k, true ) : $elem['std'];
				
				$attr = '';
				if ( wpsl_post( 'type-product' ) == 'external' && $k == '_product_url' ) {
					$attr = ' style="display: block;"';
				}
				if ( wpsl_post( '_digital' ) && $k == '_upload_file' ) {
					$attr = ' style="display: block;"';
				}
				
				$html .= '<div class="wpsl-metabox__content_item ' . $k . '" ' . $attr . '>';
				switch ( $elem['type'] ) {
					case 'text':
						$html .= '<label for="' . $k . '">' . $elem['title'] . '</label>
							<input name="' . $k . '" type="' . $elem['type'] . '" id="' . $k . '" value="' . esc_attr( $value ) . '" placeholder="' . $elem['ph'] . '" class="regular-text" />';
						break;
					case 'number':
						$html .= '<label for="' . $k . '">' . $elem['title'] . '</label>
							<input name="' . $k . '" min="0" type="' . $elem['type'] . '" id="' . $k . '" value="' . $value . '" placeholder="' . $elem['ph'] . '" class="regular-text" />';
						break;
					case 'date':
						$html .= '<label for="' . $k . '">' . $elem['title'] . '</label>
							<input name="' . $k . '" type="' . $elem['type'] . '" id="' . $k . '" value="' . esc_attr( $value ) . '" placeholder="' . $elem['ph'] . '" class="regular-text" />';
						break;				
					case 'textarea':
						$html .= '<label for="' . $k . '">' . $elem['title'] . '</label>
							<textarea rows="4" name="' . $k . '" type="' . $elem['type'] . '" id="' . $k . '" value="' . esc_attr( $value ) . '" placeholder="' . $elem['ph']  . '" class="large-text" />' . $value . '</textarea>';
						break;				
					case 'checkbox':
						$html .= '<span class="label">' . $elem['title'] . '</span><input class="wpsl-switch" name="' . $k . '" type="' . $elem['type'] . '" id="' . $k . '"' . checked( $value, 'on', false ) . ' /><label class="switch-label" for="' . $k . '"></label>';
						break;							
					case 'select':
						$html .= '<label for="' . $k . '">' . $elem['title'] . '</label>
							<select name="' . $k . '" id="' . $k . '">';
							foreach( $elem['args'] as $val => $name ){
								$html .= '<option value="' . $val . '" ' . selected( $value, $val, false ) . '>' . $name . '</option>';
							}
							$html .= '</select>';
						break;
					case 'wp_editor':
						$html .= '<label for="' . $k . '">' . $elem['title'] . '</label>';
						wp_editor( $value, $k,
							array(
								'wpautop'          => true,
								'textarea_name'	   => $k,
								'media_buttons'	   => true,
								'drag_drop_upload' => true,
								'teeny'			   => false,
								'quicktags'		   => true,
								'textarea_rows'	   => 5,
							)
						);
						break;
					case 'gallery':
						$html .= '<label for="' . $k . '">' . $elem['title'] . '</label>';
						$html .= '<div id="gallery_' . $k . '" class="' . $k . '">';
						if ( $value != '' && $temp = explode( ',', $value ) ) {
							foreach ( $temp as $t_val ) {
								$img_attr = wp_get_attachment_image_src( $t_val , array( 54, 54 ) );
								$html .= '<img class="wpsl-img" src="' . $img_attr[0] . '" width="' . $img_attr[1] . '" height="' . $img_attr[2] .'" data-id="'.$t_val.'">';
							}
						}
						$html .= '<span class="wpsl-img-upload"><i class="dashicons dashicons-plus"></i></span><input type="hidden" id="'. $k .'" name="'. $k .'" value="' . esc_attr( $value ) . '" />';
						$html .= '</div>';
						break;
					case 'char':
						global $post;
						$atts = new WPSL_Product_Attributes_Single();
						$html .= $atts->get_list( $post->ID );
						break;
					case 'variations':
						$atts = new WPSL_Product_Variations();
						$html .= $atts->get_list( $post->ID );
						break;
					case 'uploader':
						$class = ( get_post_meta( $post->ID, '_digital', true ) == true ) ? ' active' : '';
						$html .= '
						<label for="' . $k . '">' . $elem['title'] . '</label>
						<div>
							<table class="uploader wp-list-table widefat fixed striped tags' . $class . '">
								<thead>
									<tr>
										<th scope="col" class="manage-column column-id" style="width: 25px;">ID</th>
										<th scope="col" class="manage-column column-url">' . __( 'URL', 'wpsl' ) . '</th>
										<th scope="col" class="manage-column column-btn" style="width: 15px;"></th>
										<th scope="col" class="manage-column column-remove" style="width: 15px;"></th>
									</tr>
								</thead>
								<tbody class="files-list">		
									<tr class="column-row">
										<td class="column-id"></td>
										<td class="column-url">
											<input type="text" name="' . $k . '" placeholder="' . __( 'URL', 'wpsl' ) . '" value="' . esc_attr( $value ) . '" />
										</td>
										<td class="column-btn" title="' . __( 'Upload', 'wpsl' ) . '"><i class="upload_image_button dashicons dashicons-download"></i></td>
										<td class="column-remove" title="' . __( 'Delete', 'wpsl' ) . '"><i class="remove_image_button dashicons dashicons-no"></i></td>
									</tr>
								</tbody>
							</table>
						</div>';
						break;
					case 'custom':
						$html .= $elem['callback']();
						break;
				}
				if ( isset( $elem['desc'] ) ) {
					$html .= '<span class="description">' . $elem['desc'] . '</span>';
				}
				$html .= '</div>';
			}
			return $html;
		}
		
		/**
		 * Заполняем метабокс
		 */
		function fill() {
			global $post;
			
			wp_nonce_field( $this->options['id'], $this->options['id'] . '_wpnonce', false, true );
			
			echo '<div class="wpsl-metabox">';
			
			// just product
			if ( $this->options['id'] == 'product' ) {
				echo '<div class="wpsl-metabox__content_head">' . $this->get_controls( $this->options['args'][0]['fields'] ) . '</div>';
				array_shift( $this->options['args'] );
			}
			
			// menu
			if ( isset( $this->options['type'] ) && $this->options['type'] == 'tabs' ) {
				echo '<ul class="wpsl-metabox__menu">';
				foreach ( $this->options['args'] as $k => $param ) {
					
					$class = $k == 0 ? 'tab-active ' : ' ';
					
					$class .= $param['rel'] == '' || get_post_meta( $post->ID, 'type-product', true ) == $param['rel'] ? 'active ' : 'noactive ';
					echo '<li class="' . $class . $param['class'] . '" data-rel="' . $param['rel'] . '"><i class="' . $param['icon'] . '"></i>' . $param['title'] . '</li>';
				}
				echo '</ul>';
			}

			// tabs
			foreach ( $this->options['args'] as $k => $param ) {
				if ( current_user_can( $this->options['cap'] ) ) {
					
					$class = $k == 0 ? 'active tab-active ' : ' ';
					
					if ( isset( $this->options['type'] ) ) {
						$class .= $this->options['type'];
					}
					
					echo '<div class="wpsl-metabox__content ' . $class . $param['class'] . '">';
					echo $this->get_controls( $param['fields'] );
					echo '</div>';
				}
			}
			
			echo '</div>';
			
		}
		
		/**
		 * Save metabox
		 * @param array $options Опции по которым будет строиться метаблок
		 */
		function save( $post_id, $post ){
			if ( @!wp_verify_nonce( $_POST[ $this->options['id'] . '_wpnonce' ], $this->options['id'] ) ) return;
			if ( !current_user_can( 'edit_post', $post_id ) ) return;
			if ( !in_array( $post->post_type, $this->options['post'] ) ) return;
			foreach ( $this->options['args'] as $param ) {
				if ( current_user_can( $this->options['cap'] ) ) {
					foreach ( $param['fields'] as $k => $elem ) {
						if ( isset( $_POST[$k] ) && $_POST[$k] != '' ) {
							update_post_meta( $post_id, $k, wpsl_clean( $_POST[$k] ) );
						} else {
							delete_post_meta( $post_id, $k );
						}
					}
					$price = isset( $_POST['_sale_price'] ) && $_POST['_sale_price'] ? $_POST['_sale_price'] : $_POST['_regular_price'];
					update_post_meta( $post_id, '_price', wpsl_clean( $price ) );
				}
			}
		}
	}
}