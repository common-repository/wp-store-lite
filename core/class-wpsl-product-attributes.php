<?php
if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Class for get, set or update product attribute
 */
class WPSL_Product_Attributes {
	
	/**
	 * Get prefix database
	 */
    function get_table_name() {
		global $wpdb;
		$table_name = $wpdb->get_blog_prefix() . 'wpsl_attributes';
		return $table_name;
    }
	
	/**
	 * Create slug
	 */
    function create_slug( $data ) {
		global $wpdb;
		/**
		 * Check duplicate of slug
		 */
		$table_name = $this->get_table_name();
		if ( isset( $data['attribute_label'] ) && $data['attribute_label'] != '' ) {
			$current_slug = $data['attribute_label'];
		} else {
			$current_slug = _truncate_post_slug( sanitize_title( $data['attribute_name'] ) );
		}
		// if there is such same slug, then replace him
		$query = $wpdb->prepare( "SELECT attribute_label FROM $table_name WHERE attribute_label = %s", $current_slug );
		if ( $wpdb->get_var( $query ) ) {
			$num = 2;
			do {
				$alt_slug = $current_slug . "-$num";
				$num++;
				$slug_check = $wpdb->get_var( $wpdb->prepare( "SELECT attribute_label FROM $table_name WHERE attribute_label = %s", $alt_slug ) );
			} while ( $slug_check );
			$data['attribute_label'] = $alt_slug;
		} else {
			$data['attribute_label'] = $current_slug;
		}
		return $data;
    }

	/**
	 * Get attribute
	 *
	 * @param   array  $where - sql request
	 * @return  object value of attribute
	 */
    function get( $where ) {
		global $wpdb;
		$table_name = $this->get_table_name();
		if ( $where ) {
			$attr = $wpdb->get_results( "SELECT * FROM $table_name WHERE $where" );
		} else {
			$attr = $wpdb->get_results( "SELECT * FROM $table_name" );
		}
		return $attr;
    }

	/**
	 * Set attribute
	 * 
	 * @return  string  - value of attribute
	 */
    function set( $data, $format ) {
		global $wpdb;
		$data = $this->create_slug( $data );
		
		/**
		 * Insert attribute
		 */
		$insert = $wpdb->insert( $this->get_table_name(), $data, $format );
		if ( $insert == true ) {
			return $wpdb->insert_id;
		}
    }

	/**
	 * Update attribute
	 * 
	 * @return  string  - value of attribute
	 */
    function update( $attr_id, $column_name, $new_value ) {
		global $wpdb;
		return $wpdb->update( 
			$this->get_table_name(),
			array( $column_name   => $new_value ),   // replacement
			array( 'attribute_id' => $attr_id ),    // where
			array( '%s' ),
			array( '%d' ) // format for where
		);
    }

	/**
	 * Delete attribute
	 * 
	 * @return  string  - value of attribute
	 */
    function remove( $where ) {
		global $wpdb;
		return $wpdb->delete( $this->get_table_name(), $where );
    }
	
}