<?php
/**
 * Plugin Name: wpStore
 * Plugin URI: https://codyshop.ru
 * Description: wpStore is a new ecommerce solution. Easy and fast, with support for payment gateways and different types of products.
 * Version: 2.9.5
 * Author: Yan Alexandrov
 * Author URI: https://codyshop.ru
 * Text Domain: wpsl
 * Requires at least: 5.2
 * Requires PHP: 7.1
 * License:     GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */


/**
 * Start of session
 */
add_action( 'wpsl_start', 'wpsl_do_session_start' );
function wpsl_do_session_start() {
	if ( !session_id() ) {
		ini_set( 'session.gc_maxlifetime', 604800 ); // 1 week
		@session_start();
	}
}


/**
 * Set constants
 */
define( 'WPSL_ADD', 'add' );
define( 'WPSL_UPDATE', 'update' );
define( 'WPSL_DELETE', 'delete' );
define( 'WPSL_CHECKOUT', 'checkout' );
define( 'WPSL_MODE', 'mode' );
define( 'WPSL_VERSION', '2.9.4' );
define( 'WPSL_DIR', dirname( __FILE__ ) . '/' );   // Plugin folder DIR, return: /home/login/domains/your-website.com/public_html/wp-content/plugins/wp-store-lite/
define( 'WPSL_URL', plugins_url( '', __FILE__ ) ); // Plugin folder URL, return: http://your-website.com/wp-content/plugins/wp-store-lite
define( 'WPSL_TEMPLATE', WPSL_DIR . 'templates/' );


/**
 * Localization
 */
add_action( 'plugins_loaded', 'wpsl_init_plugin' );
function wpsl_init_plugin() {
	load_plugin_textdomain( 'wpsl', false, dirname( plugin_basename( __FILE__ ) ) . '/assets/lang/' );
}


/**
 * Create required roles, database tabels and pages
 */
include_once dirname( __FILE__ ) . '/core/class-wpsl-install.php';
register_activation_hook( __FILE__, 'wpsl_init_plugin' );
register_activation_hook( __FILE__, array( 'WPSL_Install', 'wpsl_edit_roles' ) );
register_activation_hook( __FILE__, array( 'WPSL_Install', 'wpsl_add_roles' ) );
register_activation_hook( __FILE__, array( 'WPSL_Install', 'wpsl_add_pages' ) );
register_activation_hook( __FILE__, array( 'WPSL_Install', 'wpsl_add_table' ) );


/**
 * Includes
 */
require( plugin_dir_path( __FILE__ ) . 'options/wpsl-options.php' );

require( plugin_dir_path( __FILE__ ) . 'core/wpsl-general-functions.php' );
require( plugin_dir_path( __FILE__ ) . 'core/wpsl-attributes-functions.php' );
require( plugin_dir_path( __FILE__ ) . 'core/wpsl-currency-functions.php' );
require( plugin_dir_path( __FILE__ ) . 'core/wpsl-seo.php' );

require( plugin_dir_path( __FILE__ ) . 'core/class-wpsl-sms.php' );
require( plugin_dir_path( __FILE__ ) . 'core/class-wpsl-order.php' );
require( plugin_dir_path( __FILE__ ) . 'core/class-wpsl-cart.php' );
require( plugin_dir_path( __FILE__ ) . 'core/class-wpsl-wishlist.php' );
require( plugin_dir_path( __FILE__ ) . 'core/class-wpsl-delivery.php' );
require( plugin_dir_path( __FILE__ ) . 'core/class-wpsl-payment.php' );
require( plugin_dir_path( __FILE__ ) . 'core/class-wpsl-product-attributes.php' );
require( plugin_dir_path( __FILE__ ) . 'core/class-wpsl-product-attributes-common.php' );
require( plugin_dir_path( __FILE__ ) . 'core/class-wpsl-product-attributes-single.php' );
require( plugin_dir_path( __FILE__ ) . 'core/class-wpsl-product-variations.php' );
require( plugin_dir_path( __FILE__ ) . 'core/class-wpsl-include-js-css.php' );
require( plugin_dir_path( __FILE__ ) . 'core/class-wpsl-constructor-email.php' );
require( plugin_dir_path( __FILE__ ) . 'core/class-wpsl-constructor-forms.php' );
require( plugin_dir_path( __FILE__ ) . 'core/class-wpsl-constructor-metaboxes.php' );
require( plugin_dir_path( __FILE__ ) . 'core/class-wpsl-featured-images.php' );
require( plugin_dir_path( __FILE__ ) . 'core/class-wpsl-terms-images.php' );
require( plugin_dir_path( __FILE__ ) . 'core/class-wpsl-terms-metaboxes.php' );
require( plugin_dir_path( __FILE__ ) . 'core/class-wpsl-terms-sorting.php' );
require( plugin_dir_path( __FILE__ ) . 'core/class-wpsl-shortcodes.php' );
require( plugin_dir_path( __FILE__ ) . 'core/class-wpsl-discount.php' );
require( plugin_dir_path( __FILE__ ) . 'core/class-wpsl-virtual.php' );

require( plugin_dir_path( __FILE__ ) . 'core/wpsl-template-actions.php' );
require( plugin_dir_path( __FILE__ ) . 'core/wpsl-template-functions.php' );
require( plugin_dir_path( __FILE__ ) . 'core/wpsl-template-filters.php' );

