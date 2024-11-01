<?php
if ( ! defined( 'ABSPATH' ) ) exit;


if ( !is_admin() ) return;


/**
 * Attributes constructor
 */
class WPSL_Product_Attributes_Single{
	
	/**
	 * Save button
	 */
	function save_btn() {
		global $post;
		$taxes = get_terms(
			array(
				'taxonomy'   => 'product_cat',
				'hide_empty' => false,
			)
		);
		$taxonomy = '<option value="0">— ' . __( 'Select attributes group', 'wpsl' ) . ' —</option>';
		//$taxonomy .= '<option value="all">' . __( 'From general settings', 'wpsl' ) . '</option>';
		if ( $taxes ) {
			foreach ( $taxes as $of_tax ) {
				$disabled = !empty( wpsl_get_atts_by_term_id( $of_tax->term_id ) ) ? '' : 'disabled';
				if ( $disabled != 'disabled' ) {
					$taxonomy .= '<option value="' . $of_tax->term_id . '" ' . $disabled . '>' . $of_tax->name . '</option>';
				}
			}
		}
		?>
		<input style="float:left;" type="button" class="button wpsl-save-product-atts" value="<?php _e( 'Save characteristics', 'wpsl' ); ?>" data-id="<?php echo $post->ID; ?>">
		<div style="float:right;" class="wpsl-create-atts-from">
			<input type="button" class="button wpsl-create-attr-button" value="<?php _e( 'Create from', 'wpsl' ); ?>">
			<select class="wpsl-create-attr-select">
				<?php echo $taxonomy; ?>
			</select>
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
			 * Edit attribute
			 */
 			$('body').on('click', '#singular-atts-list td', function() {
				var _this = $(this);
				if ( !_this.hasClass( 'edit' ) && !_this.hasClass( 'column-drag' ) && !_this.hasClass( 'column-variable' ) ) {
					if ( _this.hasClass('column-value') ) {
						list = '';
						arr = [];
						arr = _this.text().split('|');
						if ( arr[0] !== '' ) {
							$.each(arr, function(i,value){
								list += '<li class="attr"><span>'+value+'</span><i class="dashicons dashicons-no-alt"></i></li>';
							});
						}
						_this.html('<ul class="atts-input">'+list+'<li class="attr-new"><input placeholder="<?php _e( 'New comma separated', 'wpsl' ); ?>" class="edit-attribute" type="text" value="" /></li></ul>');
						val = '';
					} else if ( _this.hasClass('column-name') ) {
						val = _this.children('.row-name').text();
						_this.children('.row-name').html('<input class="edit-attribute" type="text" value="" />');
					} else {
						val = _this.text();
						_this.html('<input class="edit-attribute" type="text" value="" />');
					}
					_this.addClass('edit').find('input').val('').focus().val(val);
					$('.wpsl-save-product-atts').addClass('button-primary');
				}
			});
			$('body').on('keyup', '#singular-atts-list .attr-new input', function() {
				var tag = $(this).val().trim(),
					length = tag.length;
				if ((tag.charAt(length - 1) == ',') && (tag != ',')) {
					tag = tag.substring(0, length - 1);
					// check existing tag
					var existing = false,
						text = tag.toLowerCase();
					$('#singular-atts-list .attr').each(function() {
						if ($(this).text().toLowerCase() == text) {
							existing = true;
							return '';
						}
					});
					if (!existing) {
						$('<li class="attr"><span>' + $.trim(tag) + '</span><i class="dashicons dashicons-no-alt"></i></li>').insertBefore($('.attr-new'));
						$(this).val('');
					} else {
						$(this).val($.trim(tag));
					}
					$('.wpsl-save-product-atts').addClass('button-primary');
				}
			});
			$('body').on('mousedown', '#singular-atts-list .edit .attr i', function(e) {
				$(this).parent('li').remove();
				event.preventDefault();  
				event.stopPropagation();   
				return false;
			});
 			$('body').on('focusout', '#singular-atts-list .edit', function(e) {
				var _this = $(this);
				if ( _this.hasClass('column-value') ) {
					arr = [];
					_this.children('.atts-input').children('.attr').children('span').each(function (i, el){
						arr.push($(el).text());
					});
					_this.html(arr.join('|')).removeClass('edit');
				} else if( _this.hasClass('column-name' ) ) {
					_this.children('.row-name').html(_this.find('input').val());
					_this.removeClass('edit');
				} else {
					_this.html(_this.children('input').val()).removeClass('edit');
				}
				$('.wpsl-save-product-atts').addClass('button-primary');
			});
			/*
			 * Create group of attributes
			 */
			$('.wpsl-create-attr-button').click(function(){
				var term_id = $('.wpsl-create-attr-select').val();
				if ( term_id == '0' ) {
					alert( WPSA.load_atts );
				} else {
					var forma = $('.wpsl-save-product-atts').parents('.wpsl-attr-constructor');
					forma.append('<div class="wpsl-preloader"><img src="' + WPSA.preloader + '" /></div>');
					$.ajax({
						url  : ajaxurl,
						type : 'POST',
						cache: false,
						data: ({
							action  : 'create_product_atts',
							term_id : term_id
						}),
						success: function(result){
							$('.no-items').remove();
							$('#singular-atts-list').append(result);
							$('.wpsl-save-product-atts').addClass('button-primary');
							forma.children('.wpsl-preloader').remove();
						}
					});
				}
			});
			/*
			 * Add attribute
			 */
			$('.wpsl-add-attr').click(function() {
				var _elem = $(this).parents('tr');
				ele = [];
				arr = ['name','label','value','measure','variable'];
				$.each(arr, function(i,value){
					if ( value === 'variable' ) {
						if ($(_elem).children('.column-variable').children('input').is(':checked')) {
							ele['attribute_variable'] = '1';
						} else {
							ele['attribute_variable'] = '0';
						}
					} else {
						ele['attribute_'+value] = $(_elem).children('.column-'+value).children('input').val();
					}
				});
				// check input
				check = ['name','label','value'];
				$.each(check, function(index,val){
					if ( ele['attribute_'+val] === '' ) {
						val = $(_elem).children('.column-'+val).children('input');
						val.css({'border-color':'#ff0000'});
						setTimeout(function(){
							val.css({'border-color':'#dddddd'});
						},1000);
					}
				});
				if ( ele['attribute_name'] !== '' && ele['attribute_label'] !== '' && ele['attribute_value'] !== '' ){
					checked = ele['attribute_variable'] === '1' ? 'checked' : '';
					
					rand_id = '';
					var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
					for (var i = 0; i < 6; i++) {
						rand_id += possible.charAt(Math.floor(Math.random() * possible.length));
					}
					
					$('.wpsl-product-attr-constructor__table tbody').append('<tr class="column-row"><td class="column-drag"><i class="dashicons dashicons-editor-justify"></i></td><td class="column-name"><div class="row-name">'+ele['attribute_name']+'</div><div class="row-actions"><span class="delete-attr"><?php _e( 'Delete', 'wpsl' ); ?></span></div></td><td class="column-label">'+ele['attribute_label']+'</td><td class="column-value">'+ele['attribute_value']+'</td><td class="column-measure">'+ele['attribute_measure']+'</td><td class="column-variable" style="padding: 9px 2px;"><input value="" id="attribute_variable-'+rand_id+'" name="attribute_variable" class="attribute_variable wpsl-switch" type="checkbox" '+checked+' /><label class="switch-label" for="attribute_variable-'+rand_id+'" style="margin-left: 2px;"></label></td></tr>');
					$('.wpsl-save-product-atts').addClass('button-primary');
					$.each(arr, function(i,value){
						if ( value === 'variable' ) {
							$(_elem).children('.column-'+value).children('input').removeAttr('checked');
						} else {
							$(_elem).children('.column-'+value).children('input').val('');
						}
					});
					$('.no-items').remove();
				}
			});
			/*
			 * Edit variable checkbox
			 */
			$('body').on('click', '.attribute_variable', function() {
				$('.wpsl-save-product-atts').addClass('button-primary');
			});
			/*
			 * Delete attribute
			 */
			$('body').on('click', '.wpsl-product-attr-constructor__table .delete-attr', function() {
				var _this = $(this),
					parent = _this.parents('tr');
				parent.css({'background-color':'#ffe2e2'});
				$('.wpsl-save-product-atts').addClass('button-primary');
				setTimeout(
					function(){
						parent.remove();
					}, 200
				);
			});
			/*
			 * Save attribute
			 */
			function save_atts() {
				var atts = [];
				arr = ['name','label','value','measure','variable'];
				// each
				$('.wpsl-product-attr-constructor__table tbody tr').each(function(i,elem){
					attr = {};
					$.each(arr, function(i,value){
						if ( value === 'name' ) {
							attr['attribute_'+value] = $(elem).children('.column-'+value).children('.row-name').text();
						} else if ( value === 'variable' ) {
							if ($(elem).children('.column-'+value).children('input').is(':checked')) {
								attr['attribute_'+value] = '1';
							} else {
								attr['attribute_'+value] = '0';
							}
						} else {
							attr['attribute_'+value] = $(elem).children('.column-'+value).text();
						}
						//console.log( attr );
					});
					atts.push(JSON.stringify(attr));
				});
				var attributes = $.extend({}, atts);
				// ajax
				var forma = $('.wpsl-save-product-atts').parents('.wpsl-attr-constructor');
				forma.append('<div class="wpsl-preloader"><img src="' + WPSA.preloader + '" /></div>');
				$.ajax({
					url  : ajaxurl,
					type : 'POST',
					cache: false,
					data: ({
						action : 'save_single_product_atts',
						atts : attributes,
						post_id: $('.wpsl-save-product-atts').data('id'),
					}),
					success: function(result){
						$('.wpsl-save-product-atts').removeClass('button-primary');
						forma.children('.wpsl-preloader').remove();
					}
				});
			}
			$('.wpsl-save-product-atts').click(function(){
				if ( $('.wpsl-save-product-atts').hasClass('button-primary') ) {
					save_atts();
				}
			});
			/*
			 * Sortable attributes
			 */
			if ( document.getElementById('singular-atts-list') instanceof Object ) {
				Sortable.create(
					$('#singular-atts-list')[0],
					{
						animation: 150,
						scroll: true,
						handle: '.column-drag',
						onEnd: function (evt) {
							save_atts();
							console.log(evt);
						}
					}
				);
			}
			/*
			 * Translit name attribute
			 */
			function translit(){
				var space = '-';
				// Берем значение из нужного поля и переводим в нижний регистр
				var text = $('.column-name input[type="text"]').val().toLowerCase();
				var transl = {
					'а': 'a', 'б': 'b', 'в': 'v', 'г': 'g', 'д': 'd', 'е': 'e', 'ё': 'e', 'ж': 'zh', 
					'з': 'z', 'и': 'i', 'й': 'j', 'к': 'k', 'л': 'l', 'м': 'm', 'н': 'n',
					'о': 'o', 'п': 'p', 'р': 'r','с': 's', 'т': 't', 'у': 'u', 'ф': 'f', 'х': 'h',
					'ц': 'c', 'ч': 'ch', 'ш': 'sh', 'щ': 'sh','ъ': space, 'ы': 'y', 'ь': '', 'э': 'e', 'ю': 'yu', 'я': 'ya',
					' ': space, '_': space, '`': space, '~': space, '!': space, '@': space,
					'#': space, '$': space, '%': space, '^': space, '&': space, '*': space, 
					'(': space, ')': space,'-': space, '\=': space, '+': space, '[': space, 
					']': space, '\\': space, '|': space, '/': space,'.': space, ',': space,
					'{': space, '}': space, '\'': space, '"': space, ';': space, ':': space,
					'?': space, '<': space, '>': space, '№':space
				}
				var result = '';
				var curent_sim = '';
				for(i=0; i < text.length; i++) {
					if(transl[text[i]] != undefined) {
						if(curent_sim != transl[text[i]] || curent_sim != space){
							result += transl[text[i]];
							curent_sim = transl[text[i]];
						}
					}
					else {
						result += text[i];
						curent_sim = text[i];
					}
				}
				result = result.replace(/^-/, '');
				result = result.replace(/-$/, '');
				// Выводим результат
				return result;
			}
			$(function(){
				$('.column-name input[type="text"]').keyup(function(){
					trans = translit();
					$('.column-label input[type="text"]').val(trans);
					return false;
				});
			});
		});
		</script>
		<style>
		.wpsl-product-attr-constructor__table tfoot th{
			padding: 4px 2px !important;
		}
		.wpsl-product-attr-constructor__table tfoot th input{
			width: 100%;
			padding: 4px;
			font-size: 13px;
			margin: 0;
			line-height: 18px;
			color: #808080;
			border: 1px solid #ccc;
			-webkit-border-radius: 3px;
			-moz-border-radius: 3px;
			border-radius: 3px;
			box-shadow: inset 0 1px 2px rgba(0,0,0,.07);
			background-color: #fff;
			color: #32373c;
			outline: 0;
		}
		.wpsl-add-attr{
			cursor: pointer;
			padding: 4px 1px;
			height: 24px;
			color: #0085ba;
			box-sizing: border-box;
		}
		.wpsl-product-attr-constructor__table .delete-attr{
			padding-left: 0 !important;
		}
		.edit-attribute{
			width: 100%;
			padding: 3px;
			font-size: 13px;
			margin: 0;
			line-height: 1;
			color: #808080;
			border: 1px solid #ccc;
			-webkit-border-radius: 3px;
			-moz-border-radius: 3px;
			border-radius: 3px;
			box-shadow: inset 0 1px 2px rgba(0,0,0,.07);
			background-color: #fff;
			color: #32373c;
			outline: 0;
			text-align: left;
		}
		/* TAGS IN INPUT */
		.attributes-list .atts-input {
			list-style: none;
			display: table;
			width: 100%;
			overflow: hidden;
			padding: 0;
			float: left;
			margin: 0;
		}
		.attributes-list li{
			margin: 0;
			float: left;
		}
		.attributes-list .attr{
			background: #f1f1f1;
			float: left;
			padding: 0 19px 3px 2px;
			border-radius: 2px;
			margin: 0 3px 3px 0 !important;
			position: relative;
		}
		.attributes-list .attr i{
			position: absolute;
			right: 0;
			padding: 6px 3px;
			top: 0;
			width: 10px;
			height: 10px;
			content: '';
			cursor: pointer;
			opacity: .7;
			font-size: 12px;
		}
		.attributes-list .attr i:hover{
			opacity: 1;
			background-color:#eeeeee;
		}
		.attributes-list .attr-new input[type="text"]{
			display: inline-block;
			width: 100%;
			padding: 3px;
			font-size: 13px;
			margin: 0 !important;
			line-height: 1;
			color: #808080;
			border: 1px solid #ccc;
			-webkit-border-radius: 3px;
			-moz-border-radius: 3px;
			border-radius: 3px;
			box-shadow: inset 0 1px 2px rgba(0,0,0,.07);
			background-color: #fff;
			color: #32373c;
		}
		.attributes-list .attr-new input[type="text"]:focus{
			outline:none; 
		}
		</style>
		<?php
	}
	
	/**
	 * Fill row of table with attributes
	 */
    function fill_row( $attr ) {
		$attr = (object)$attr;
		$unique = mb_substr( str_shuffle( str_repeat( 'qwertyuiopasdfghjklzxcvbnm', (int)( 6 / mb_strlen( 'qwertyuiopasdfghjklzxcvbnm' ) ) + 1 ) ), 0, 6 );
		$value = isset( $attr->attribute_value ) ? $attr->attribute_value : '';
		$html = '
		<tr class="column-row">
			<td class="column-drag"><i class="dashicons dashicons-editor-justify"></i></td>
			<td class="column-name">
				<div class="row-name">' . $attr->attribute_name . '</div>
				<div class="row-actions">
					<span class="delete-attr">' . __( 'Delete', 'wpsl' ) . '</span>
				</div>
			</td>
			<td class="column-label">' . $attr->attribute_label . '</td>
			<td class="column-value">' . $value . '</td>
			<td class="column-measure">' . $attr->attribute_measure . '</td>
			<td class="column-variable" style="padding: 9px 2px;">
				<input value="" id="attribute_variable-' . $unique . '" name="attribute_variable" class="attribute_variable wpsl-switch" type="checkbox" ' . checked( $attr->attribute_variable, 1, false ) . ' />
				<label class="switch-label" for="attribute_variable-' . $unique . '" style="margin-left: 2px;"></label>
			</td>
		</tr>';
		return $html;
	}
	
	/**
	 * Fill list of attributes
	 */
    function fill_list( $post_id, $term_id = '' ) {
		$html = '';
		$atts = get_post_meta( $post_id, '_atts', true );
		if ( !empty( $atts ) && is_array( $atts ) ) {
			foreach ( $atts as $attr ) {
				$attr = json_decode( $attr );
				$html .= $this->fill_row( $attr );
			}
		} else {
			$html .= '
				<tr class="no-items">
					<td class="colspanchange" colspan="6">' . __( 'Attributes not found', 'wpsl' ) . '</td>
				</tr>
			';
		}
		return $html;
	}
	
	/**
	 * List of attributes
	 */
    function get_list( $post_id ) {
		ob_start();
		?>
		<div class="wpsl-attr-constructor">
			<table class="wpsl-product-attr-constructor__table wp-list-table widefat fixed striped tags">
				<thead>
					<tr>
						<td id="cb" class="manage-column column-drag"></td>
						<th scope="col" class="manage-column column-name"><?php _e( 'Title', 'wpsl' ); ?></th>
						<th scope="col" class="manage-column column-label"><?php _e( 'Label', 'wpsl' ); ?></th>
						<th scope="col" class="manage-column column-value"><?php _e( 'Value', 'wpsl' ); ?></th>
						<th scope="col" class="manage-column column-measure"><?php _e( 'Measure', 'wpsl' ); ?></th>
						<th scope="col" class="manage-column column-variable" style="width: 44px;" title="<?php _e( 'Variable', 'wpsl' ); ?>"><i class="dashicons dashicons-screenoptions"></i></th>
					</tr>
				</thead>
				<tbody id="singular-atts-list" class="attributes-list">
					<?php echo $this->fill_list( $post_id ); ?>
				</tbody>
				<tfoot>
					<tr>
						<th scope="col" class="manage-column column-add" title="<?php _e( 'Add attribute', 'wpsl' ); ?>"><i class="dashicons dashicons-plus wpsl-add-attr"></i></th>
						<th scope="col" class="manage-column column-name"><input type="text" placeholder="<?php _e( 'Title', 'wpsl' ); ?>" /></th>
						<th scope="col" class="manage-column column-label"><input type="text" placeholder="<?php _e( 'Label', 'wpsl' ); ?>" /></th>
						<th scope="col" class="manage-column column-value"><input type="text" placeholder="<?php _e( 'Value', 'wpsl' ); ?>" /></th>
						<th scope="col" class="manage-column column-measure"><input type="text" placeholder="<?php _e( 'Measure', 'wpsl' ); ?>" /></th>
						<th scope="col" class="manage-column column-variable" title="<?php _e( 'Variable', 'wpsl' ); ?>">
							<input value="" id="attribute_variable" name="attribute_variable" class="attribute wpsl-switch" type="checkbox" />
							<label class="switch-label" for="attribute_variable" style="margin-left: 2px;"></label>
						</th>
					</tr>
				</tfoot>
			</table>
			<div class="wpsl-create-atts-from-box">
				<?php echo $this->save_btn(); ?>
			</div>
		</div>
		<?php
		echo $this->enqueue_script();
		$html = ob_get_clean();
		return $html;
    }
}