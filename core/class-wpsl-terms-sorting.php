<?php
/**
 * wpStore
 *
 * Drug and drop sorting terms of products
 * @since	2.4.0
 */


if ( ! defined( 'ABSPATH' ) ) exit;


if ( !is_admin() ) return;


class WPSL_Terms_Sorting {

	/*
	*	Main Constructor
	*/
	function __construct() {
		// admin init
		add_action( 'admin_head', array( $this, 'custom_tax_order_admin_init' ) );
		// front end init
		add_action( 'init', array( $this, 'custom_tax_order_front_end_init' ) );
		// handle the AJAX request
		add_action( 'wp_ajax_update_taxonomy_order', array( $this, 'handle_ajax_request' ) );
	}

	/*
	*	Init and load the files as needed
	*	@since 0.1
	*/
	public function custom_tax_order_admin_init() {
		/* Admin Side Re-Order of Hierarchical Taxonomies */
		if( is_admin() ) {
			// get global screen data
			$screen = get_current_screen();
			// confirm $screen and $screen->base is set
			if( isset( $screen ) && isset( $screen->base ) ) {
				// confirm we're on the edit-tags page
				if( $screen->base == 'edit-tags' ) {
					// ensuere that our terms have a `tax_position` value set, so they display properly
					$this->ensure_terms_have_tax_position_value( $screen );
					// retreive a list of enabled taxonomies
					$taxonomies = self::get_registered_taxonomies();
					// confirm that the tax_position arg is set and no orderby param has been set
					if( ! isset( $_GET['orderby'] ) && $this->is_taxonomy_position_enabled( $screen->taxonomy ) ) {
						// enqueue our scripts/styles
						$this->enqueue_scripts_and_styles();
						// ensure post types have tax_position set
						add_filter( 'admin_init', array( $this, 'wpsl_ensure_tax_position_set' ) );
						// re-order the posts
						add_filter( 'terms_clauses', array( $this, 'alter_tax_order' ), 10, 3 );
					}
				}
			}
		}
	}

	/*
	*	Properly order the taxonomies on the front end
	*	@since 0.1
	*/
	public function custom_tax_order_front_end_init() {
		/* Front End Re-Order of Hierarchical Taxonomies */
		if( ! is_admin() ) {
			add_filter( 'terms_clauses', array( $this, 'alter_tax_order' ), 10, 3 );
		}
	}

	/*
	*	Enqueue any scripts/styles we need
	*	@since 0.1
	*/
	public function enqueue_scripts_and_styles() {
		// enqueue jquery ui drag and drop
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-sortable' );
	}

	/*
	*	Make sure each taxonomy has some tax_position set in term meta
	*	if not, assign a value to 'tax_position' in wp_termmeta
	*	@since 0.1
	*/
	public function ensure_terms_have_tax_position_value( $screen ) {
		if( isset( $screen ) && isset( $screen->taxonomy ) ) {
			$terms = get_terms( $screen->taxonomy, array( 'hide_empty' => false ) );
			$x = 1;
			foreach( $terms as $term ) {
				if( ! get_term_meta( $term->term_id, 'tax_position', true ) ) {
					update_term_meta( $term->term_id, 'tax_position', $x );
					$x++;
				}
			}
		}
	}

	/*
	 *	Изменение порядка таксономий на основе значения tax_position
	 */
	public function alter_tax_order( $pieces, $taxonomies, $args ) {
		foreach( $taxonomies as $taxonomy ) {
			// confirm the tax is set to hierarchical -- else do not allow sorting
			if( $this->is_taxonomy_position_enabled( $taxonomy ) ) {
				global $wpdb;

				$join_statement = " LEFT JOIN $wpdb->termmeta AS term_meta ON t.term_id = term_meta.term_id AND term_meta.meta_key = 'tax_position'";

				if ( ! $this->does_substring_exist( $pieces['join'], $join_statement ) ) {
					$pieces['join'] .= $join_statement;
				}
				$pieces['orderby'] = "ORDER BY CAST( term_meta.meta_value AS UNSIGNED )";
			}
		}
		return $pieces;
	}

	/**
	* Check if a substring exists inside a string
	*
	* @since 1.2.3
	*
	* @param string | $string	 | The main string ( haystack ) we're searching in
	* @param string | $substring | The substring we're searching for
	* @return bool  | T || F 	 | True if substring exists, else false
	*/
	protected function does_substring_exist( $string, $substring ) {

		// Check if the $substring exists already in the $string
		return ( strstr( $string, $substring ) === false ) ? false : true;
	}

	/*
	*	Handle The AJAX Request
	*	@since 0.1
	*/
	public function handle_ajax_request() {
		$array_data = $_POST['updated_array'];
		foreach( $array_data as $taxonomy_data ) {
			update_term_meta( $taxonomy_data[0], 'tax_position', ( int ) ( $taxonomy_data[1] + 1 ) );
		}
		wp_die();
		exit;
	}

	/**
	*	Helper function to confirm 'tax_position' is set to true ( allowing sorting of taxonomies )
	*	eg: For an example on how to enable tax_position/sorting for taxonomies, please see:
	*	@since 0.1
	*	@return true/false
	*/
	public function is_taxonomy_position_enabled( $taxonomy_name ) {
		// Confirm a taxonomy name was passed in
		if( ! $taxonomy_name ) {
			return false;
		}
		$tax_object = get_taxonomy( $taxonomy_name );
		if( $tax_object && is_object( $tax_object ) ) {
			// get saved taxonomies
			$enabled_taxonomies = array();
			$enabled_taxonomies = array( 
				'0' => 'product_cat',
				'1' => 'wpsl_status'
			 );
			// if 'tax_position' => true || is set on the settings page
			if( isset( $tax_object->tax_position ) && $tax_object->tax_position || in_array( $taxonomy_name, $enabled_taxonomies ) ) {
				return true;
			} else {
				return false;
			}
		}
		return false;
	}

	/**
	*	Helper function to return an array of enabled drag and drop taxonomies
	*	@since 0.1
	*	@returns array of enabled taxonomes, or empty if none enabled
	*/
	public static function get_registered_taxonomies() {
		// get ALL taxonomies on site
		$registered_taxonomies = get_taxonomies();
		// Array of taxonomies we want to exclude from being displayed in our options
		$ignored_taxonomies = apply_filters( 'wpsl_simple_taxonomy_ordering_ignored_taxonomies', array( 
			'nav_menu',
			'link_category',
			'post_format'
		 ) );
		// Easy Digital Downloads taxonomies
		$ignored_taxonomies = array_merge( $ignored_taxonomies, apply_filters( 'wpsl_simple_taxonomy_ordering_ignored_edd_taxonomies', array( 
			'edd_log_type',
		 ) ) );
		// Strip Woocommerce product attributes
		foreach( $registered_taxonomies as $registered_tax ) {
			// strip all woocommerce product attributes
			if ( strpos( $registered_tax, 'pa_' ) !== false ) {
				$location = array_search( $registered_tax, $registered_taxonomies );
				unset( $registered_taxonomies[$location] );
			}
		}
		// Strip Duplicate Taxonomies
		foreach( $ignored_taxonomies as $ignored_tax ) {
			if( in_array( $ignored_tax, $registered_taxonomies ) ) {
				$location = array_search( $ignored_tax, $registered_taxonomies );
				if( $location ) {
					unset( $registered_taxonomies[$location] );
				}
			}
		}
		// return the taxonomies
		return $registered_taxonomies;
	}

}
new WPSL_Terms_Sorting;