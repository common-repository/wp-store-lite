var $ = jQuery;
$(document).ready(function() {
	$(document)
		// price validation
		.on('change keyup input click oninput', '.wpsl-validate-price', function() {
			this.value = this.value.replace(/^\.|[^\d\.]|\.(?=.*\.)|^0+(?=\d\d)?$/g, '');
			if ( this.value.indexOf('.') != '-1' ) {
				// цифра 4, устанавливает количество цифр после запятой
				// т.е. если 4, то максимум 3 цифры после запятой
				this.value = this.value.substring( 0, this.value.indexOf('.') + 4 );													
			}
		})
		// сохранение цены в админке не заходя на страницу продукта
		.on('blur', '.this_price', function() {
			this_price = $(this);
			$.ajax({
				type:'POST',
				url:ajaxurl,
				data:'action=updatePrice&price_val=' + this_price.val() + '&product_id=' + this_price.attr('data-id'),
				beforeSend:function(xhr){
					this_price.attr('readonly','readonly').next().html('Сохраняю...');
				},
				success:function(results){
					this_price.removeAttr('readonly').next().html('<span style="color:#8BC34A">'+WPSA.saved+'</span>');
				}
			});
		})
		// сохранения вывода иконки "Best Sellers"
		.on('change', '.this_hit', function() {
			var _this = $(this),
				_val  = $(this).prop('checked') === true ? 'on' : '';
			$.ajax({
				url : ajaxurl,
				type: 'POST',
				data: 'action=updateHit&hit_val=' + _val + '&product_id=' + _this.attr('data-id'),
				beforeSend:function(xhr){
					_this.attr('readonly','readonly').next().html(WPSA.work);
				},
				success:function(results){
					_this.removeAttr('readonly').next().html(results);
				}
			});
		})
		// проверка рботоспособности СМС шлюза
		.on('click', '#wps-test', function() {
			var _this = $(this);
			_this.text(WPSA.sending);
			$.ajax({
				type : 'POST',
				url  : ajaxurl,
				data :'action=test_sms',
				success: function(msg) {
					$('#sms-verification-info').html(msg);
					_this.text(WPSA.send);
				}
			});
			return false;
		})
		// working with product type
		.on('change', '#type-product', function(){
			var val = $(this).val();
			$(this).parents('.wpsl-metabox').children('.wpsl-metabox__menu').find('li').each(function() {
				if ( $(this).data('rel') === '' || $(this).data('rel') === val ) {
					$(this).addClass('active');
				} else {
					$(this).removeClass('active');
				}
			});
			if ( val != 'simple' ) {
				$('.wpsl-metabox__menu').children('[data-rel="'+val+'"]').trigger('click');
			} else {
				$('.wpsl-metabox__menu li:first').trigger('click');
			}
			
			if ( val === 'external' ) {
				$('._product_url').fadeIn(250);
			} else {
				$('._product_url').fadeOut(250);
			}
		})
		// digital product
		.on('change', '#_digital', function(){
			if ( $(this).prop('checked') ) {
				$('._upload_file').fadeIn(250);
			} else {
				$('._upload_file').fadeOut(250);
			}
		})
		.on('click', '.wpsl-edit-variation', function(){
			$('#edit_variation').fadeIn(100);
		})
		.on('click', '.wpsl-hide-editor', function(){
			$('#edit_variation').fadeOut(100);
		})
		.on('change', '.wps-select-payment label input', function(){
			if ($(this).prop('checked')) {
				$(this).parent('label').addClass('active');
			} else {
				$(this).parent('label').removeClass('active');
			}
		});
	// payment widget
	$('.wps-select-payment label input').each(function(){
		if ($(this).prop('checked')) {
			$(this).parent('label').addClass('active');
		} else {
			$(this).parent('label').removeClass('active');
		}
	});
	// вкладки метабоксов
	jQuery('ul.wpsl-metabox__menu').on('click', 'li:not(.tab-active)', function() {
		jQuery(this)
			.addClass('tab-active').siblings().removeClass('tab-active')
			.closest('div.wpsl-metabox').find('div.wpsl-metabox__content').removeClass('tab-active').eq($(this).index()).addClass('tab-active');
		var ulIndex = $('ul.wpsl-metabox__menu').index($(this).parents('ul.wpsl-metabox__menu'));
	});
});

