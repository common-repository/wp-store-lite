<?php
if ( ! defined( 'ABSPATH' ) ) exit;


if ( !is_admin() ) return;


/**
 * Attributes constructor
 */
class WPSL_Product_Attributes_Common {

	/**
	 * Get list of all product cartegorys
	 */
	function get_product_cat() {
		if ( get_current_screen()->parent_file == 'edit.php?post_type=product' ) {
			$taxonomy = '';
			$product_cats = get_terms(
				array(
					'taxonomy'   => 'product_cat',
					'hide_empty' => false,
				)
			);
			$list = '<option value="0">' . __('Select characteristic"s group', 'wpsl') . '</option>';
			$list .= '<option value="all">' . __('From general settings', 'wpsl') . '</option>';
			if ( isset( $product_cats ) ) {
				foreach ( $product_cats as $product_cat ) {
					$taxonomy .= '<option value="' . $product_cat->term_id . '">' . $product_cat->name . '</option>';
				}
			}
			?>
			<input type="button" class="button wpsl-create-atts-from" value="<?php _e( 'Create from', 'wpsl' ); ?>">
			<select class="wpsl-create-atts-select">
				<?php echo $taxonomy; ?>
			</select>
			<?php
		}
	}

	/**
	 * Fill attribute constructor
	 */
	function fill_constructor( $term_id, $attr_id = '', $product_id = '' ) {
		if ( isset( $attr_id ) ) {
			$attr = wpsl_get_attr_by_id( $attr_id );
		}
		?>
		<div class="wpsl-fill-constructor__head">
			<h2><?php _e( 'Attribute', 'wpsl' ); ?></h2>
			<span class="page-title-action add-attribute"><?php _e( 'Add new', 'wpsl' ); ?></span>
			<span class="open-form dashicons dashicons-no-alt"></span>
		</div>
		<table class="wp-list-table widefat fixed striped tags">
			<thead>
				<tr>
					<th scope="col" id="name" class="manage-column column-name"><?php _e( 'Name', 'wpsl' ); ?></th>
					<th scope="col" id="value" class="manage-column column-val"><?php _e( 'Value', 'wpsl' ); ?></th>
				</tr>
			</thead>
			<tbody id="the-atts-list">
				<tr>
					<td scope="col" class="manage-column column-name"><?php _e( 'Title', 'wpsl' ); ?></td>
					<td scope="col" class="manage-column column-val"><input value="<?php echo isset( $attr->attribute_name ) ? $attr->attribute_name : ''; ?>" type="text" class="attribute attribute_name" name="attribute_name" placeholder="<?php _e( 'Title', 'wpsl' ); ?>" /></td>
				</tr>
				<tr>
					<td scope="col" class="manage-column column-name"><?php _e( 'Label', 'wpsl' ); ?></td>
					<td scope="col" class="manage-column column-val"><input value="<?php echo isset( $attr->attribute_label ) ? $attr->attribute_label : ''; ?>" type="text" class="attribute attribute_label" name="attribute_label" placeholder="<?php _e( 'Label', 'wpsl' ); ?>" /></td>
				</tr>
				<tr>
					<td scope="col" class="manage-column column-name"><?php _e( 'Values', 'wpsl' ); ?></td>
					<td scope="col" class="manage-column column-val"><input value="<?php echo isset( $attr->attribute_value ) ? $attr->attribute_value : ''; ?>" type="text" class="attribute attribute_value" name="attribute_value" placeholder="<?php _e( 'Default values via |', 'wpsl' ); ?>" />
					</td>
				</tr>
				<tr>
					<td scope="col" class="manage-column column-name"><?php _e( 'Measure', 'wpsl' ); ?></td>
					<td scope="col" class="manage-column column-val"><input value="<?php echo isset( $attr->attribute_measure ) ? $attr->attribute_measure : ''; ?>" type="text" class="attribute attribute_measure" name="attribute_measure" placeholder="<?php _e( 'Measure', 'wpsl' ); ?>" /></td>
				</tr>
				<tr>
					<td scope="col" class="manage-column column-name"><?php _e( 'Description', 'wpsl' ); ?></td>
					<td scope="col" class="manage-column column-val">
						<textarea class="attribute attribute_desc" name="attribute_desc" placeholder="<?php _e( 'Description', 'wpsl' ); ?>" ><?php echo isset( $attr->attribute_desc ) ? $attr->attribute_desc : ''; ?></textarea>
					</td>
				</tr>
				<tr>
					<td scope="col" class="manage-column column-name"><?php _e( 'Type', 'wpsl' ); ?></td>
					<td scope="col" class="manage-column column-val">
						<select class="attribute attribute_type" name="attribute_type">
							<?php
							$list = array(
								'checkbox' => __( 'Select buttons', 'wpsl' ),
								'select'   => __( 'Dropdown list', 'wpsl' ),
								'color'    => __( 'Select color', 'wpsl' ),
								'image'    => __( 'Select image', 'wpsl' ),
								'slider'   => __( 'Slider', 'wpsl' ),
							);
							$i = 0;
							foreach ( $list as $key => $val ) {
								$selected = isset( $attr->attribute_type ) ? selected( $attr->attribute_type, $key ) : '';
								if ( !isset( $attr->attribute_type ) && $i == 0 ) {
									$selected = 'selected="selected"';
								}
								echo '<option value="' . $key . '" ' . $selected . '>' . $val . '</option>';
								$i++;
							}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td scope="col" class="manage-column column-name"><?php _e( 'Filterable', 'wpsl' ); ?></td>
					<td scope="col" class="manage-column column-val field">
						<input value="1" id="attribute_filterable" <?php echo isset( $attr->attribute_filterable ) ? checked( $attr->attribute_filterable, true ) : 'checked'; ?> type="checkbox" class="attribute attribute_filterable wpsl-switch" name="attribute_filterable" placeholder="<?php _e( 'Filterable', 'wpsl' ); ?>" />
						<label class="switch-label" for="attribute_filterable"></label>
					</td>
				</tr>
				<tr>
					<td scope="col" class="manage-column column-name"><?php _e( 'Variability', 'wpsl' ); ?></td>
					<td scope="col" class="manage-column column-val field">
						<input value="" id="attribute_variable" <?php echo isset( $attr->attribute_variable ) ? checked( $attr->attribute_variable, true ) : ''; ?> type="checkbox" class="attribute attribute_variable wpsl-switch" name="attribute_variable" placeholder="<?php _e( 'Variability', 'wpsl' ); ?>" />
						<label class="switch-label" for="attribute_variable"></label>
					</td>
				</tr>
			</tbody>
		</table>
		<input type="button" class="button wpsl-save-attr" value="<?php echo $attr_id != '' ? __( 'Save attribute', 'wpsl' ) : __( 'Add new attribute', 'wpsl' ); ?>" data-id="<?php echo $attr_id; ?>" data-term_id="<?php echo $term_id; ?>" data-product_id="<?php echo $product_id; ?>" />
		<?php
	}



