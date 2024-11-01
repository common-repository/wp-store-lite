<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if( is_admin() ) return;

/**
 * Override template
 *
 * @param string $name Template name.
 */
function wpsl_override_template( $name ) {
	$template = '';
	
	// locate_template() вернет путь до файла, если дочер. или родит. тема имеет такой файл
	if( file_exists( $overridden_template = locate_template( "{$name}.php" ) ) ) {
		$template = $overridden_template;
	} else {
		// если файл не найден в теме или доч. теме, загружаем файл из каталога 'templates' плагина
		$template = WPSL_DIR . "templates/{$name}.php";
	}

	// Allow 3rd party plugins to filter template file from their plugin.
	$template = apply_filters( 'wpsl_override_template', $template, $name );
	
	return $template;
}


/**
 * Get template part (for templates like the shop-loop)
 *
 * @param mixed  $dir  Directory name
 * @param string $name Template name (default: '').
 */
function wpsl_get_template( $dir, $name = '' ) {
	$template = '';
	
	// locate_template() вернет путь до файла, если дочер. или родит. тема имеет такой файл
	if ( $overridden_template = locate_template( "wpstore/{$dir}/{$name}.php" ) ) {
		$template = $overridden_template;
	} else {
		// если файл не найден в теме или доч. теме, загружаем файл из каталога 'templates' плагина
		$template = WPSL_DIR . "templates/{$dir}/{$name}.php";
	}

	// Allow 3rd party plugins to filter template file from their plugin.
	$template = apply_filters( 'wpsl_get_template', $template, $dir, $name );
	
	if ( $template ) {
		load_template( $template, false );
	}
}


/**
 * Like wpsl_get_template, but returns the HTML instead of outputting.
 *
 * @param mixed  $dir  Directory name
 * @param string $name Template name (default: '').
 */
function wpsl_get_template_html( $dir, $name = '' ) {
	ob_start();
	wpsl_get_template( $dir, $name );
	return ob_get_clean();
}


/**
 * Loop classes
 */
if ( ! function_exists( 'wpsl_loop_class' ) ) {
	function wpsl_loop_class() {
		echo apply_filters( 'wpsl_loop_class', 'xl-' . wpsl_opt( 'productcount', 4 ) . ' lg-4 md-4 sm-3 xs-2' );
	}
}


/**
 * Template pages
 */
if ( ! function_exists( 'wpsl_content' ) ) {

	/**
	 * Output wpStore content.
	 *
	 * This function is only used in the optional 'wpstore.php' template.
	 * which people can add to their themes to add basic wpstore support.
	 * without hooks or modifying core templates.
	 *
	 * For the plugin to work properly, the container with the list of products must contain the "wpsl-loop" id
	 */
	function wpsl_content() {

		if ( is_singular( 'product' ) ) {

			while ( have_posts() ) :
				the_post();
				wpsl_get_template( 'single', 'product' );
			endwhile;

		} else {
				
			wpsl_get_template( 'loop', 'product-sorting' );
			
			do_action( 'wpsl_before_shop_loop' ); ?>

			<div id="wpsl-loop" class="wpsl wpsl-loop <?php wpsl_loop_class(); ?>">
					
			<?php
				$products = new WP_Query( wpsl_query_args() );
				if ( $products->have_posts() ) {
					while ( $products->have_posts() ) {
						$products->the_post();
						wpsl_get_template( 'loop', 'product' );
					}
				} else {
					wpsl_get_template( 'loop', 'no-products-found' );
				}
				
				wp_reset_postdata();
				
				set_query_var( 'products', $products );
				
				wpsl_get_template( 'loop', 'product-pagi' );

				wp_reset_query();
				
			?>
				
			</div>
			
			<?php do_action( 'wpsl_after_shop_loop' );

		}
	}
}