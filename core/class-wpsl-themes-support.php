<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * 3-rd part themes support.
 *
 * @since   2.8.0
 */
class WPSL_Themes_Support {

	function __construct() {
		
		if ( current_theme_supports( 'wpstore' ) ) return;
		
		$theme = wp_get_theme();
		
		$themes = array(
			'twentyfifteen',
			'twentysixteen',
			'twentyseventeen',
			'twentynineteen',
			'twentytwelve',
		);
		if ( in_array( $theme->get( 'TextDomain' ), $themes ) ) {
			add_filter( 'template_include', array( $this, 'theme_support' ), 99, 1 );
		}
	}

	/**
	 * Include archive page Twenty Twelve theme.
	 */
	public function theme_support( $template ) {
		
		$theme = wp_get_theme();
		
		if ( is_tax( 'product_cat' ) || is_tax( 'product_tag' ) || is_post_type_archive( 'product' ) ) {
			$template = WPSL_DIR . 'core/theme-support/' . $theme->get( 'TextDomain' ) . '.php';
		}
		return $template;
	}
}

new WPSL_Themes_Support();
