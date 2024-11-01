var $ = jQuery;
$(document).ready(function() {
	/**
	 * Products sorting and filter
	 */
	function selectProducts( action, sortObj ) {
		var params = { action: action };
		if ( sortObj ) {
			params = $.extend({}, params, sortObj);
		}
		if ( $('form').is('.wpsl-filter') ) {
			var filterData = $('.wpsl-filter').serializeArray();
			var returnObj = {};
			for (var i = 0; i < filterData.length; i++){
				returnObj[filterData[i]['name']] = filterData[i]['value'];
			}
			params = $.extend({}, params, returnObj);
		}
		$.ajax({
			url : WPSL.ajaxurl,
			type: 'get',
			data: params,
			beforeSend: function() {
				if ( action === 'get_products' ) {
					$('#wpsl-loop').append('<div class="wpsl-loader"></div>');
				}
				if ( action === 'filter_product_count' ) {
					$('.wpsl-filter').append('<div class="wpsl-loader"></div>');
				}
			},
			success: function(result){
				if ( action === 'get_products' ) {
					$('#wpsl-loop').empty().html(result);
					$('html, body').animate({ scrollTop: $('#wpsl-loop').offset().top - 100 }, 500);
				}
				if ( action === 'filter_product_count' ) {
					$('.wpsl-filter__result').fadeIn(250).css({ 'margin-top' : $(window).scrollTop() }).html(result);
				}
			},
			complete: function(result) {
				$('.wpsl-loader').remove();
				var new_url = new URL(window.location);
				new_url.search = new URLSearchParams(params);
				history.pushState('', '', new_url);
			}
		});
		return false;
	}
	$(document)
		.on('click', '.wpsl-sort__ajax', function() {
			var sortObj = {};
			if ( $('div').is('.wpsl-sort') ) {
				// go ajax
				sortObj = { order: $(this).data('order'), orderby: $(this).data('orderby'), term_id: $(this).data('term') };
				$(this).siblings('span')
					.data('order', $(this).data('default'))
					.find('i').remove();
					
				$(this).find('i').remove();
				if ( $(this).data('order') == 'DESC' )
					$(this).append('<i class="icon-bar-chart2"></i>').data('order','ASC');
				else
					$(this).append('<i class="icon-bar-chart1"></i>').data('order','DESC');
			}
			selectProducts( 'get_products', sortObj );
		})
		.on('click', '.wpsl-filter__item_title', function() {
			$(this)
				.siblings('.wpsl-filter__item_values')
				.slideToggle(250);
			$(this).children('i')
				.toggleClass('icon-chevron-down icon-chevron-up');
		})
		.on('click', '.wpsl-filter__item_values .all', function() {
			$(this)
				.parents('.wpsl-filter__item_values')
				.find('input[type="checkbox"]')
				.prop('checked',true);
		})
		.on('click', '.wpsl-filter__item_values .reset', function() {
			$(this)
				.parents('.wpsl-filter__item_values')
				.find('input[type="checkbox"]')
				.prop('checked',false);
		})
		.on('click', function( event ) {
			if( $(event.target).closest('.wpsl-filter__result').length ) 
				return;
			$('.wpsl-filter__result').fadeOut(250);
			event.stopPropagation();
		})
		.on('change', '.wpsl-filter input[type="checkbox"]', function() {
			selectProducts( 'filter_product_count', {} );
		})
		.on('submit', '.wpsl-filter', function(e) {
			e.preventDefault();
			sortObj = {};
			if ( $('div').is('.wpsl-sort') ) {
				sort = $('.wpsl-sort').find('span.active');
				if ( sort ) {
					sortObj = { order: sort.data('order'), orderby: sort.data('orderby'), term_id: sort.data('term') };
				}
			}
			selectProducts( 'get_products', sortObj );
			$('.wpsl-filter__result').fadeOut(250);
		})
		.on('scroll', function(e) {
			if ( $('div').is('.wpsl-filter__result') ) {
				var offset = $('.wpsl-filter__result').offset();
				var topPadding = 80;
				if ( $(window).scrollTop() > offset.top ) {
					$('.wpsl-filter__result').stop().animate({marginTop: $(window).scrollTop() - offset.top + topPadding});
				} else {
					$('.wpsl-filter__result').stop().animate({marginTop: 0});
				}
			}
		});
		$('.range-slider-price').jRange({
			showLabels: true,
			isRange : true,
			ondragend: function () {
				selectProducts( 'filter_product_count', {} );
			},
			onbarclicked: function () {
				selectProducts( 'filter_product_count', {} );
			},
		});
		
		
	/**
	 * Mobile menu
	 */
	$(document)
		.on('click', '.wpsl-mobile__box .wpsl-close', function(e) {
			$('.wpsl-mobile__box').slideUp('fast');
			return false;
		})
		.on('click', '.wpsl-mobile__menu_item.active', function(e) {
			$('.wpsl-mobile__box').slideDown('fast');
			$(this).siblings('div').removeClass('wpsl-ajax');
			if ( $(this).hasClass('wpsl-ajax') ){
				return false;
			} else {
				$.ajax({
					url  : WPSL.ajaxurl,
					type : 'POST',
					cache: false,
					data: ({
						action : 'mobile_menu',
						type   : $(this).attr('data-type'),
					}),
					beforeSend: function() {
						$(this).parents('.wpsl-mobile__box').append('<div class="wpsl-preloader"><img src="' + WPSL.preloader + '" /></div>');
						$('.wpsl-mobile__box_head .wpsl-header h3').text($(this).attr('data-title'));
					},
					success: function(data){
						$('.wpsl-mobile__box_body').html(data).empty();
						$('.wpsl-preloader').remove();
					}
				});
				$(this).addClass('wpsl-ajax');
			}
		})
		.on('mousewheel', function(event) {
			if (event.originalEvent.wheelDelta >= 0) {
				// scroll to up
				$('.wpsl-mobile').animate({'margin-bottom': '0'});
			} else {
				// to button
				$('.wpsl-mobile').animate({'margin-bottom': '-70px'});
			}
		});
		
	/**
	 * Cart and order pages
	 */
	function change_cart(_this) {
		$.ajax({
			url  : WPSL.ajaxurl,
			type : 'POST',
			cache: false,
			data: ({
				action : 'change_cart',
				str: _this.parents('form').serialize(),
			}),
			success: function(data){
				data = JSON.parse( data );
				$('.wpsl-c').replaceWith(data.cart);
				$('.wpsl-loader').remove();
				if ( $('span').is('.product-basket-total') ) {
					$('.wpsl-count-box').html(data.count);
				}
			}
		});
	}
	if ( !$('input[name="policy"]').prop('checked') ) {
		$('input[name="policy"]').siblings('.wpsl-hidden').slideDown(250)
			.parents('form')
			.find('input[type="submit"]')
			.addClass('wpsl-disabled');
	}
	$(document)
		// delete product from cart
		.on('click', '.wpsl-delete', function(){
			$(this).parents('.wpsl-table__row').append('<div class="wpsl-loader"></div>');
			var row   = $(this).parents('.wpsl-table__row'),
				input = row.children('.quo').children('input');
			input.val('0');
			change_cart($(this));
		})
		// change cart
		.on('change', '.quo input', function(){
			$(this).parents('.wpsl-table__row').append('<div class="wpsl-loader"></div>');
			change_cart($(this));
		})
		// apply coupon
		.on('click', '#update_cart .apply', function(){
			$(this).append('<div class="wpsl-loader"></div>');
			change_cart($(this));
			return false;
		})
		// add product to cart
		.on('click', '.wpsl-add-to-cart', function() {
			var url = $(this).attr('href');
			$.get(
				url, { action: 'send_simple', id: $(this).data('id'), nonce: WPSL.nonce }
			);
			$(this).attr('href', url.split('?')[0]).text($(this).attr('data-click')).removeClass('wpsl-add-to-cart');
			return false;
		})
		// policy
		.on('click', 'input[name="policy"]', function(){
			if ( $(this).prop('checked') ) {
				$(this).siblings('.wpsl-hidden').slideUp(250)
					.parents('form')
					.find('input[type="submit"]')
					.toggleClass('wpsl-disabled');
			} else {
				$(this).siblings('.wpsl-hidden').slideDown(250)
					.parents('form')
					.find('input[type="submit"]')
					.toggleClass('wpsl-disabled');
			}
		})
		// checking phone for authenticity
		.on('click', '#sms-send-code', function(){
			var _this = $(this);
			$.ajax({
				type: 'POST',
				url: WPSL.ajaxurl,
				data: {
					phone : $(this).parents('form').find('input[name=userphone]').val(),
					action: 'check_phone'
				},
				beforeSend: function() {
					_this.addClass('wpsl-disabled').text(WPSL.sending);
				},
				success: function(msg) {
					$('#sms-verification').html(msg);
				},
				complete: function(msg) {
					_this.removeClass('wpsl-disabled').text(WPSL.send);
				}
			});
			return false;
		});
		
		
	/**
	 * Smart products search
	 */
	$(document)
		.on('input', '.wpsl-search__text', function() {
			var searchTerm = $(this).val();
			// проверим, если в поле ввода более 2 символов, запускаем ajax
			if( searchTerm.length > 2 ){
				$.ajax({
					url : WPSL.ajaxurl,
					type: 'POST',
					data:{
						'action' : 'smart_search',
						'term'   : searchTerm
					},
					success:function(result){
						$('.wpsl-search__result').fadeIn().html(result);
					}
				});
			}
		})
		.on('click', '.wpsl-searchbox__close', function(){
			$(this).parents('.wpsl-search__result').fadeOut();
		})
		// скроллим при фокусировки в инпут
		.on('click', '.wpsl-search__text', function(){
			var scroll_el = $('.wpsl-search__text');
			if ($(scroll_el).length != 0) {
				$('html, body').animate({ scrollTop: $(scroll_el).offset().top - 50 }, 500);
			}
			return false;
		});
		
		
	/**
	 * Review form
	 */
	$(document)
		.on('click', '.wpsl-review__add', function() {
			var forma = $(this).parents('.wpsl-reviews').children('.wpsl-reviews__form').show();
			$('html, body').animate({ scrollTop: $('.wpsl-reviews').offset().top - 95 }, 400);
			return false;
		})
		.on('click', '.wpsl-rating__icon', function() {
			$(this)
				.siblings('.wpsl-rating__txt').remove()
				.parents('.wpsl-rating')
				.prepend('<span class="wpsl-rating__txt">' + $(this).attr('title') + '</span>');
		})
		.on('click', '.wpsl-mark__item_grade', function() {
			var grade = $(this).attr('data-grade');
			$(this).addClass('active').parents('li').siblings('li').find('.wpsl-mark__item_grade').removeClass('active');
			$('.wpsl-reviews__list .wpsl-review').each(function(){
				//console.log( grade );
				if ( grade !== 'all' && $(this).attr('data-grade') !== 'all' ) {
					if( $(this).attr('data-grade') === grade ) {
						$(this).show();
					} else {
						$(this).hide();
					}
				} else {
					$(this).show();
				}
			})
		});


	/**
	 * AJAX forms
	 */
	$('body').on('click', 'button[type="submit"]', function() {
		var button = $(this),
			action = button.attr('data-action'),
			forma  = button.parents('#'+action);
		if ( typeof(action)!='undefined' && forma.data('ajax') == 1 ) {
			// check null fields
			var errors = 0;
			var field = [];
			forma.find('input[data-validate], textarea[data-validate]').each(function() {
				field.push($(this).attr('name'));
				for (var i = 0; i < field.length; i++) {
					if (!$(this).val()) {
						$(this).css('border-color', 'red');
						setTimeout(function() {
							$(this).css('border-color', '#eaeaea');
						}.bind(this), 2000);
						errors++;
					}
				}
			});
			
			// check required fields
			$('.wpsl-variations').each(function() {
				console.log( $(this).find('input:radio:checked').length );
				if ( $(this).find('input:radio:checked').length == 0 ) {
					$(this).children('.wpsl-hidden').fadeIn(0);
					setTimeout(function() {
						$(this).children('.wpsl-hidden').fadeOut(0)
					}.bind(this),3000);
					errors++;
				}
			});
			
			// if no errors send form
			if( errors == 0 ) {
				$.ajax({
					url: WPSL.ajaxurl,
					type: forma.attr('method'),
					cache: false,
					data: {
						action : action,
						str: forma.serialize(),
						nonce: WPSL.nonce,
					},
					beforeSend: function() {
						button.css({'color':'transparent'}).append(WPSL.loader);
					},
					success: function(data){
						forma.children('.wpsl-result').html(data).fadeIn(200);
						button.removeAttr('style');
						forma.find('.wpsl-preloader').remove();
						forma[0].reset();
					}
				});
				return false;
			}
		}
	});
	/**
	 * Single product tabs
	 */
	$('ul.wpsl-tabs__menu').on('click', 'li:not(.active)', function() {
		$(this).addClass('active').siblings().removeClass('active')
			.closest('.wpsl-tabs').children('.wpsl-tabs__content').find('.wpsl-tabs__content_item').removeClass('active').eq($(this).index()).addClass('active');
		var ulIndex = $('ul.wpsl-tabs__menu').index($(this).parents('ul.wpsl-tabs__menu'));
	});
	/**
	 * Single product select variations
	 */
	$('#wpsl-buy').on('change', function() {
		var sel = [];
		var form = this;
		$('#wpsl-buy').find('input[type="radio"]:checked, select').each(function() {
			sel.push($(this).attr('name'));
		});
		var data = sel.reduce(function(obj, variation) {
			obj[variation] = form[variation].value
			return obj
		}, {})
		var findElem = WPSL.variable.filter(function(elem) {
			return sel.every(function(key) {
			   return elem.item_variation[key] == data[key]
			})
		})
		var id = '',
			price = '';
		if (findElem.length) {
			id = findElem[0].item_id;
			price = findElem[0].item_price
		}
		$('#wpsl-buy [name="price"]').val(price);
		$('#wpsl-buy [name="product-id"]').val(id);
	});
	$(document)
		// счетчик загрузок бесплатных товаров
		.on( 'click', '.wps-download', function() {
			$(this).text('Загружаю');
			$.ajax({
				url : WPSL.ajaxurl,
				type: 'post',
				data: {
					action : 'download',
					post_id : $(this).data('id')
				}
			});
		});
});