require( plugin_dir_path( __FILE__ ) . 'core/widgets/class-wpsl-widget-filter.php' );
require( plugin_dir_path( __FILE__ ) . 'core/widgets/class-wpsl-widget-payment.php' );
require( plugin_dir_path( __FILE__ ) . 'core/widgets/class-wpsl-widget-search.php' );
require( plugin_dir_path( __FILE__ ) . 'core/widgets/class-wpsl-widget-category.php' );

require( plugin_dir_path( __FILE__ ) . 'core/class-wpsl-themes-support.php' );

require( plugin_dir_path( __FILE__ ) . 'admin/wpsl-post-types-adding.php' );
require( plugin_dir_path( __FILE__ ) . 'admin/wpsl-post-types-management.php' );
require( plugin_dir_path( __FILE__ ) . 'admin/wpsl-post-types-metaboxes.php' );
require( plugin_dir_path( __FILE__ ) . 'admin/wpsl-duplicate-product.php' );
require( plugin_dir_path( __FILE__ ) . 'admin/wpsl-media-library.php' );
require( plugin_dir_path( __FILE__ ) . 'admin/wpsl-csv-exporter.php' );
require( plugin_dir_path( __FILE__ ) . 'admin/wpsl-csv-importer.php' );
require( plugin_dir_path( __FILE__ ) . 'admin/wpsl-admin.php' );
require( plugin_dir_path( __FILE__ ) . 'admin/wpsl-add-items-in-menu.php' );
require( plugin_dir_path( __FILE__ ) . 'admin/wpsl-permalinks.php' );

require( plugin_dir_path( __FILE__ ) . 'ajax/class-wpsl-ajax-admin.php' );
require( plugin_dir_path( __FILE__ ) . 'ajax/class-wpsl-ajax-frontend.php' );

require( plugin_dir_path( __FILE__ ) . 'addons/wpsl-addons.php' );


/**
 * Combine js
 */
add_action( 'wp_enqueue_scripts', 'wpsl_combine_scripts', 9999999 );
function wpsl_combine_scripts() {
	if ( wpsl_opt( 'combinejs' ) == '1' ) {
		global $wp_scripts;
		/*
		 * #1. Reorder the handles based on its dependency,
		 * The result will be saved in the to_do property ( $wp_scripts->to_do )
		*/
		$wp_scripts->all_deps( $wp_scripts->queue );

		// Располагаем новый js файл по пути: \wp-content\theme\combine-script.js
		$merged_file_location = get_stylesheet_directory() . DIRECTORY_SEPARATOR . 'combine-script.js';

		$merged_script = $inc = '';

		// Запускаем цикл, который последовательно сохраняет их в переменную $merged_script
		foreach( $wp_scripts->to_do as $handle ) {
			/*
			 * Очищаем скрипт от версии, например, вместо wp-content/themes/combine-script.js?v=1.2.4
			 * будет wp-content/themes/combine-script.js
			*/
			$src = strtok( $wp_scripts->registered[$handle]->src, '?' );

			/**
			 * Объединяем js файлы
			 * Учитываем протокол http/https
			 */
			if ( strpos( $src, 'http' ) !== false ) {
				// Получаем урл сайта
				$site_url = site_url();

				if ( strpos( $src, $site_url ) !== false )
					$js_file_path = str_replace( $site_url, '', $src );
				else
					$js_file_path = $src;
				$js_file_path = ltrim( $js_file_path, '/' );
			} else {
				$js_file_path = ltrim( $src, '/' );
			}

			/**
			 * Проверим, существует ли файл после слияния
			 * Проверяем wp_localize_script
			 */
			if ( file_exists( $js_file_path ) ) {
				$localize = '';
				if ( key_exists( 'data', $wp_scripts->registered[$handle]->extra ) ) {
					$localize = $wp_scripts->registered[$handle]->extra['data'] . ';';
					//$localize = @$obj->extra['data'] . ';';
				}
				$merged_script .= $localize . file_get_contents( $js_file_path ) . ';';
			}
		}

		// записываем скрипты в файл
		file_put_contents ( $merged_file_location , $merged_script );

		// #4. Подключаем созданный скрипт в подвал
		wp_enqueue_script( 'combine-script', get_stylesheet_directory_uri() . '/combine-script.js', '', '', true );

		// 5. Отключаем все файлы, которые были слиты
		//print_r( $wp_scripts->registered );
		foreach ( $wp_scripts->registered as $k=>$v ) {
			foreach ( $wp_scripts->to_do as $script ) {
				if ( $k == $script ) {
					//echo 'название скрипта: ' . $script . '<br>';
					//print_r( $v->extra );
					$scr = $v->extra;
					if ( $scr && isset( $scr['data'] ) ) {
						$allajaxs[] = $scr['data'];
					}
				}
			}
		}

		// теперь можно вернуть на место ajax скрипты
		$allajaxs = array_diff( $allajaxs, array( '' ) );
		/* echo '<pre>';
		print_r( $allajaxs );
		echo '</pre>'; */
		$inc = '<script type="text/javascript">';
		$inc .= '/* <![CDATA[ */ ';
		foreach ( $allajaxs as $key => $value ) {
			$inc .= $value;
		}
		$inc .= ' /* ]]> */';
		$inc .= '</script>';
		echo $inc;


		//print_r( $wp_scripts->to_do );
		foreach( $wp_scripts->to_do as $handle ) {
			wp_deregister_script( $handle );
		}
	}
}