jQuery(document).ready(function($) {
	/**
	 * PRODUCT GALLERY
	 */
	// add new image
	jQuery('.wpsl-img-upload').click(function(e) {
		var _custom_media = true,
		_orig_attachment = wp.media.editor.send.attachment;
		var button = jQuery(this),
			field = button.siblings('input[type="hidden"]');
		wp.media.editor.send.attachment = function(props, attachment){
			if ( _custom_media ) {
				//console.log(attachment);
				if(field.val() == '') {
					field.val(attachment.id);
					button.parent('div').before('<img class="wpsl-img" width=54 height=54 src='+attachment.url+' data-id='+attachment.id+' class=attachment-54x54 />');
				} else {
					if ( field.val().indexOf(attachment.id) < 0 ) {
						field.val(field.val()+','+attachment.id);
						button.parent('div').before('<img class="wpsl-img" width=54 height=54 src='+attachment.url+' data-id='+attachment.id+' class=attachment-54x54 />');
						return false;
					} else {
						alert( WPSA.already );
					}
				}
			} else {
				return _orig_attachment.apply( this, [props, attachment] );
			};
		}
		wp.media.editor.open(button);
		return false;
	});
	// remove image from gallery
	jQuery('body').on('click', '.wpsl-img', function(){
		var _this = jQuery(this),
			input = _this.siblings('input');
		valArr = input.val().split(',');
		console.log( input );
		var index = valArr.indexOf(jQuery(this).attr('data-id'));
		if (index > -1) {
			valArr.splice(index, 1);
			input.val(valArr.toString());
			_this.remove();
		}
	});
	// sortable gallery
	if ( document.getElementById('gallery__product_image_gallery') instanceof Object ) {
		Sortable.create(
			$('#gallery__product_image_gallery')[0],
			{
				animation: 150,
				scroll: true,
				handle: '.wpsl-img',
				onEnd: function (e) {
					arr = [];
					$('#gallery__product_image_gallery').children('.wpsl-img').each(function (i,elem) {
						arr.push($(elem).data('id'));
					});
					$('#gallery__product_image_gallery').children('input[type="hidden"]').val(arr.join(','));
				}
			}
		);
	}
	/**
	 * JS скрипт для ajax поиска в админке
	 */
	function ajax_admin_search_update_search() {
		s = a.val().replace(' ', '+');
		var url = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
		for (var i = 0; i < url.length; i++) {
			if (/(^s$)|(\bs=.*\b)|(\bs=)/g.test(url[i]) === true || /http.*/g.test(url[i]) === true) y = i;
		}
		if (typeof y === 'undefined') url.unshift('s='+s);
		else url[y] = 's='+s;
		url = url.join('&');
		url = window.location.pathname+'?'+url;


		$.get(url, {}, function(data) {
			var r = $('<div />').html(data);
			var table = r.find(z);
			var tablenav_top = r.find(tnt);
			var tablenav_bottom = r.find(tnb);
			$(z).html(table);
			$(tnt).html(tablenav_top);
			$(tnb).html(tablenav_bottom);
		},'html');

		$(document).ajaxStop(function() {
			if(s.length) {
				history.pushState({}, "after search", url);
			} else {
				history.pushState({}, "empty search", url);
			}
		});

	}

	$(function() {
		a = $('#posts-filter input[type="search"]');
		t = a.closest('form').find('table');
		if(!t.length) t = a.closest('div').find('table');
		if(!t.length) return;
		z = '.'+t.attr('class').replace(/\s/g, '.');
		tn = '.top .displaying-num';
		bn = '.bottom .displaying-num';
		tpl = '.top span.pagination-links';
		bpl = '.bottom span.pagination-links';
		tnt = '.tablenav.top';
		tnb = '.tablenav.bottom';
		var timer;
		a.on('keyup', function(event) {
			if (timer) clearTimeout(timer);
			timer = setTimeout(ajax_admin_search_update_search, 300);
		});
	});
});


/**
 * JS скрипт для загрузки файлов
 */ 
jQuery(function($){
	/*
	 * действие при нажатии на кнопку загрузки изображения
	 */
	$('.upload_image_button').click(function(){
		var send_attachment_bkp = wp.media.editor.send.attachment;
		var button = $(this);
		wp.media.editor.send.attachment = function(props, attachment) {
			button.parents('tr').find('input[type="text"]').attr('value', attachment.url);
			wp.media.editor.send.attachment = send_attachment_bkp;
		}
		wp.media.editor.open(button);
		return false;    
	});
	/*
	 * удаляем значение произвольного поля
	 * если быть точным, то мы просто удаляем value у input type="hidden"
	 */
	$('.remove_image_button').click(function(){
		$(this).parents('tr').find('input[type="text"]').attr('value', '');
	});
});
/**
 * JS скрипт для импорта товаров из csv файла
 */