/**
 * JS product photogallery
 */
(function($, undefined) {
	$.fn.maxGallery = function(options) {
		var defaults = {},
		$this = $(this);
		options = $.extend({}, defaults, options);
		// find all thumbs
		var length = $('.wpsl-thumbs').find('a').length;
		if ( length === 0 ) {
			var length = $('.wpsl-gallery__thumb').find('a').length;
		}
		var href, arrOfImgs = [];
		for (var i = 0; i<length; i++) {
			href = $('.wpsl-thumbs')
				.find('a')
					.eq(i)
						.attr('href');

			arrOfImgs.push(href);
		}
		$(document)
		.on('click', '.wpsl-item', function(e) {
			return false;
		});
		var Gallery = {
			id: null,
			title: '',
			init: function() {
				var _this = this;
				$(document)
				.on('click', '.wpsl-item', function(e) {
					dataid = $('.wpsl-item img').attr('data-id');
					_this.id = parseInt(dataid);
					_this.show(_this.id);
					return false;
				})
				/* .on('click', '.wpsl-thumbs__main', function() {
					dataid = $(this).children().attr('data-id');
					_this.id = parseInt(dataid);
					_this.show(_this.id);
					return false;
				}) */
				.on('click', '.wpsl-thumbs__main', function(e) {
					var _img = jQuery(this).children();
					var href = jQuery(this).prop('href');
					jQuery(this).addClass('current').siblings().removeClass('current');
					jQuery('.wpsl-gallery__thumb a').attr('href', href);
					$('.wpsl-gallery__thumb a img').attr({ 'src': href, 'data-id': _img.attr('data-id'), 'data-title': _img.attr('data-title') });
					jQuery('.wpsl-slider__item-inner img').attr('src', href);
					return false;
				})
				.on('click', '.wpsl-btn-next', function(e) {
					_this.next();
					e.preventDefault();
				})
				.on('click', '.wpsl-btn-prev', function(e) {
					_this.prev();
					e.preventDefault();
				})
				.on('click', '.wpsl-btn-close', function() {
					_this.hide();
				})
				.on('click', '.wpsl-btn-full', function() {
					var element = document.body;
					var req = element.requestFullScreen || element.webkitRequestFullScreen || element.mozRequestFullScreen;		
					if(req) {
						req.call(element);
					} else {
						var wscript = new ActiveXObject('Wscript.shell');
						wscript.SendKeys('{F11}');
					}
					return false;
				})
				.on('keydown', function(e) {
					if (!$this.is(':visible')) {
						return;
					} else if (e.which === 39) {
						_this.next();
					} else if (e.which === 37) {
						_this.prev();
					} else if (e.which === 27) {
						_this.hide();
					} else if (e.which === 38) {
						this.id = length-1;
						_this.prev(this.id);
					} else if (e.which === 40) {
						this.id = 0;
						_this.prev(this.id);
					}
				});
			},
			show: function(id) {
				$('.wpsl-cur-img').attr('src', arrOfImgs[id]);
				$this.show();
				this.setNum();
				this.setTitle();
				this.setHash();
			},
			next: function() {
				var id = arrOfImgs[this.id + 1] ? this.id + 1 : 0;
				this.id = id;
				$('.wpsl-cur-img').attr('src', arrOfImgs[id]);
				this.setNum();
				this.setTitle();
				this.setHash();
			},
			prev: function(idSet) {
				var id;
				if (idSet !== undefined) {
					id = idSet;
				} else {
					id = arrOfImgs[this.id - 1] ? this.id - 1 : arrOfImgs.length - 1;
				}
				this.id = id;
				$('.wpsl-cur-img').attr('src', arrOfImgs[id]);
				this.setNum();
				this.setTitle();
				this.setHash();
			},
			hide: function() {
				$this.hide();
			},
			setHash: function() {
				//window.location.hash = '#img' + (this.id + 1);
			},
			setNum: function () {
				$('.wpsl-slider__item-number').text(this.id+1 + ' '+WPSL.of+' ' + length);
			},
			setTitle: function() {
				var title = $('.wpsl-thumbs__main').eq(this.id).find('img').data('title');
				$('.wpsl-slider__item-title').text(title);
			}
		};
		Gallery.init();
	};

})(jQuery);
$(function() {
	$('.wpsl-slider').maxGallery();
});

