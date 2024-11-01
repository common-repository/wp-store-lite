<?php
/**
 * Class for options
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


if ( !is_admin() ) return;


if( ! current_user_can( 'manage_options' ) ) return;


class WPSL_Options {
	
    public $options = '';

    function __construct( $args = false ) {
        $this->init_properties( $args );
		add_action( 'admin_menu', array( $this, 'add_page' ), 10 );
		add_action( 'admin_print_scripts', array( $this, 'add_script' ), 10 );
		add_action( 'wpsl_sidebar_content', array( $this, 'statistic' ), 10 );
    }
    
    function init_properties( $args ){
        $properties = get_class_vars( get_class( $this ) );
        foreach ( $properties as $name=>$val ){
            if( isset( $args[$name] ) ) $this->$name = $args[$name];
        }
    }
	
	/**
	 * Get statistic
	 */
	function statistic() {
		?>
		<style>
		.wpsl-search{
			position: relative;
			margin-bottom: 25px;
		}
		.wpsl-search input{
			display: inline-block;
			padding: 10px 20px;
			border: 0;
			font-size: 14px;
			line-height: 20px;
			height: inherit;
			-webkit-border-radius: 10px;
			-moz-border-radius: 10px;
			border-radius: 10px;
			box-shadow: none !important;
			background-color: #f2f5f9;
			color: #293a4e;
			outline: 0;
			width: 100%;
		}
		.wpsl-search input::-webkit-input-placeholder {color:#a4b7cd;}
		.wpsl-search input::-moz-placeholder          {color:#a4b7cd;}
		.wpsl-search input:-moz-placeholder           {color:#a4b7cd;}
		.wpsl-search input:-ms-input-placeholder      {color:#a4b7cd;}
		.wpsl-search i{
			position: absolute;
			right: 13px;
			top: 13px;
		}
		.wpsl-statistic{
			width: calc(100% + 20px);
			background-color: #F2F5F9;
			float: left;
			margin-right: -20px;
			padding: 10px 35px 20px 35px;
			border-radius: 20px 0 0 20px;
			margin-bottom: 35px;
			position: relative;
			display: -webkit-flex;
			display: -ms-flexbox;
			display: flex;
			-webkit-flex-wrap: wrap;
			-ms-flex-wrap: wrap;
			flex-wrap: wrap;
		}
		.wpsl-statistic__icon{
			position: absolute;
			bottom: -10px;
			right: 50px;
			width: 140px;
			height: 140px;
		}
		.wpsl-statistic__icon:before{
			font-size: 130px;
			color: #f7f9fb;
			z-index: 0;
		}
		.wpsl-statistic__title{
			margin: 0 0 15px;
			font-weight: 900;
			color: #0073aa;
			font-size: 18px;
		}
		.wpsl-statistic__item{
			width: 50%;
			float:left;
			position: relative;
			z-index: 3;
			font-size: 30px;
			font-weight: 900;
			color: #a4b7cd;
			margin-bottom: 15px;
			margin-top: 15px;
		}
		.wpsl-statistic__item > span{
			font-size: 14px;
			font-weight: 400;
			display: block;
			margin-bottom: 7px;
		}
		</style>
		<script>
		jQuery('body').on('input', '.wpsl-search__input', function() {
			var searchTerm = $(this).val();
			jQuery('span.highlight').each(function(){
				jQuery(this).after(jQuery(this).html()).remove();  
			});
			// проверим, если в поле ввода более 2 символов
			if( searchTerm.length > 2 ){
				jQuery('.tab-box').each(function(){ // в селекторе задаем область поиска
					jQuery(this).html(jQuery(this).html().replace(new RegExp(searchTerm, 'ig'), '<span class="highlight" style="background-color: #fdfbe4;">$&</span>')); // выделяем найденные фрагменты
					n = jQuery(this).find('span.highlight').length; // количество найденных фрагментов
					console.log( n );
					i = $(this).index() - 1;
					if (n > 0) {
						$('.item').eq(i).find('pre').remove();
						$('.item').eq(i).find('span').before('<pre>'+n+'</pre>');
					} else {
						$('.item').eq(i).find('pre').remove();
					}
				});
			}
		});
		</script>
		<!--div class="wpsl-search"><input class="wpsl-search__input" value="" placeholder="< ?php _e( 'Search by settings', 'wpsl' ); ?>"/><i class="icon-search"></i></div-->
		<h2 class="wpsl-statistic__title"><?php _e( 'Store statistics', 'wpsl' ); ?></h2>
		<div class="wpsl-statistic">
			<div class="wpsl-statistic__item"><span><?php _e( 'Products', 'wpsl' ); ?></span><?php echo wp_count_posts( 'product' )->publish; ?></div>
			<div class="wpsl-statistic__item"><span><?php _e( 'Orders', 'wpsl' ); ?></span><?php echo wp_count_posts( 'shop_order' )->publish; ?></div>
			<div class="wpsl-statistic__item"><span><?php _e( 'Sales', 'wpsl' ); ?> (<?php echo wpsl_opt(); ?>)</span><?php echo preg_replace( '/000/', 'k', wpsl_orders_total() ); ?></div>
			<?php if ( in_array( 'support', get_post_types() ) ) : ?>
			<div class="wpsl-statistic__item"><span><?php _e( 'Tickets', 'wpsl' ); ?></span><?php echo wp_count_posts( 'support' )->publish; ?></div>
			<?php endif; ?>
			<div class="wpsl-statistic__item"><span><?php _e( 'Reviews', 'wpsl' ); ?></span><?php echo count( wpsl_get_user_review( array( 'user_id' => '' ) ) ); ?></div>
			<div class="wpsl-statistic__icon dashicons dashicons-admin-site"></div>
		</div>
		<?php
	}

	/**
	 * Add option page to admin
	 */
	function add_page() {
		add_menu_page( __( 'wpStore options', 'wpsl' ), __( 'wpStore', 'wpsl' ), 'manage_options', 'wpsl_options', array( &$this, 'fill_page' ), 'dashicons-admin-generic', 100 );
    }

	/**
	 * Add script
	 */
	function add_script() {
		wp_enqueue_script( 'selectize', WPSL_URL . '/assets/js/selectize.min.js', array( 'jquery' ) );
	}
	
	/**
	 * Get field
	 */
	function get_field( $field ) {
		$html = '';
		if ( isset( $field['std'] ) && $field['std'] != '' && wpsl_opt( $field['id'] ) == '' ) {
			$val = $field['std'];
		} else {
			$val = wpsl_opt( $field['id'] );
		}
		switch ( $field['type'] ) {
			case( 'text' ):
				$html .= '<input type="text" id="wpsl-' . $field['id'] . '" name="' . $field['id'] . '" placeholder="' . $field['title'] . '" value="' . $val . '" class="wpsl-text" autocomplete="off" readonly onfocus="this.removeAttribute(\'readonly\')">';
				break;
			case( 'number' ):
				$html .= '<input type="number" id="wpsl-' . $field['id'] . '" name="' . $field['id'] . '" value="' . $val . '" class="wpsl-text validate-number" autocomplete="off">';
				break;
			case( 'email' ):
				$html .= '<input type="text" id="wpsl-' . $field['id'] . '" name="' . $field['id'] . '" value="' . $val . '" class="wpsl-text validate-email" autocomplete="off">';
				break;
			case( 'checkbox' ):
				$html .= '<input type="checkbox" id="wpsl-' . $field['id'] . '" name="' . $field['id'] . '" value="' . $val . '" ' . checked( true, wpsl_opt( $field['id'] ), false ) . ' class="wpsl-switch">';
				$html .= '<label class="switch-label" for="wpsl-' . $field['id'] . '"></label>';
				break;
			case( 'textarea' ):
				$html .= '<textarea id="wpsl-' . $field['id'] . '" name="' . $field['id'] . '">' . $val . '</textarea>';
				break;
			case( 'upload' ):
				$html .= '<input type="hidden" id="wpsl-' . $field['id'] . '" name="' . $field['id'] . '" value="' . $val . '">';
				$src = wpsl_opt( $field['id'] ) != '' ? wpsl_opt( $field['id'] ) : 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAGQAAABkAQMAAABKLAcXAAAABlBMVEUAAAC7u7s37rVJAAAAAXRSTlMAQObYZgAAACJJREFUOMtjGAV0BvL/G0YMr/4/CDwY0rzBFJ704o0CWgMAvyaRh+c6m54AAAAASUVORK5CYII=';
				if ( wpsl_opt( $field['id'] ) == '' && $field['std'] ) {
					$src = $field['std'];
				}
				$html .= '<span class="wpsl-upload-img"><img src="' . $src . '" /><span class="wpsl-remove-img"><i class="dashicons dashicons-no"></i></span></span><i class="wpsl-reset-img dashicons dashicons-image-rotate" data-default="' . $field['std'] . '" title="' . __( 'Reset to default', 'wpsl' ) . '"></i>';
				break;
			case( 'select' ):
				$multi = isset( $field['multi'] ) ? 'multiple' : '';
				$arr = isset( $field['multi'] ) ? '[]' : '';
				$html .= '<select id="wpsl-' . $field['id'] . '" class="wpsl-select" name="' . $field['id'] . $arr . '" ' . $multi . '>';
				if ( $field['opts'] ) {
					foreach ( $field['opts'] as $key => $val ) {
						if ( is_array( wpsl_opt( $field['id'] ) ) ) {
							$selected = in_array( $key, wpsl_opt( $field['id'] ) ) ? 'selected="selected"' : '';
						} else {
							$selected = selected( wpsl_opt( $field['id'] ), $key, false );
						}
						$html .= '<option value="' . $key . '" ' . $selected . '>' . $val . '</option>';
					}
				}
				$html .= '</select>';
				break;
			case( 'editor' ):
				$settings = array(
					'wpautop'          => true,
					'textarea_name'	   => $field['id'],
					'media_buttons'	   => true,
					'drag_drop_upload' => true,
					'teeny'			   => false,
					'quicktags'		   => true,
					'textarea_rows'	   => 15,
				);
				if ( isset( $field['opts'] ) ) {
					$settings = array_merge( $settings, $field['opts'] );
				}
				ob_start();
				$html .= wp_editor( $val, str_replace( '_', '', $field['id'] ), $settings );
				$html .= ob_get_contents();
				ob_end_clean();
				break;
			case( 'custom' ):
				$html .= isset( $field['html'] ) ? $field['html'] : '';
				break;
		}
		return $html;
	}
	
	/**
	 * Add option page to admin
	 */
	function get_options( $groups ) {
		$html = '';
		if ( $groups ) {
			$i = 0;
			foreach( $groups as $group ) {
				$active = ( $i == 0 ) ? ' active' : '';
				$html .= '<div class="group' . $active . '">';
				foreach( $group as $field ) {
					$class = isset( $field['rel'] ) ? $field['rel'] : 'parent';
					$html .= '<div class="field ' . $field['id'] . ' ' . $class . '">';
					if ( $field['type'] != 'custom' ) {
						$html .= '<div class="option" title="' . __( 'Copy option name', 'wpsl' ) . '">' . $field['id'] . '</div>';
					}
					$html .= '<div class="title">';
					$html .= $field['title'];
					if ( isset( $field['desc'] ) && $field['desc'] != '' ) {
						$html .= '<span class="desc">' . $field['desc'] . '</span>';
					}
					$html .= '</div>';
					$html .= '<div class="control">';
					$html .= $this->get_field( $field );
					if ( isset( $field['help'] ) && $field['help'] != '' ) {
						$html .= '<span class="help">' . $field['help'] . '</span>';
					}
					$html .= '</div>';
					$html .= '</div>';
				}
				$html .= '</div>';
				$i++;
			}
		}
		return $html;
    }
	
	/**
	 * Add option page to admin
	 */
	function get_tabs() {
		$options = array();
		if ( $options = $this->options ) {
			// menu
			$tabs = '<div class="wpsl-opt__content_menu">';
			$tabs .= '<h2>WP STORE</h2>';
			$tabs .= '<ul class="opt-tabs">';
			$i = 0;
			foreach ( $options as $option ) {
				$class = $i == 0 ? 'item active' : 'item';
				$tabs .= '<li class="' . $class . '"><i class="dashicons ' . $option['icon'] . '"></i>' . $option['title'] . '<span>' . $option['desc'] . '</span></li>';
				$i++;
			}
			$tabs .= '</ul>';
			$tabs .= '</div>';
			
			// content
			$i = 0;
			foreach ( $options as $option ) {
				$class = $i == 0 ? 'tab-active' : '';
				$tabs .= '<div class="tab-box ' . $class . '">';
				$tabs .= $this->get_options( $option['groups'] );
				$tabs .= '</div>';
				$i++;
			}
			
			return $tabs;
		}
    }
	
	/**
	 * Fill option page
	 */
	function fill_page() {
		?><style>
			#wpcontent, #wpfooter {
				background-color: #f2f5f9;
			}
			#wpbody-content {
				padding-bottom: 35px;
			}
			.wpsl-opt {
				margin-left: -20px;
				padding: 30px 0;
				min-height: 80vh;
				background-color: #f7f9fb;
				width: calc(100% + 20px);
				display: table;
			}
			.wpsl-opt__content{
				padding: 0;
				position: relative;
				display: inline-block;
				float: left;
				width: calc(100% - 300px);
				box-sizing: border-box;
			}
			.wpsl-opt__content_menu{
				display: inline-block;
				float:left;
				margin: 0;
				width: 215px;
			}
			.wpsl-opt__content_menu h2{
				color: #0073aa;
				font-weight: 900;
				font-size: 22px;
				padding-left: 50px;
			}
			.wpsl-opt__content .tab-active{
				display: inline-block !important;
			}
			.wpsl-opt__content .tab-box {
				overflow: hidden;
				display: inline-block;
				display: none;
				width: calc(100% - 215px);
				float: left;
				min-height: 100vh;
				box-sizing: border-box;
				background-color: #fff;
				border-radius: 20px;
				box-shadow: 0 0 35px #EFF2F5;
			}
			.wpsl-opt__content .tab-box .group{
				clear: none;
				padding: 35px;
				display: inline-block;
				box-sizing: border-box;
				width:100%;
				float: left;
			}
			.wpsl-opt__content .tab-box .group:nth-child(2n){
				background-color: rgba(247, 249, 251, 0.7);
			}
			.wpsl-opt__content .tab-box .group:nth-child(2n) input, .wpsl-opt__content .tab-box .group:nth-child(2n) textarea, .wpsl-opt__content .tab-box .group:nth-child(2n) .selectize-input {
				background-color: #fff;
			}
			.wpsl-opt__content .tab-box .group.active{
				display: block;
			}
			.wpsl-opt__sidebar{
				background-color: #F7F9FB;
				padding: 0 35px;
				z-index: 3;
				overflow: hidden;
				box-sizing: border-box;
				width: 300px;
				float: left;
				height: calc(100vh - 20px);
				position: static;
				position: sticky;
				top: 62px;
				outline: none;
			}
			.wpsl-opt .wpsl-btn{
				width: 100%;
				height: 100%;
				display: inline-block;
			}
			.wpsl-save-options{
				border: 0;
				padding: 15px;
				border-radius: 35px;
				width: 100%;
				font-weight: 900;
				background-color: #0073AA;
				color: #fff;
				float: left;
				display: inline-block;
				position: relative;
				cursor: pointer;
				outline: none;
				box-sizing: border-box;
			}
			.wpsl-opt .opt-tabs {
				width: 100%;
				margin: 50px 0 0;
			}
			.wpsl-opt .opt-tabs li {
				display: block;
				width: 100%;
				margin: 0;
				padding: 11px 15px;
				box-sizing: border-box;
				color: #95a8bd;
				cursor: pointer;
				font-weight: 700;
				line-height: 1.6;
				position: relative;
				text-decoration: none;
				border-left: 3px solid transparent;
			}
			.wpsl-opt .opt-tabs li:hover, .wpsl-opt .opt-tabs li.active, .wpsl-opt .opt-tabs li.active:hover{
				background-color: #F2F5F9;
				width: 100%;
				color: #0073aa;
				border-left: 3px solid #0072A9;
			}
			.wpsl-opt .opt-tabs li i{
				margin-right: 11px;
				top: 7px;
				position: relative;
			}
			.wpsl-opt .opt-tabs li span{
				display: block;
				padding-left: 31px;
				font-weight: 400;
				line-height: 1;
				text-transform: lowercase;
				font-style: italic;
			}
			.field{
				width: 100%;
				display: inline-block;
				float: left;
				margin-top: 10px;
				position: relative;
				padding: 5px 0;
			}
			.field > .option{
				position: absolute;
				top: 0px;
				font-size: 0;
				z-index: 2;
				right: -5px;
				width: 18px;
				height: 18px;
				box-sizing: border-box;
				border: 5px solid #fff;
				border-radius: 100%;
				cursor: pointer;
				color: #a4b7cd;
				background-color: #ddeaf7;
			}
			.field > .option:hover{
				background-color: #0073aa;
			}
			.field:first-child {
				margin: 0;
			}
			.field .title, .field .control{
				float:left;
				display: inline-block;
			}
			.field .title{
				font-weight: bold;
				font-size: 16px;
				line-height: 1;
				width: 200px;
				box-sizing: border-box;
				padding: 0 35px 5px 0;
				color: #6d8caf;
			}
			.field .title .desc{
				font-weight: normal;
				display: block;
				color: #a4b7cd;
				margin-top: 5px;
				line-height: 1;
				font-size: 13px;
				font-style: italic;
			}
			.field .control{
				width: calc(100% - 200px);
			}
			.field .control .help{
				font-weight: normal;
				display: block;
				color: #a4b7cd;
				margin-top: 5px;
				line-height: 1;
				font-size: 13px;
				width: 100%;
				float: left;
				font-style: italic;
			}
			.field > div > input, .field > div > textarea  {
				display: inline-block;
				padding: 10px 20px;
				border: 0;
				font-size: 14px;
				line-height: 20px;
				height: inherit;
				-webkit-border-radius: 10px;
				-moz-border-radius: 10px;
				border-radius: 10px;
				box-shadow: none !important;
				background-color: #f7f9fb;
				color: #293a4e;
				outline: 0;
				width: 100%;
			}
			.field > div > input[type="number"]{
				width: 100px;
			}
			.field > div > input::-webkit-input-placeholder {color:#a4b7cd;}
			.field > div > input::-moz-placeholder          {color:#a4b7cd;}
			.field > div > input:-moz-placeholder           {color:#a4b7cd;}
			.field > div > input:-ms-input-placeholder      {color:#a4b7cd;}
			.field > div > textarea{
				min-height: 100px;
			}
			.field .wpsl-switch {
				display: none!important;
			}
			.field .switch-label {
				display: block;
				overflow: hidden;
				cursor: pointer;
				height: 18px;
				width: 36px;
				float: left;
				position: relative;
				padding: 0;
				line-height: 18px;
				border: 2px solid #A4B7CD;
				border-radius: 18px;
				background-color: #A4B7CD;
				transition: background-color 0.2s ease-in;
			}
			.field .switch-label:before {
				content: "";
				display: block;
				width: 15px;
				margin: 0;
				background: #ffffff;
				position: absolute;
				top: 0;
				bottom: 0;
				right: 16px;
				border: 2px solid #A4B7CD;
				border-radius: 18px;
				transition: all 0.2s ease-in 0s;
			}
			.field .wpsl-switch:checked + .switch-label {
				background-color: #0085ba;
			}
			.field .wpsl-switch:checked + .switch-label,
			.field .wpsl-switch:checked + .switch-label:before {
				border-color: #0085ba;
			}
			.field .wpsl-switch:checked + .switch-label:before {
				right: 0;
			}
			
			.field.useful{
				padding: 25px;
				border-radius: 15px;
				background-color: #E9F3ED;
				box-sizing: border-box;
				color: #4C6D55;
				width: 100%;
				margin-top: 20px;
				margin-bottom: 20px;
			}
			.field.useful:before{
				content: '';
				position: absolute;
				width: 200px;
				height: 140px;
				left: 30px;
				top: -15px;
				background-repeat: no-repeat;
				background-size: 130px;
				background-image: url(<?php echo WPSL_URL . '/addons/img/quality.png'; ?>);
			}
			.field.useful > .option{
				display: none;
			}
			.field.useful .title{
				color: #4C6D55;
				width: 100%;
				padding-left: 175px;
			}
			.field.useful .title .desc{
				font-size: 14px;
				color: #4c6d55;
				margin-top: 15px;
				line-height: 1.4;
			}
			.field.useful .control{
				width: 100%;
			}
			.field.useful .control a{
				color: #23af5b;
				text-align: right;
				font-weight: 700;
				display: block;
			}
			
			/* SELECT */
			.selectize-control.plugin-drag_drop.multi>.selectize-input>div.ui-sortable-placeholder {
				background: #F2F5F9 !important;
				background: rgba(0,0,0,0.06) !important;
				border: 0 none !important;
				box-shadow: inset 0 0 12px 4px #fff;
				visibility: visible !important;
				webkit-box-shadow: inset 0 0 12px 4px #fff;
			}
			.selectize-control.plugin-drag_drop .ui-sortable-placeholder::after {
				content: '!';
				visibility: hidden;
			}
			.selectize-control.plugin-drag_drop .ui-sortable-helper {
				box-shadow: 0 2px 5px rgba(0,0,0,0.2);
				webkit-box-shadow: 0 2px 5px rgba(0,0,0,0.2);
			}
			.selectize-dropdown-header {
				background: #F7F9FB;
				border-bottom: 1px solid #d0d0d0;
				border-radius: 3px 3px 0 0;
				moz-border-radius: 3px 3px 0 0;
				padding: 5px 8px;
				position: relative;
				webkit-border-radius: 3px 3px 0 0;
			}
			.selectize-dropdown-header-close {
				color: #303030;
				font-size: 20px !important;
				line-height: 20px;
				margin-top: -12px;
				opacity: .4;
				position: absolute;
				right: 8px;
				top: 50%;
			}
			.selectize-dropdown-header-close:hover {
				color: #000;
			}
			.selectize-dropdown.plugin-optgroup_columns .optgroup {
				border-right: 1px solid #F2F5F9;
				border-top: 0 none;
				box-sizing: border-box;
				float: left;
				moz-box-sizing: border-box;
				webkit-box-sizing: border-box;
			}
			.selectize-dropdown.plugin-optgroup_columns .optgroup:last-child {
				border-right: 0 none;
			}
			.selectize-dropdown.plugin-optgroup_columns .optgroup:before {
				display: none;
			}
			.selectize-dropdown.plugin-optgroup_columns .optgroup-header {
				border-top: 0 none;
			}
			.selectize-control.plugin-remove_button [data-value] {
				padding-right: 24px !important;
				position: relative;
			}
			.selectize-control.plugin-remove_button [data-value] .remove {
				border-left: 1px solid #d0d0d0;
				border-radius: 0 2px 2px 0;
				bottom: 0;
				box-sizing: border-box;
				color: inherit;
				display: inline-block;
				font-size: 12px;
				font-weight: bold;
				moz-border-radius: 0 2px 2px 0;
				moz-box-sizing: border-box;
				padding: 2px 0 0 0;
				position: absolute;
				right: 0;
				text-align: center;
				text-decoration: none;
				top: 0;
				vertical-align: middle;
				webkit-border-radius: 0 2px 2px 0;
				webkit-box-sizing: border-box;
				width: 17px;
				z-index: 1;
			}
			.selectize-control.plugin-remove_button [data-value] .remove:hover {
				background: rgba(0,0,0,0.05);
			}
			.selectize-control.plugin-remove_button [data-value].active .remove {
				border-left-color: #cacaca;
			}
			.selectize-control.plugin-remove_button .disabled [data-value] .remove:hover {
				background: 0;
			}
			.selectize-control.plugin-remove_button .disabled [data-value] .remove {
				border-left-color: #fff;
			}
			.selectize-control {
				position: relative;
				width: 100%;
			}
			.selectize-dropdown,.selectize-input,.selectize-input input {
				color: #303030;
				font-family: inherit;
				font-size: 13px;
				line-height: 18px;
				webkit-font-smoothing: inherit;
			}
			.selectize-input,.selectize-control.single .selectize-input.input-active {
				cursor: text;
				display: inline-block;
			}
			.selectize-input {
				display: inline-block;
				padding: 10px 20px;
				border: 0;
				font-size: 14px;
				line-height: 20px;
				height: inherit;
				-webkit-border-radius: 10px;
				-moz-border-radius: 10px;
				border-radius: 10px;
				box-shadow: none !important;
				background-color: #f7f9fb;
				color: #293a4e;
				outline: 0;
				width: 100%;
				box-sizing: border-box;
			}
			.selectize-control.multi .selectize-input.has-items {
				padding: 6px 8px 3px;
			}
			.selectize-input.disabled,.selectize-input.disabled * {
				cursor: default !important;
			}
			.selectize-input.focus {
				box-shadow: inset 0 1px 2px rgba(0,0,0,0.15);
				webkit-box-shadow: inset 0 1px 2px rgba(0,0,0,0.15);
			}
			.selectize-input.dropdown-active {
				border-radius: 3px 3px 0 0;
				moz-border-radius: 3px 3px 0 0;
				webkit-border-radius: 3px 3px 0 0;
			}
			.selectize-input>* {
				display: inline;
				display: inline-block;
				display: -moz-inline-stack;
				vertical-align: baseline;
				zoom: 1;
			}
			.selectize-control.multi .selectize-input>div {
				background: #F2F5F9;
				border: 0 solid #d0d0d0;
				color: #303030;
				cursor: pointer;
				margin: 0 3px 3px 0;
				padding: 2px 6px;
			}
			.selectize-control.multi .selectize-input>div.active {
				background: #e8e8e8;
				border: 0 solid #cacaca;
				color: #303030;
			}
			.selectize-control.multi .selectize-input.disabled>div,.selectize-control.multi .selectize-input.disabled>div.active {
				background: #fff;
				border: 0 solid #fff;
				color: #7d7d7d;
			}
			.selectize-input>input {
				background: none !important;
				border: 0 none !important;
				box-shadow: none !important;
				display: inline-block !important;
				line-height: inherit !important;
				margin: 0 2px 0 0 !important;
				max-height: none !important;
				max-width: 100% !important;
				min-height: 0 !important;
				padding: 0 !important;
				text-indent: 0 !important;
				webkit-box-shadow: none !important;
				webkit-user-select: auto !important;
			}
			.selectize-input>input::-ms-clear {
				display: none;
			}
			.selectize-input>input:focus {
				outline: none !important;
			}
			.selectize-input::after {
				clear: left;
				content: ' ';
				display: block;
			}
			.selectize-input.dropdown-active::before {
				background: #f0f0f0;
				bottom: 0;
				content: ' ';
				display: block;
				height: 1px;
				left: 0;
				position: absolute;
				right: 0;
			}
			.selectize-dropdown {
				background: #fff;
				border: 1px solid #d0d0d0;
				border-radius: 0 0 3px 3px;
				border-top: 0 none;
				box-shadow: 0 1px 3px rgba(0,0,0,0.1);
				box-sizing: border-box;
				margin: -1px 0 0 0;
				moz-border-radius: 0 0 3px 3px;
				moz-box-sizing: border-box;
				position: absolute;
				webkit-border-radius: 0 0 3px 3px;
				webkit-box-shadow: 0 1px 3px rgba(0,0,0,0.1);
				webkit-box-sizing: border-box;
				z-index: 10;
			}
			.selectize-dropdown [data-selectable] {
				cursor: pointer;
				overflow: hidden;
			}
			.selectize-dropdown [data-selectable] .highlight {
				background: rgba(125,168,208,0.2);
				border-radius: 1px;
				moz-border-radius: 1px;
				webkit-border-radius: 1px;
			}
			.selectize-dropdown [data-selectable],.selectize-dropdown .optgroup-header {
				padding: 5px 8px;
			}
			.selectize-dropdown .optgroup:first-child .optgroup-header {
				border-top: 0 none;
			}
			.selectize-dropdown .optgroup-header {
				background: #fff;
				color: #303030;
				cursor: default;
			}
			.selectize-dropdown .active {
				background-color: #f5fafd;
				color: #495c68;
			}
			.selectize-dropdown .active.create {
				color: #495c68;
			}
			.selectize-dropdown .create {
				color: rgba(48,48,48,0.5);
			}
			.selectize-dropdown-content {
				max-height: 200px;
				overflow-x: hidden;
				padding-bottom: 10px;
				overflow-y: auto;
			}
			.selectize-control.single .selectize-input,.selectize-control.single .selectize-input input {
				cursor: pointer;
			}
			.selectize-control.single .selectize-input.input-active,.selectize-control.single .selectize-input.input-active input {
				cursor: text;
			}
			.selectize-control.single .selectize-input:after {
				border-color: #a4b7cd transparent transparent transparent;
				border-style: solid;
				border-width: 5px 5px 0 5px;
				content: ' ';
				display: block;
				height: 0;
				margin-top: -3px;
				position: absolute;
				right: 15px;
				top: 50%;
				width: 0;
			}
			.selectize-control.single .selectize-input.dropdown-active:after {
				border-color: transparent transparent #a4b7cd transparent;
				border-width: 0 5px 5px 5px;
				margin-top: -4px;
			}
			.selectize-control.rtl.single .selectize-input:after {
				left: 15px;
				right: auto;
			}
			.selectize-control.rtl .selectize-input>input {
				margin: 0 4px 0 -2px !important;
			}
			.selectize-control .selectize-input.disabled {
				background-color: #fafafa;
				opacity: .5;
			}
			/* media upload */
			.wpsl-upload-img{
				display: inline-block;
				width: 50px;
				height: 50px;
				cursor: pointer;
				border: 2px solid #dfdfdf;
				color: #dfdfdf;
				position: relative;
				z-index: 2;
			}
			.wpsl-upload-img img{
				width: 50px;
				height: 50px;
			}
			.wpsl-upload-img:hover{
				border: 2px solid #0085ba;
				color: #0085ba;
			}
			.wpsl-remove-img{
				display: none;
				position: absolute;
				top: 0;
				right: 0;
				width: 16px;
				height: 16px;
				background-color: #ff0000;
				color: #fff;
				z-index: 3;
			}
			.wpsl-remove-img i{
				font-size: 12px !important;
				width: 16px;
				line-height: 1.3;
			}
			.wpsl-upload-img:hover .wpsl-remove-img{
				display: block;
			}
			.wpsl-reset-img{
				cursor: pointer;
				margin-left: 10px;
				color: #dfdfdf;
			}
			.wpsl-reset-img:hover{
				color: #0085ba;
			}
			@media only screen and (min-width: 300px) and (max-width: 640px) {
				.wpsl-opt .opt-tabs{
					width: 50px;
				}
				.wpsl-opt .opt-tabs li{
					font-size: 0;
				}
				.wpsl-opt .content .tab-box{
					width: calc(100% - 30px);
					margin-left: 50px;
				}
			}
			</style>
			<script>
			jQuery(document).ready(function() {
				// tabs
				jQuery('ul.opt-tabs').on('click', 'li:not(.active)', function() {
					jQuery(this).addClass('active').siblings().removeClass('active');
					jQuery(this).closest('.wpsl-opt__content').find('.tab-box').removeClass('tab-active').eq($(this).index()).addClass('tab-active');
					$('body,html').animate({
						scrollTop:0
					}, 250);
				});
				// swither
				$('.switch-label').click(function() {
					var input = $(this).prev('input');
					if ( input.val() === '1' ) {
						input.val('0');
					} else {
						input.val('1');
					}
				});
				// copy to
				jQuery('body').on('click', '.option', function() {
					var $tmp = $('<textarea>');
					$('body').append($tmp);
					$tmp.val($(this).text()).select();
					document.execCommand('copy');
					$tmp.remove();
				});
				// realations
				$('.parent').each(function() {
					var el = $(this).nextUntil('.parent'),
						checkbox = $(':checkbox', this);
					checkbox.change(function() {
						el.toggle(this.checked)
					}).change()
				});
				// validate number format
				$('.validate-number').bind('change keyup input click', function() {
					if (this.value.match(/[^0-9]/g)) {
						this.value = this.value.replace(/[^0-9]/g, '');
					}
				});
				// validate email
				$('.validate-email').blur(function() {
					var pattern = /^([a-z0-9_\.-])+@[a-z0-9-]+\.([a-z]{2,4}\.)?[a-z]{2,4}$/i;
					if(pattern.test($(this).val())){
						$(this).css({'border' : '1px solid #569b44'});
					} else {
						$(this).css({'border' : '1px solid #ff0000'});
					}
				});
				// select
				jQuery('.wpsl-select').selectize({
					plugins: ['remove_button'],
					persist: false,
					create: true,
					render: {
						item: function(data, escape) {
							return '<div>"' + escape(data.text) + '"</div>';
						}
					}
				});
				// mediauploader
				$('.wpsl-upload-img').click(function(){
					var send_attachment_bkp = wp.media.editor.send.attachment;
					var _this = $(this);
					wp.media.editor.send.attachment = function(props, attachment) {
						_this.children().attr('src', attachment.url);
						_this.prev().val(attachment.url); //attachment.id
						wp.media.editor.send.attachment = send_attachment_bkp;
					}
					wp.media.editor.open(_this);
					return false;    
				});
				$('.wpsl-remove-img').click(function(){
					var src = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAGQAAABkAQMAAABKLAcXAAAABlBMVEUAAAC7u7s37rVJAAAAAXRSTlMAQObYZgAAACJJREFUOMtjGAV0BvL/G0YMr/4/CDwY0rzBFJ704o0CWgMAvyaRh+c6m54AAAAASUVORK5CYII=';
					$(this).prev().attr('src', src);
					$(this).parent().prev().val('');
					return false;
				});
				// reset
				$('.wpsl-reset-img').click(function() {
					var url = $(this).data('default');
					$(this).siblings('input').val(url);
					$(this).siblings('.wpsl-upload-img').children('img').attr('src',url);
				});
				// save options
				$('#wpsl-options-form').submit(function() {
					var _this = $(this);
					$.ajax({
						type: 'POST',
						url: ajaxurl,
						data: {
							action: 'save_option',
							str: _this.serialize()
						},
						beforeSend:function(xhr){
							_this.find('button').css({'color':'transparent'}).append('<div class="wpsl-preloader"><img src="'+WPSA.preloader+'" /></div>');
						},
						success: function(msg) {
							_this.find('button').removeAttr('style').find('.wpsl-preloader').remove();
						}
					});
					return false;
				});
			});
			</script>
			<form id="wpsl-options-form" class="wpsl-opt" method="post" enctype="multipart/form-data" action="">
				<div class="wpsl-opt__content">
					<?php echo $this->get_tabs(); ?> 
				</div>
				<div class="wpsl-opt__sidebar">
					<div class="wpsl-btn">
						<?php do_action( 'wpsl_sidebar_content' ); ?>
						<button type="submit" class="wpsl-save-options" value="<?php _e( 'Save', 'wpsl' ) ?>"><span class="icon-save"></span> <?php _e( 'Save', 'wpsl' ) ?></button>
					</div>
				</div>
			</form>
		<?php
	}

}