<?php
/**
 * Include css in admin page
 */
add_action( 'admin_print_styles', 'wpsl_include_css_admin' );
function wpsl_include_css_admin() {
	wp_enqueue_style( 'wpsl-admin', WPSL_URL . '/assets/css/wpsl-admin.css' );
	wp_enqueue_style( 'wpsl-icomoon', WPSL_URL . '/assets/css/wpsl-icomoon.css' );
	wp_enqueue_style( 'jqueryui', WPSL_URL . '/assets/css/jquery-ui.css', false, null );
}


/**
 * Include js in admin page
 */
add_action( 'admin_enqueue_scripts', 'wpsl_include_js_admin' );
function wpsl_include_js_admin() {
	wp_enqueue_script( 'sortable', WPSL_URL . '/assets/js/sortable.min.js' );
	wp_enqueue_script( 'wpsl-admin', WPSL_URL . '/assets/js/wpsl-admin.js', array( 'jquery-form' ) );
	wp_localize_script( 'wpsl-admin', 'WPSA',
		apply_filters( 'wpsl_add_data_to_admin_scripts',
			array(
				'work'        => __( 'Work', 'wpsl' ),
				'send'        => __( 'Send', 'wpsl' ),
				'sending'     => __( 'Sending', 'wpsl' ),
				'save_attr'   => __( 'Save attribute', 'wpsl' ),
				'add_attr'    => __( 'Add new attribute', 'wpsl' ),
				'load_atts'   => __( 'You have not selected where to download the attributes', 'wpsl' ),
				'preloader'   => WPSL_URL . '/assets/img/preloader.svg',
				'saved'       => __( 'Saved', 'wpsl' ),
				'already'     => __( 'This image is already in the gallery', 'wpsl' ),
				'sure'        => __( 'Are you sure?', 'wpsl' ),
				'select_file' => __( 'Please select a file to import', 'wpsl' ),
				'file_sended' => __( 'The file is sent. The import process is in progress...', 'wpsl' ),
				'ajax_error'  => __( 'Error ajax request', 'wpsl' ),
				'name'        => __( 'Name of attribute', 'wpsl' ),
				'measure'     => __( 'Mesuare of attribute', 'wpsl' ),
			)
		)
	);
	wp_enqueue_script( 'jquery-ui-datepicker' );
}


/**
 * Include css in frontend
 */
add_action( 'wp_print_styles', 'wpsl_include_css_frontend' );
function wpsl_include_css_frontend() {
	wp_enqueue_style( 'wpsl-layout', WPSL_URL . '/assets/css/wpsl-layout.css' );
	wp_enqueue_style( 'wpsl-icomoon', WPSL_URL . '/assets/css/wpsl-icomoon.css' );
	wp_enqueue_style( 'wpsl-css', WPSL_URL . '/assets/css/wpsl.css' );
	
	global $post;
	if( isset( $post->post_content ) && has_shortcode( $post->post_content, 'wpsl-account' ) ) {
		wp_enqueue_style( 'wpsl-account', WPSL_URL . '/assets/css/wpsl-account.css' );
	}
}


/**
 * Include css in frontend
 */
add_action( 'wp_enqueue_scripts', 'wpsl_include_js_frontend' );
function wpsl_include_js_frontend() {
	global $post;
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'range', WPSL_URL . '/assets/js/jquery-range.js', '', '', true );
	wp_enqueue_script( 'wpsl', WPSL_URL . '/assets/js/wpsl.js', array( 'jquery' ), '', true );
	wp_localize_script( 'wpsl', 'WPSL',
		apply_filters( 'wpsl_add_data_to_localize',
			array(
				'ajaxurl'   => admin_url( 'admin-ajax.php' ),
				'nonce'     => wp_create_nonce( 'wpsl-nonce' ),
				'preloader' => WPSL_URL . '/assets/img/preloader.svg',
				'loader'    => '<div class="wpsl-preloader"><span class="icon-loader"></span></div>',
				'send'      => __( 'Send', 'wpsl' ),
				'sending'   => __( 'Sending', 'wpsl' ),
				'of'        => __( 'of', 'wpsl' ),
			)
		)
	);
	
	if( isset( $post->post_content ) && has_shortcode( $post->post_content, 'wpsl-account' ) ) {
		wp_enqueue_script( 'wpsl-account', WPSL_URL . '/assets/js/wpsl-account.js', '', '', true );
	}
}


add_action( 'admin_footer', 'wpsl_init_datepicker', 99 ); // для админки
function wpsl_init_datepicker(){
	?>
	<script type="text/javascript">
	jQuery(document).ready(function($){
		'use strict';
		$.datepicker.setDefaults({
			closeText: 'Закрыть',
			prevText: '<Пред',
			nextText: 'След>',
			currentText: 'Сегодня',
			monthNames: ['Январь','Февраль','Март','Апрель','Май','Июнь','Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'],
			monthNamesShort: ['Янв','Фев','Мар','Апр','Май','Июн','Июл','Авг','Сен','Окт','Ноя','Дек'],
			dayNames: ['воскресенье','понедельник','вторник','среда','четверг','пятница','суббота'],
			dayNamesShort: ['вск','пнд','втр','срд','чтв','птн','сбт'],
			dayNamesMin: ['Вс','Пн','Вт','Ср','Чт','Пт','Сб'],
			weekHeader: 'Нед',
			dateFormat: 'yy-mm-dd',
			firstDay: 1,
			showAnim: 'slideDown',
			isRTL: false,
			showMonthAfterYear: false,
			yearSuffix: '',
			minDate: 0,
			maxDate: '+30'
		} );
		// init
		$('input[name*="date"], .datepicker').datepicker({ dateFormat: 'yy-mm-dd' });         
	});
	</script>
	<?php
}