/**
 * JS of products carousel
 */
var itemSlider = {
    xDown:null,                                                        
    yDown:null,
    
    init: function() {
        $('.wpsl-carousel').each(function(index){
            var slider = this;
            var $movingDiv = $('.wpsl-carousel__wrap', this).first();
            //according to this: https://stackoverflow.com/questions/2264072/detect-a-finger-swipe-through-javascript-on-the-iphone-and-android
            $movingDiv[0].addEventListener('touchstart', itemSlider.handleTouchStart, false); 
            $movingDiv[0].addEventListener('touchmove', itemSlider.handleTouchMove, false);
            console.log($movingDiv[0]);
            var movingWidth = itemSlider.getMovingWidth(this);
            if (movingWidth > $(this).width()) {
                //$('.wpsl-carousel__control', this).show();
            } else {
				$('.wpsl-carousel__control', this).hide();
			}
            $('.next.wpsl-carousel__control', this).click(function(e){
                e.preventDefault();
				console.log( slider );
                itemSlider.moveDiv('prev', slider);
            });
            $('.prev.wpsl-carousel__control', this).click(function(e){
                e.preventDefault();
                itemSlider.moveDiv('next', slider);
            });
        });
    },
    getMovingWidth: function(slider) {
        var $items = $('.wpsl-carousel__wrap_item', slider);
        var numberOfItems = $items.length;
        var itemWidth = numberOfItems ? $($items[0]).outerWidth(true) : 0;
        var movingWidth = itemWidth * numberOfItems;
        return movingWidth;        
    },
    moveDiv: function(direction, slider) {
		var movingWidth = itemSlider.getMovingWidth(slider);
		var increment = $(slider).width()-$('.wpsl-carousel__control', slider).first().outerWidth(true)*2; //box width minus arrows width
		var maxNegative = $(slider).width() - movingWidth;
		var currentPosition = parseInt($('.wpsl-carousel__wrap', slider).css('margin-left'));
		if (direction == 'prev') {
			var newPosition = currentPosition - increment;
			newPosition = newPosition < maxNegative ? maxNegative : newPosition;
		} else if (direction == 'next') {
			var newPosition = currentPosition + increment;
			newPosition = (newPosition > 0)? 0: newPosition
		}
		$('.wpsl-carousel__wrap', slider).animate({'margin-left': newPosition});
		
		if ( newPosition === maxNegative ) {
			$('.next.wpsl-carousel__control').css('opacity','0');
		} else {
			$('.next.wpsl-carousel__control').css('opacity','1');
		}
		if ( newPosition < 0 ) {
			$('.prev.wpsl-carousel__control').css('opacity','1');
		} else {
			$('.prev.wpsl-carousel__control').css('opacity','0');
		}
    },
    handleTouchStart: function(evt) {
        itemSlider.xDown = evt.touches[0].clientX;                                      
        itemSlider.yDown = evt.touches[0].clientY;                                      
    }, 
    handleTouchMove: function(evt) {
        if ( ! itemSlider.xDown || ! itemSlider.yDown ) {
			return;
        }
        var slider = $(evt.target).closest('.wpsl-carousel')[0];
        
        var xUp = evt.touches[0].clientX;                                    
        var yUp = evt.touches[0].clientY;

        var xDiff = itemSlider.xDown - xUp;
        var yDiff = itemSlider.yDown - yUp;

        if ( Math.abs( xDiff ) > Math.abs( yDiff ) ) {
            if ( xDiff > 0 ) {
                /* prev swipe */
                itemSlider.moveDiv('prev', slider); 
            } else {
                /* next swipe */
                itemSlider.moveDiv('next', slider);
            }                       
        } else {
            if ( yDiff > 0 ) {
                /* up swipe */ 
            } else { 
                /* down swipe */
            }                                                                 
        }
        /* reset values */
        itemSlider.xDown = null;
        itemSlider.yDown = null;
    } 
}
itemSlider.init();