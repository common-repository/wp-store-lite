var $ = jQuery;
$(document).ready(function(){
	$(document)
		.on('click', '.wpsl-account__menu_item.collapse', function() {
			$(this).parents('.wpsl-account__menu').toggleClass('collapsed')
				.siblings('.wpsl-account__tabs').toggleClass('collapsed');
		})
		.on('click', '.wpsl-account__menu .check', function() {
			_this = $(this);
			_this.addClass('active').siblings('.wpsl-account__menu_item').removeClass('active');
			$('.wpsl-account__tabs').find('.wpsl-account__tabs_item').each(function(i,el) {
				$(el).removeClass('active');
				if ( _this.data('type') === $(el).data('type') ) {
					$(el).addClass('active');
				}
			});
		})
		// save profile
		.on('click', '#wpsl-save-profile', function() {
			$.ajax({
				url: WPSL.ajaxurl,
				type: 'POST',
				cache: false,
				data: {
					action : 'update_profile',
					str: $('#profileform').serialize(),
				},
				success: function(data){
					$('.wpsl-response').html(data);
				}
			});
			return false;
		})
		// open ticket
		.on('click', '.ticket', function() {
			$.ajax({
				url: WPSL.ajaxurl,
				type: 'POST',
				cache: false,
				data: {
					action   : 'get_ticket',
					ticket_id: $(this).attr('data-id'),
					user_id  : $(this).attr('data-user'),
				},
				beforeSend: function() {
					$('.tickets-content').append('<div class="wpsl-preloader"><img src="' + WPSL.preloader + '" /></div>');
				},
				success: function(data){
					$('.tickets-content').html(data);
				},
				complete: function(data) {
					$('.wpsl-preloader').fadeOut(250);
				}
			});
			return false;
		})
		// create ticket
		.on('click', '.wpsl-tickets__create', function() {
			$('.wps-ticket-form').fadeIn(0);
			$('.wps-no-tickets').fadeOut(0);
		})
		.on('change', '#ticket-order', function() {
			$.ajax({
				url: WPSL.ajaxurl,
				type: 'POST',
				cache: false,
				data: {
					action : 'select_order',
					order: $(this).val(),
				},
				beforeSend: function() {
					$('.ticket-product').append('<div class="wpsl-preloader"><img src="' + WPSL.preloader + '" /></div>');
				},
				success: function(data){
					$('.field-type-empty').html(data);
				},
				complete: function(data) {
					$('.wpsl-preloader').fadeOut(250);
				}
			});
			return false;
		})
		// get order
		.on('click', '.order', function() {
			$.ajax({
				url: WPSL.ajaxurl,
				type: 'POST',
				cache: false,
				data: {
					action  : 'get_order',
					order_id: $(this).attr('data-id'),
					user_id : $(this).attr('data-user'),
				},
				beforeSend: function() {
					$('.order-content').append('<div class="wpsl-preloader"><img src="' + WPSL.preloader + '" /></div>');
				},
				success: function(data){
					$('.order-content').html(data);
				},
				complete: function(data) {
					$('.wpsl-preloader').fadeOut(250);
				}
			});
			return false;
		})
		// get download product
		.on('click', '.wps-order-box .download', function() {
			$.ajax({
				url: WPSL.ajaxurl,
				type: 'POST',
				cache: false,
				data: {
					action : 'regenerate_link',
					id     : $(this).attr('data-id'),
					order  : $(this).attr('data-order'),
				},
				beforeSend: function() {
					$('.product-item .download').append('<div class="wpsl-preloader"><img src="' + WPSL.preloader + '" /></div>');
				},
				success: function(data){
					$(this).html(data).removeClass('download').addClass('active');
				},
				complete: function(data) {
					$('.wpsl-preloader').fadeOut(250);
				}
			});
			return false;
		});
});