jQuery(document).ready(function($){
	var files;
	$('.wpsl-import input[type=file]').on('change', function(){
		files = this.files;
	});
	$('.wpsl-import__button').on( 'click', function( event ){
		event.stopPropagation(); // остановка всех текущих JS событий
		event.preventDefault();  // остановка дефолтного события для текущего элемента - клик для <a> тега
		// ничего не делаем если files пустой
		if( typeof files == 'undefined' ) {
			alert(WPSA.select_file);
			return false;
		};
		
		$(this).addClass('disabled').removeClass('button-primary');
		// создадим данные файлов в подходящем для отправки формате
		var data = new FormData();
		$.each( files, function( key, value ){
			data.append( key, value );
		});
		data.append( 'action', 'import_form' );
		$('.wpsl-import__result').text(WPSA.file_sended);
		$.ajax({
			url         : ajaxurl,
			type        : 'POST',
			data        : data,
			cache       : false,
			processData : false,
			contentType : false,
			success     : function( json ){
				if( json ){
					$('.wpsl-import__result').html(json);
					$('.wpsl-import__button').removeClass('disabled').addClass('button-primary');
				}
			},
			// функция ошибки ответа сервера
			error: function( jqXHR, status, errorThrown ){
				$('.wpsl-import__result').text(WPSA.ajax_error);
				$('.wpsl-import__button').removeClass('disabled').addClass('button-primary');
			}
		});
	});

	jQuery(document).on('submit', '.wpsl-import__form', function() {
		var _this = $(this),
			_btn  = $(this).find('button');
		$.ajax({
			url: ajaxurl,
			type: 'POST',
			cache: false,
			data: {
				action : 'import_products',
				str: $(this).serialize(),
			},
			beforeSend: function() {
				_btn.addClass('disabled').removeClass('button-primary');
			},
			success: function(data){
				$('.wpsl-import__result').html(data);
				_btn.removeClass('disabled').addClass('button-primary');
			}
		});
		return false;
	});
	$(document).on('change', '.wpsl-select-field', function() {
		$(this).siblings('input').remove();
		if ( $(this).val() == 'individual' || $(this).val() == 'global' ) {
			$(this).after('<input type="text" placeholder="'+WPSA.name+'" name="'+$(this).val()+'['+$('.wpsl-select-field').index($(this))+'][attribute_name]" value=""><input type="text" placeholder="'+WPSA.measure+'" name="'+$(this).val()+'['+$('.wpsl-select-field').index($(this))+'][attribute_measure]" value="">');
		}
	});
})



/*
*	YIKES Simple Taxonomy Ordering Scripts
*	@compiled by YIKES & Evan Herman
*	@since v0.1
*/
jQuery( document ).ready( function() {

	// if the tax table contains items
	if( ! jQuery( '#the-list' ).find( 'tr:first-child' ).hasClass( 'no-items' ) && jQuery( 'body' ).hasClass( 'taxonomy-product_cat' ) ) {
		
		jQuery( '.taxonomy-product_cat #the-list' ).sortable({
			placeholder: "wpsl-drag-drop-tax-placeholder",
			axis: "y",
			// on start set a height for the placeholder to prevent table jumps
			start: function(event, ui) {
				var height = jQuery( ui.item[0] ).css( 'height' );
				jQuery( '.wpsl-drag-drop-tax-placeholder' ).css( 'height', height );
			},
			// update callback
			update: function( event, ui ) {
				// hide checkbox, append a preloader
				jQuery( ui.item[0] ).find( 'input[type="checkbox"]' ).hide().after( '<img src="' + WPSA.preloader + '" class="wpsl-simple-taxonomy-preloader" />' );
				
				// empty array				
				var updated_array = [];
				
				// store the updated tax ID
				jQuery( '#the-list' ).find( 'tr.ui-sortable-handle' ).each( function() {
					var tax_id = jQuery( this ).attr( 'id' ).replace( 'tag-', '' );
					updated_array.push( [ tax_id, jQuery( this ).index() ] );
				});
				
				// build the ajax data
				var data = {
					'action': 'update_taxonomy_order',
					'updated_array': updated_array 
				};
				
				// Run the ajax request
				jQuery.post( ajaxurl, data, function( response ) {
					jQuery( '.wpsl-simple-taxonomy-preloader' ).remove();
					jQuery( ui.item[0] ).find( 'input[type="checkbox"]' ).show();
				});
			}
		});
	}

});



jQuery( document ).ready( function() {
	( function( $ ) {
		var media = wp.media;
		if ( media ) {
			var Library = wp.media.controller.Library;
			var oldMediaFrame = wp.media.view.MediaFrame.Post;

			// Extending the current media library frame to add a new tab
			wp.media.view.MediaFrame.Post = oldMediaFrame.extend({
				
				initialize: function() {
					// Calling the initalize method from the current frame before adding new functionality
					oldMediaFrame.prototype.initialize.apply( this, arguments );
					var options = this.options;  
					// Adding new tab
					this.states.add([
						new Library({
							id:         'inserts',
							title:      'ZIP files',
							priority:   100,
							toolbar:    'main-insert',
							filterable: 'all',
							library:    wp.media.query( options.library ),
							multiple:   false,
							editable:   false,
							library:  wp.media.query( _.defaults({
								// Adding a new query parameter
								zip: 'zip',

							}, options.library ) ), 
							 
							// Show the attachment display settings.
							displaySettings: true,
							// Update user settings when users adjust the
							// attachment display settings.
							displayUserSettings: true
						}), 
					]);
				}, 

			});	
		}
	}(jQuery));
});