	/**
	 * wpStore
	 *
	 * Show attributes constructor
	 *
	 * @since	2.7
	 */
	function constructor( $term_id, $product_id = '' ){

		// first collect characteristics
		$atts = wpsl_sort_atts_by( wpsl_get_atts( array( 'attribute_term_id' => $term_id ) ), 'attribute_position' );
		$html = $attr_id = '';
		if ( count( $atts ) > 0 ) {
			foreach ( $atts as $attr ) {
				$filterable = ( $attr->attribute_filterable == 1 ) ? __( 'Yes', 'wpsl' ) : __( 'No', 'wpsl' );
				$variable   = ( $attr->attribute_variable == 1 ) ? __( 'Yes', 'wpsl' ) : __( 'No', 'wpsl' );
				$html .= '
				<tr class="column-row" data-id="' . $attr->attribute_id . '">
					<td class="column-drag"><i class="dashicons dashicons-editor-justify"></i></td>
					<td class="column-name">
						<div class="row-name">' . $attr->attribute_name . '</div>
						<div class="row-actions">
							<span class="edit-attr" data-id="' . $attr->attribute_id . '" data-term_id="' . $term_id . '">' . __( 'Edit', 'wpsl' ) . ' | </span>
							<span class="delete-attr" data-id="' . $attr->attribute_id . '">' . __( 'Delete', 'wpsl' ) . '</span>
						</div>
					</td>
					<td class="column-label">' . $attr->attribute_label . '</td>
					<td class="column-measure">' . $attr->attribute_measure . '</td>
					<td class="column-desc">' . $attr->attribute_desc . '</td>
					<td class="column-filterable">' . $filterable . '</td>
					<td class="column-variable">' . $variable . '</td>
				</tr>';
			}
		} else {
			$html .= '
				<tr class="no-items">
					<td class="colspanchange" colspan="7">' . __( 'Attributes not found', 'wpsl' ) . '</td>
				</tr>
			';
		}
		?>
		<div class="wpsl-attr-constructor">
			<h2><?php _e( 'Create characteristics of product', 'wpsl' ); ?></h2>
			<span class="page-title-action open-form add-attribute"><?php _e( 'Add new', 'wpsl' ); ?></span>
			<table class="wpsl-attr-constructor__table wp-list-table widefat fixed striped tags">
				<thead>
					<tr>
						<td id="cb" class="manage-column column-drag"></td>
						<th scope="col" class="manage-column column-name"><?php _e( 'Title', 'wpsl' ); ?></th>
						<th scope="col" class="manage-column column-label"><?php _e( 'Label', 'wpsl' ); ?></th>
						<th scope="col" class="manage-column column-measure"><?php _e( 'Measure', 'wpsl' ); ?></th>
						<th scope="col" class="manage-column column-desc"><?php _e( 'Description', 'wpsl' ); ?></th>
						<th scope="col" class="manage-column column-filterable" title="<?php _e( 'Filterable', 'wpsl' ); ?>"><i class="dashicons dashicons-filter"></i></th>
						<th scope="col" class="manage-column column-variable" title="<?php _e( 'Variable', 'wpsl' ); ?>"><i class="dashicons dashicons-screenoptions"></i></th>
					</tr>
				</thead>
				<tbody id="atts-list" class="attributes-list">
					<?php echo $html; ?>
				</tbody>
				<tfoot>
					<tr>
						<td id="cb" class="manage-column column-drag"></td>
						<th scope="col" class="manage-column column-name"><?php _e( 'Title', 'wpsl' ); ?></th>
						<th scope="col" class="manage-column column-label"><?php _e( 'Label', 'wpsl' ); ?></th>
						<th scope="col" class="manage-column column-measure"><?php _e( 'Measure', 'wpsl' ); ?></th>
						<th scope="col" class="manage-column column-desc"><?php _e( 'Description', 'wpsl' ); ?></th>
						<th scope="col" class="manage-column column-filterable" title="<?php _e( 'Filterable', 'wpsl' ); ?>"><i class="dashicons dashicons-filter"></i></th>
						<th scope="col" class="manage-column column-variable" title="<?php _e( 'Variable', 'wpsl' ); ?>"><i class="dashicons dashicons-screenoptions"></i></th>
					</tr>
				</tfoot>
			</table>
			<div class="wpsl-create-atts-from-box">
				<?php echo $this->get_product_cat(); ?>
			</div>
			<div class="wpsl-fill-constructor wrap">
				<?php echo $this->fill_constructor( $term_id, $attr_id, $product_id ); ?>
			</div>
		</div>
		<script>
		/**
		 * JS конструктора произвольных полей
		 */
		jQuery(document).ready(function() {
			var $ = jQuery;
			$('body').on('click', '.open-form', function() {
				$('.wpsl-fill-constructor').toggleClass('active');
			});
			$('.attribute').each(function () {
				$('body').on('input change', '.attribute', function() {
					$('.wpsl-save-attr').addClass('button-primary');
				});
			});
			/*
			 * Save attribute
			 */
			$('body').on('click', '.wpsl-save-attr', function() {
				var atts = {};
				_this = $(this);
				$('.wpsl-fill-constructor tbody input').each(function(i,elem) {
					atts[elem.attributes.name.value] = elem.value;
				});
				atts['attribute_id'] = $(this).attr('data-id');
				atts['attribute_term_id'] = $(this).attr('data-term_id');
				atts['attribute_type'] = $('.wpsl-fill-constructor tbody .attribute_type').val();
				atts['attribute_desc'] = $('.wpsl-fill-constructor tbody .attribute_desc').val();
				if ($('.wpsl-fill-constructor tbody .attribute_filterable').is(':checked')) {
					atts['attribute_filterable'] = '1';
				} else {
					atts['attribute_filterable'] = '0';
				}
				if ($('.wpsl-fill-constructor tbody .attribute_variable').is(':checked')) {
					atts['attribute_variable'] = '1';
				} else {
					atts['attribute_variable'] = '0';
				}
				//console.log( atts['attribute_id'] );
				if ( atts['attribute_id'] === '' ) {
					atts['attribute_position'] = $('#atts-list').children('.column-row').length + 1;
				} else {
					console.log( $('#atts-list').children('[data-id="'+atts['attribute_id']+'"]') );
					atts['attribute_position'] = $('#atts-list').children('[data-id="'+atts['attribute_id']+'"]').index();
				}
				//console.log( atts );
				var json = JSON.stringify(atts);
				if ( typeof(atts['attribute_name']) !== 'undefined' && atts['attribute_name'] !== '' ) {
					$('.wpsl-fill-constructor').append('<div class="wpsl-preloader"><img src="' + WPSA.preloader + '" /></div>');
					$.ajax({
						url  : ajaxurl,
						type : 'POST',
						cache: false,
						data: {
							action : 'save_attribute',
							json : json,
							term_id: _this.data('term_id'),
						},
						success: function(result){
							result = JSON.parse(result);
							form = $('.wpsl-attr-constructor__table tbody');
							if ( result.status === 'update' ) {
								var item = form.children('[data-id="'+result.attribute_id+'"]');
								item.replaceWith(result.html);
								$('.wpsl-fill-constructor tbody .attribute_label').val(result.attribute_label);
							} else {
								form.append(result.html);
								_this.attr('data-id',result.attribute_id);
								$('.wpsl-fill-constructor tbody .attribute_label').val(result.attribute_label);
							}
							$('#atts-list').children('.no-items').remove();
							$('.wpsl-fill-constructor').children('.wpsl-preloader').remove();
							_this.val(WPSA.save_attr);
							_this.removeClass('button-primary');
						}
					});
				} else {
					input = $('.wpsl-fill-constructor tbody .attribute_name');
					input.css({'border-color': '#ff0000'});
					setTimeout(
						function(){
							input.css({'border-color': '#cccccc'});
						}, 2000
					);
				}
			});
			/*
			 * Add attribute
			 */
			$('body').on('click', '.add-attribute', function() {
				$('.wpsl-fill-constructor tbody input[type="text"]').each(function(i,elem) {
					elem.value = '';
				});
				$('.wpsl-fill-constructor tbody .attribute_value').val('');
				$('.wpsl-fill-constructor tbody .attribute_type').val('checkbox');
				$('.wpsl-fill-constructor tbody .attribute_desc').val('');
				$('.wpsl-fill-constructor tbody #attribute_filterable').prop('checked', true);
				$('.wpsl-fill-constructor tbody #attribute_variable').prop('checked', false);
				$('.wpsl-fill-constructor input[type="button"]').attr('data-id', '');
				$('.wpsl-save-attr').removeClass('button-primary');
				$('.wpsl-save-attr').val(WPSA.add_attr);
			});
			/*
			 * Delete attribute
			 */
			$('body').on('click', '.delete-attr', function() {
				var _this = $(this),
					parent = _this.parents('tr'),
					attr_id = _this.data('id');
				parent.css({'background-color':'#ffe2e2'});
				$.ajax({
					url  : ajaxurl,
					type : 'POST',
					cache: false,
					data: {
						action : 'remove_attribute',
						attr_id: attr_id,
					},
					success: function(result){
						setTimeout(
							function(){
								parent.remove();
							}, 200
						);
					}
				});
			});
			/*
			 * Edit attribute
			 */
			$('body').on('click', '.edit-attr', function() {
				var _this = $(this),
					attr_id = _this.data('id');
					term_id = _this.data('term_id');
				$('.wpsl-fill-constructor').toggleClass('active');
				$('.wpsl-fill-constructor').append('<div class="wpsl-preloader"><img src="' + WPSA.preloader + '" /></div>');
				$.ajax({
					url  : ajaxurl,
					type : 'POST',
					cache: false,
					data: {
						action : 'edit_attribute',
						attr_id: attr_id,
						term_id: term_id,
					},
					success: function(result){
						$('.wpsl-fill-constructor').html(result);
					}
				});
			});
			/*
			 * Sortable attributes
			 */
			if ( document.getElementById('atts-list') instanceof Object ) {
				Sortable.create(
					$('#atts-list')[0],
					{
						animation: 150,
						scroll: true,
						handle: '.column-drag',
						onEnd: function (e) {
							arr = [];
							$('#atts-list').children('.column-row').each(function (i,elem) {
								arr.push($(elem).data('id'));
							});
							$('.wpsl-attr-constructor').append('<div class="wpsl-preloader"><img src="' + WPSA.preloader + '" /></div>');
							$.ajax({
								url  : ajaxurl,
								type : 'POST',
								cache: false,
								data: {
									action  : 'sort_attribute',
									sortable: JSON.stringify(arr),
								},
								success: function(result){
									$('.wpsl-attr-constructor').children('.wpsl-preloader').remove();
									//$('.wpsl-create-atts-from-box').html(result);
								}
							});
						}
					}
				);
			}
		});
		</script>
		<?php
	}
	
}