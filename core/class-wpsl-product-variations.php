<?php
if ( ! defined( 'ABSPATH' ) ) exit;


if ( !is_admin() ) return;


/**
 * Variations constructor
 */
class WPSL_Product_Variations{
	
	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'admin_footer', array( &$this, 'editor' ) );
	}
	
	/**
	 * Variation editor
	 */
	function editor() {
		$fields = array(
			array(
				'type'        => 'custom',
				'fill'        => '<h2>' . __( 'Variation editor', 'wpsl' ) . '</h2><i class="wpsl-hide-editor dashicons dashicons-no-alt"></i>',
			),
			array(
				'type'        => 'text',
				'name'        => '_sku',
				'title'       => __( 'Sku', 'wpsl' ),
				'value'       => '',
				'class'       => '',
				'placeholder' => __( 'Product sku', 'wpsl' ),
				'required'    => 0,
				'notice'      => __( 'Notice', 'wpsl' )
			),
			array(
				'type'        => 'uploader',
				'name'        => '_upload_file',
				'title'       => __( 'Upload file', 'wpsl' ),
				'value'       => '',
				'class'       => '',
				'placeholder' => __( 'Link to download', 'wpsl' ),
				'required'    => 0,
				'notice'      => __( 'Notice', 'wpsl' )
			),
		);
		$args = array(
			'action'  => '',
			'onclick' => 'edit_variation',
			'submit'  => __( 'Save', 'wpsl' ),
			'ajax'    => true,
		);
		echo wpsl_get_form( $fields, $args );
	}
	
	/**
	 * Save button
	 */
	function save_btn() {
		global $post;
		echo '<input type="button" class="button wpsl-save-product-variations" data-id="' . $post->ID . '" value="' . __( 'Save variations', 'wpsl' ) . '">';
		echo '<input type="button" class="button wpsl-create-variations" data-id="' . $post->ID . '" value="' . __( 'Create from attributes', 'wpsl' ) . '">';
	}
	
	/**
	 * Mass management
	 */
    function mass_management() {
		?>
		<div class="wpsl-variation-menegment">
			<select id="field_to_edit" class="variation_actions">
				<option value=""><?php _e( 'Mass management', 'wpsl' ); ?></option>
				<option value="delete_all"><?php _e( 'Delete all variations', 'wpsl' ); ?></option>
				<optgroup label="<?php _e( 'Pricing', 'wpsl' ); ?>">
					<option value="variable_price_set"><?php _e( 'Set price', 'wpsl' ); ?></option>
					<option value="variable_price_increase_fixed"><?php _e( 'Increase by a fixed value', 'wpsl' ); ?></option>
					<option value="variable_price_increase_percent"><?php _e( 'Increase by percent', 'wpsl' ); ?></option>
					<option value="variable_price_decrease_fixed"><?php _e( 'Decrease by a fixed value', 'wpsl' ); ?></option>
					<option value="variable_price_decrease_percent"><?php _e( 'Decrease by percent', 'wpsl' ); ?></option>
				</optgroup>
			</select>
			<input class="wpsl-value wpsl-validate-price" type="text" value="" placeholder="<?php _e( 'Value', 'wpsl' ); ?>" />
			<a class="button"><?php _e( 'Apply', 'wpsl' ); ?></a>
		</div>
		<?php
	}
	
	/**
	 * Enqueue script
	 */
    function enqueue_script() {
		?>
		<script>
		jQuery(document).ready(function() {
			var $ = jQuery;
			/*
			 * Mass managment
			 */
			$('.wpsl-variation-menegment .button').click(function(){
				var val = $(this).siblings('.wpsl-value').val(),
					act = $(this).siblings('.variation_actions').val();
				$('#wpsl-variations__list tr').each(function(i,elem){
					input = $(elem).children('.column-price').children('input[type="text"]');
					if ( input.val() === undefined ) {
						curent = Number(0);
					} else {
						curent = parseInt( input.val(), 10 );
					}
					
					price = 0;
					if ( act === 'variable_price_set' ) {
						price = val;
					} else if ( act === 'variable_price_increase_fixed' ) {
						price = Number(curent) + Number(val);
					} else if ( act === 'variable_price_increase_percent' ) {
						price = curent + ( curent / 100 * val );
					} else if ( act === 'variable_price_decrease_fixed' ) {
						price = curent - val;
					} else if ( act === 'variable_price_decrease_percent' ) {
						price = curent - ( curent / 100 * val );
					} else if ( act === 'delete_all' ) {
						$('#wpsl-variations__list tr').remove();
					}
					input.val(price);
				});
			});
			/*
			 * Create variations from attributes
			 */
			$('.wpsl-create-variations').click(function(){
				$('.wpsl-save-product-variations').addClass('button-primary');
				// ajax
				var form = $(this).parents('.wpsl-variations');
				form.append('<div class="wpsl-preloader"><img src="' + WPSA.preloader + '" /></div>');
				$.ajax({
					url  : ajaxurl,
					type : 'POST',
					cache: false,
					data: ({
						action : 'add_product_variations',
						post_id : $(this).data('id'),
					}),
					success: function(result){
						$(form).find('.no-items').remove();
						$('#wpsl-variations__list').append(result);
						form.children('.wpsl-preloader').remove();
					}
				});
			});
			/*
			 * Save variations
			 */
			$('.wpsl-save-product-variations').click(function(){
				var vars = [];
				arr = ['id','image','variation','price'];
				// each
				$('#wpsl-variations__list tr').each(function(i,elem){
					item = {};
					$.each(arr, function(i,value){
						if ( value === 'id' ) {
							item['item_id'] = $(elem).children('.column-'+value).text();
						}
						if ( value === 'image' ) {
							item['item_image'] = $(elem).children('.column-'+value).children('span').children('img').data('id');
						}
						if ( value === 'variation' ) {
							select = {};
							$(elem).children('.column-'+value).children('select').each(function(i,sel){
								select[$(sel).attr('name')] = $(sel).val();
							});
							item['item_variation'] = select;
						}
						if ( value === 'price' ) {
							item['item_price'] = $(elem).children('.column-'+value).children('input').val();
						}
					});
					vars.push(JSON.stringify(item));
				});
				var variations = $.extend({}, vars);
				if ( $.isEmptyObject(variations) ) {
					variations = '';
				}
				console.log(variations);
				// ajax
				var _this = $(this),
					form = _this.parents('.wpsl-variations');
				form.append('<div class="wpsl-preloader"><img src="' + WPSA.preloader + '" /></div>');
				$.ajax({
					url  : ajaxurl,
					type : 'POST',
					cache: false,
					data: ({
						action     : 'save_product_variations',
						variations : variations,
						product_id : _this.data('id'),
					}),
					success: function(data){
						$('#wpsl-variations__list').html(data);
						_this.removeClass('button-primary');
						form.children('.wpsl-preloader').remove();
					}
				});
			});
			/*
			 * Remove variation
			 */
			$(document).on('click', '#wpsl-variations__list .dashicons-trash', function(){
				$(this).parents('tr').remove();
				$('.wpsl-save-product-variations').addClass('button-primary');
				return false;
			});
			/*
			 * Upload image
			 */
			$(document).on('click', '.add-variation-img', function(){
				var send_attachment_bkp = wp.media.editor.send.attachment;
				var button = $(this);
				wp.media.editor.send.attachment = function(props, attachment) {
					//console.log( attachment );
					if ( typeof attachment.sizes.thumbnail === 'undefined' ) {
						$(button).children('img').attr('src', attachment.url);
					} else {
						$(button).children('img').attr('src', attachment.sizes.thumbnail.url);
					}
					$(button).children('img').attr('data-id', attachment.id);
					wp.media.editor.send.attachment = send_attachment_bkp;
				}
				wp.media.editor.open(button);
				$('.wpsl-save-product-variations').addClass('button-primary');
				return false; 		
			});
			/*
			 * Remove variation image
			 */
			$(document).on('click', '.remove-variation-img', function(){
				$(this).siblings('img').attr('src', 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');
				$(this).siblings('img').data('id', '');
				$('.wpsl-save-product-variations').addClass('button-primary');
				return false;
			});
		});
		</script>
		<style>
		.wpsl-variations__table{
			position: relative;
		}
		.wpsl-variations__table .column-id{
			width: 36px;
			text-align: center;
			padding: 8px 4px;
		}
		.wpsl-variations__table .column-action {
			width: 50px;
			padding: 8px 3px;
			text-align: center;
			overflow: hidden;
			text-overflow: ellipsis;
			white-space: nowrap;
		}
		.wpsl-variations__table tbody .column-action{
			padding: 12px 2px 4px 0;
			cursor: pointer;
			color: #6b6b6b;
		}
		.wpsl-variations__table .column-action i{
			padding: 0 2px;
			line-height: 21px;
		}
		.wpsl-variations__table .column-action .dashicons-admin-settings:hover{
			color: #006b96;
		}
		.wpsl-variations__table .column-action .dashicons-trash:hover {
			color: #f44336;
		}
		.wpsl-variations__table .column-image{
			width: 46px;
			padding: 0;
			text-align: center;
		}
		.wpsl-variations__table .column-image i{
			font-size: 15px;
			line-height: 21px;
			color: #6b6b6b;
			text-align: center;
		}
		.wpsl-variations__table .add-variation-img{
			color: #e5e5e5;
			padding: 10px 0px;
			box-sizing: border-box;
			font-size: 32px;
			width: 45px;
			height: 45px;
			cursor: pointer;
			overflow: hidden;
			position: relative;
		}
		.wpsl-variations__table .add-variation-img:hover .remove-variation-img{
			display: block;
		}
		.wpsl-variations__table .add-variation-img img{
			width: 100%;
			position: absolute;
			top: 0; left: 0;
			z-index: 7;
			border-radius: 3px;
			margin: 3px;
			width: calc(100% - 6px);
			height: calc(100% - 6px);
		}
		.wpsl-variations__table .add-variation-img .remove-variation-img{
			position: absolute;
			top: 0; right: 0;
			background-color: #ec5840;
			width: 16px;
			height: 16px;
			color: #fff;
			font-size: 14px;
			padding: 3px 2px;
			box-sizing: border-box;
			line-height: 6px;
			display: none;
			z-index: 9;
		}
		.wpsl-variations__table .add-variation-img:hover{
			color: #008ec2;
		}
		.wpsl-variations__table .column-price{
			width: 70px !important;
			text-align: center;
			padding: 8px 5px 8px 0;
		}
		.wpsl-variations__table .column-variation select{
			max-width: 23.8% !important;
			min-width: 50px;
			margin-right: 5px;
			width: 100%;
		}
		.wpsl-variations__table .no-items{
			text-align: center;
		}
		.wpsl-save-product-variations{
			margin-top: 10px !important;
		}
		.wpsl-create-variations{
			margin-top: 10px !important;
			float: right !important;
		}
		.wpsl-variation-menegment{
			margin-bottom: 10px;
		}
		.wpsl-variation-menegment > select{
			margin-right: 10px;
			float: left;
			max-width: 190px;
		}
		.wpsl-variation-menegment > input {
			width: 80px !important;
		}
		.wpsl-variation-menegment > a{
			line-height: 24px !important;
			height: 26px !important;
		}
		#edit_variation{
			display: none;
			position: fixed;
			z-index: 9999;
			background-color: #ffffff;
			left: 0;
			top: 100px;
			bottom: 100px;
			right: 0;
			margin: 0 auto;
			box-sizing: border-box;
			width: 100%;
			max-width: 600px;
			box-shadow: 0 0 9999px 9999px rgba(0, 0, 0, 0.5);
		}
		#edit_variation button[type="submit"]{
			background: #0085ba;
			border-color: #0073aa #006799 #006799;
			box-shadow: 0 1px 0 #006799;
			color: #fff;
			text-decoration: none;
			text-shadow: 0 -1px 1px #006799, 1px 0 1px #006799, 0 1px 1px #006799, -1px 0 1px #006799;
			display: inline-block;
			text-decoration: none;
			font-size: 13px;
			line-height: 26px;
			height: 28px;
			margin: 0;
			padding: 0 10px 1px;
			cursor: pointer;
			border-width: 1px;
			border-style: solid;
			-webkit-appearance: none;
			border-radius: 3px;
			white-space: nowrap;
			box-sizing: border-box;
			float: right;
		}
		#edit_variation .column-btn, #edit_variation .column-remove{
			padding: 12px 10px 12px 0;
			cursor: pointer;
		}
		#edit_variation .column-btn:hover, #edit_variation .column-remove:hover{
			color: #0085ba;
		}
		.wpsl-form {
			
		}
		.wpsl-form__row {
			padding: 12px 20px 13px;
			float: left;
			width: 100%;
			box-sizing: border-box;
		}
		.wpsl-form__row:nth-child(2n) {
			background-color: #f9f9f9;
		}
		.wpsl-form__row_title{
			font-weight: 700;
			margin-bottom: 5px;
			float: left;
		}
		.wpsl-form__row > h2{
			width: calc(100% - 43px);
			float: left;
			margin: 0;
		}
		.wpsl-form__row > i{
			width: 43px;
			float: left;
			height: 43px;
			position: absolute;
			right: 0;
			top: 0;
			cursor: pointer;
			line-height: 43px;
			border-left: 1px solid #f9f9f9;
		}
		.wpsl-form__row > i:hover{
			background-color: #f9f9f9;
		}
		.wpsl-form input, .wpsl-form select, .wpsl-form textarea{
			width: 100%;
		}
		.wpsl-form .wpsl-hidden{
			display: none;
		}
		</style>
		<?php
	}
	
	/**
	 * Save variations
	 */
	function save_variations( $product_id, $variations ) {

		$html = '';
		$product_old_variations = get_post_meta( $product_id, '_product_variations', true );
		$product_new_variations = wp_unslash( $variations );
		$variations_list = array();
		
		if ( isset( $product_new_variations ) && !empty( $product_new_variations ) ) {
			
			// data of parent product
			$parent_product    = get_post( $product_id );
			$product_metas_arr = array(
				'_price',
			);
			
			foreach ( $product_new_variations as $variation ) {
				$variation = json_decode( $variation );
				/**
				 * If the id of variation exists, then update all metas
				 */
				if ( !empty( $variation->item_id ) ) {
					foreach ( $product_metas_arr as $meta ) {
						if ( $meta != '_price' ) {
							if ( $val = get_post_meta( $product_id, $meta, true ) ) {
								update_post_meta( $variation->item_id, $meta, $val );
							} else {
								delete_post_meta( $variation->item_id, $meta );
							}
						} else {
							update_post_meta( $variation->item_id, $meta, $variation->item_price );
						}
					}

					if ( $variation->item_image ) {
						update_post_meta( $variation->item_id, '_thumbnail_id', $variation->item_image );
					} else {
						delete_post_meta( $variation->item_id, '_thumbnail_id' );
					}
					$url = !empty( $variation->item_image ) ? get_the_post_thumbnail_url( $variation->item_id, 'thumbnail' ) : 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';
					
					$html .= '
						<tr class="column-row">
							<td class="column-id">' . $variation->item_id . '</td>
							<td class="column-image"><span class="add-variation-img upload_image_variation dashicons dashicons-plus"><img src="' . $url . '" data-id="' . $variation->item_image . '"><span class="remove-variation-img">&times;</span></span></td>
							<td class="column-variation">' . $this->filling( $variation->item_variation, $product_id ) . '</td>
							<td class="column-price"><input class="wpsl-validate-price" type="text" value="' . $variation->item_price . '" name="" placeholder="' . __( 'Price', 'wpsl' ) . '" /></td>
							<td class="column-action" title="' . __( 'Action', 'wpsl' ) . '"><i class="wpsl-edit-variation dashicons dashicons-admin-settings" title="' . __( 'Edit', 'wpsl' ) . '"></i><i class="dashicons dashicons-trash" title="' . __( 'Delete', 'wpsl' ) . '"></i></td>
						</tr>';
						
					$variations_list[] = json_encode( array(
						'item_id'        => $variation->item_id,
						'item_image'     => $variation->item_image,
						'item_variation' => $variation->item_variation,
						'item_price'     => $variation->item_price,
					), JSON_UNESCAPED_UNICODE );
						
				}
				/**
				 * If the id of variation is not exists, then create
				 */
				else {
					$variation_data = array( 
						'post_status'	 => $parent_product->post_status,
						'post_title'	 => $parent_product->post_title,
						'comment_status' => $parent_product->comment_status,
						'post_author'	 => $parent_product->post_author,
						'post_type'	     => 'product_variation',
						'ping_status'	 => get_option( 'default_ping_status' ),
					 );
					$variation_id = wp_insert_post( wp_slash( $variation_data ) );
					foreach ( $product_metas_arr as $meta ) {
						if ( $meta != '_price' ) {
							update_post_meta( $variation_id, $meta, get_post_meta( $product_id, $meta, true ) );
						} else {
							update_post_meta( $variation_id, $meta, $variation->item_price );
						}
					}
					update_post_meta( $variation_id, '_thumbnail_id', $variation->item_image ); // вписываем id миниатюры
					set_post_thumbnail( $variation_id, $variation->item_image );
					
					// set id parent product
					update_post_meta( $variation_id, '_parent_id', $product_id );
					
					$url = !empty( $variation->item_image ) ? get_the_post_thumbnail_url( $variation_id, 'thumbnail' ) : 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';
					
					$html .= '
						<tr class="column-row">
							<td class="column-id">' . $variation_id . '</td>
							<td class="column-image"><span class="add-variation-img upload_image_variation dashicons dashicons-plus"><img src="' . $url . '" data-id="' . $variation->item_image . '"><span class="remove-variation-img">&times;</span></span></td>
							<td class="column-variation">' . $this->filling( $variation->item_variation, $product_id ) . '</td>
							<td class="column-price"><input class="wpsl-validate-price" type="text" value="' . $variation->item_price . '" name="" placeholder="' . __( 'Price', 'wpsl' ) . '" /></td>
							<td class="column-action" title="' . __( 'Action', 'wpsl' ) . '"><i class="wpsl-edit-variation dashicons dashicons-admin-settings" title="' . __( 'Edit', 'wpsl' ) . '"></i><i class="dashicons dashicons-trash" title="' . __( 'Delete', 'wpsl' ) . '"></i></td>
						</tr>';
						
					$variations_list[] = json_encode( array(
						'item_id'        => $variation_id,
						'item_image'     => $variation->item_image,
						'item_variation' => $variation->item_variation,
						'item_price'     => $variation->item_price,
					), JSON_UNESCAPED_UNICODE );
				}
			}
		} else {
			$html .= '
				<tr class="no-items">
					<td class="colspanchange" colspan="5">' . __( 'Variations of the product exist. Make sure that among the characteristics of eating is variable, and click "Create features".', 'wpsl' ) . '</td>
				</tr>
			';
		}
		
		update_post_meta( $product_id, '_product_variations', $variations_list );
		update_post_meta( $product_id, 'type-product', 'variable' );
		
			
/* 		// получаем id новых вариаций
		$n = json_decode( $variation );
		for ( $i = 0; $i < count( $n ); $i++ ) {
			$new_id[] = $n[$i][0];
		}

		// получаем id старых вариаций
		$n = json_decode( $old_variation );
		for ( $i = 0; $i < count( $n ); $i++ ) {
			$old_id[] = $n[$i][0];
		}
		
		// сравниваем и получаем id удаленных вариаций
		// и удаляем отсутствующие вариации безвозвратно
		$result = array_diff( $old_id, $new_id );
		foreach ( $result as $res ) {
			wp_delete_post( $res, true );
		} */

		return $html;
	}
	
	/**
	 * Get attributes
	 */
	function get_atts( $product_id ) {
		$arrays = array();
		// the collection of values of the attributes
		$variations = get_post_meta( $product_id, '_atts', true );
		if ( !empty( $variations ) ) {
			foreach ( $variations as $variation ) {
				$variation = json_decode( $variation );
				if ( $variation->attribute_variable == 1 ) {
					$arrays[$variation->attribute_label] = explode( '|' , $variation->attribute_value );
				}
			}
		}
		return $arrays;
	}
	
	/**
	 * Get variations
	 */
	function get_variations( $product_id ) {
		
		$result = array();
		
		$arrays = $this->get_atts( $product_id );
		
		while (list($key, $values) = each($arrays)) {
			// If a sub-array is empty, it doesn't affect the cartesian product
			if (empty($values)) {
				continue;
			}

			// Seeding the product array with the values from the first sub-array
			if (empty($result)) {
				foreach($values as $value) {
					$result[] = array($key => $value);
				}
			}
			else {
				// Second and subsequent input sub-arrays work like this:
				//   1. In each existing array inside $product, add an item with
				//      key == $key and value == first item in input sub-array
				//   2. Then, for each remaining item in current input sub-array,
				//      add a copy of each existing array inside $product with
				//      key == $key and value == first item of input sub-array

				// Store all items to be added to $product here; adding them
				// inside the foreach will result in an infinite loop
				$append = array();

				foreach($result as &$product) {
					// Do step 1 above. array_shift is not the most efficient, but
					// it allows us to iterate over the rest of the items with a
					// simple foreach, making the code short and easy to read.
					$product[$key] = array_shift($values);

					// $product is by reference (that's why the key we added above
					// will appear in the end result), so make a copy of it here
					$copy = $product;

					// Do step 2 above.
					foreach($values as $item) {
						$copy[$key] = $item;
						$append[] = $copy;
					}

					// Undo the side effecst of array_shift
					array_unshift($values, $product[$key]);
				}

				// Out of the foreach, we can add to $results now
				$result = array_merge($result, $append);
			}
		}
		return $result;
	}
	
	/**
	 * Fill single variation
	 */
	function fill_variation( $variation, $product_id ) {
		$html = '';
		$new = new WPSL_Product_Variations();
		$atts = $new->get_atts( $product_id );
		foreach ( (array)$variation as $key => $val ) {
			$html .= '<select name="' . $key . '">';
			$html .= '<option value="">' . __( 'Attribute', 'wpsl' ) . '</option>';
			foreach ( $atts[$key] as $k => $v ) {
				$html .= '<option value="' . $v . '"' . selected( $v, $val, false ) . '>' . $v . '</option>';
			}
			$html .= '</select>
		';
		}
		return $html;
	}
	
	/**
	 * Fill variations
	 */
	function fill_variations( $variation, $product_id ) {
		$html = '';
		foreach ( $variation as $key => $val ) {
			$atts = $this->get_atts( $product_id );
			$html .= '<select name="' . $key . '">';
			$html .= '<option value="">' . __( 'Attribute', 'wpsl' ) . '</option>';
			foreach ( $atts[$key] as $k => $v ) {
				$html .= '<option value="' . $v . '"' . selected( $v, $val, false ) . '>' . $v . '</option>';
			}
			$html .= '</select>';
		}
		return $html;
	}
	
	/**
	 * Fill row of table with variations
	 */
    function fill_row( $product_id = '', $variations = '' ) {
		$html = '';
		$url = isset( $url ) ? $url : 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';
		$img_id = isset( $img_id ) ? $img_id : '';

		$values = $this->get_variations( $product_id );
		if ( !empty( $variations ) ) {
			$values = $variations;
		}
		if ( $values ) {
			foreach ( $values as $index => $variation ) {
				$html .= '
				<tr class="column-row">
					<td class="column-id"></td>
					<td class="column-image"><span class="add-variation-img upload_image_variation dashicons dashicons-plus"><img src="' . $url . '" data-id="' . $img_id . '"><span class="remove-variation-img">&times;</span></span></td>
					<td class="column-variation">' . $this->fill_variations( $variation, $product_id ) . '</td>
					<td class="column-price"><input class="wpsl-validate-price" type="text" value="" name="" placeholder="' . __( 'Price', 'wpsl' ) . '" /></td>
					<td class="column-action" title="' . __( 'Action', 'wpsl' ) . '"><i class="wpsl-edit-variation dashicons dashicons-admin-settings" title="' . __( 'Edit', 'wpsl' ) . '"></i><i class="dashicons dashicons-trash" title="' . __( 'Delete', 'wpsl' ) . '"></i></td>
				</tr>';
			}
		}
		return $html;
	}
	
	/**
	 * Fill list of variations
	 */
	function filling( $variation, $product_id ) {
		$html = '';
		$arrays = $this->get_atts( $product_id );
		foreach ( (array)$variation as $key => $val ) {
			$html .= '<select name="' . $key . '">';
			$html .= '<option value="">' . __( 'Attribute', 'wpsl' ) . '</option>';
			if ( isset( $arrays[$key] ) && is_array( $arrays[$key] ) ) {
				foreach ( $arrays[$key] as $k => $v ) {
					$html .= '<option value="' . $v . '"' . selected( $v, $val, false ) . '>' . $v . '</option>';
				}
			}
			$html .= '</select>';
		}
		return $html;
	}
	
	/**
	 * Fill list of variations
	 */
    function fill_list( $product_id ) {
		$html = '';
		$variations = get_post_meta( $product_id, '_product_variations', true );
		if ( !empty( $variations ) ) {
			foreach ( $variations as $variation ) {
				$variation = json_decode( $variation );
				$url = !empty( $variation->item_image ) ? get_the_post_thumbnail_url( $variation->item_id, 'thumbnail' ) : 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';
				$html .= '
					<tr class="column-row">
						<td class="column-id">' . $variation->item_id . '</td>
						<td class="column-image"><span class="add-variation-img upload_image_variation dashicons dashicons-plus"><img src="' . $url . '" data-id="' . $variation->item_image . '"><span class="remove-variation-img">&times;</span></span></td>
						<td class="column-variation">' . $this->filling( $variation->item_variation, $product_id ) . '</td>
						<td class="column-price"><input class="wpsl-validate-price" type="text" value="' . $variation->item_price . '" name="" placeholder="' . __( 'Price', 'wpsl' ) . '" /></td>
						<td class="column-action" title="' . __( 'Action', 'wpsl' ) . '"><i class="wpsl-edit-variation dashicons dashicons-admin-settings" data-id="' . $variation->item_id . '" title="' . __( 'Edit', 'wpsl' ) . '"></i><i class="dashicons dashicons-trash" data-id="' . $variation->item_id . '" title="' . __( 'Delete', 'wpsl' ) . '"></i></td>
					</tr>';
			}
		} else {
			$html .= '
				<tr class="no-items">
					<td class="colspanchange" colspan="5">' . __( 'Variations of the product exist. Make sure that among the characteristics of eating is variable, and click "Create features".', 'wpsl' ) . '</td>
				</tr>
			';
		}
		return $html;
	}
	
	/**
	 * List of variations
	 */
    function get_list( $product_id ) {
		ob_start();
		echo $this->mass_management();
		?>
		<div class="wpsl-variations">
			<table class="wpsl-variations__table wp-list-table widefat fixed striped tags">
				<thead>
					<tr>
						<th scope="col" class="manage-column column-id"><?php _e( 'ID', 'wpsl' ); ?></th>
						<th scope="col" class="manage-column column-image"><i class="dashicons dashicons-format-image"></i></th>
						<th scope="col" class="manage-column column-variation"><?php _e( 'Variation', 'wpsl' ); ?></th>
						<th scope="col" class="manage-column column-price"><?php _e( 'Price', 'wpsl' ); ?></th>
						<th class="column-action" title="<?php _e( 'Action', 'wpsl' ); ?>"><?php _e( 'Action', 'wpsl' ); ?></th>
					</tr>
				</thead>
				<tbody id="wpsl-variations__list">
					<?php echo $this->fill_list( $product_id ); ?>
				</tbody>
			</table>
			<div class="wpsl-variations__save">
				<?php echo $this->save_btn(); ?>
			</div>
		</div>
		<?php
		echo $this->enqueue_script();
		$html = ob_get_clean();
		return $html;
    }
}