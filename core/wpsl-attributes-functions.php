<?php 


if ( ! defined( 'ABSPATH' ) ) exit;


/*
 * Sanitize data of attribute
 *
 * @since 2.7.0
 */
function wpsl_sanitize_attr( $data ) {
	$data['attribute_id']         = (int)wpsl_clean( $data['attribute_id'] );
	$data['attribute_name']       = wpsl_clean( $data['attribute_name'] );
	$data['attribute_label']      = $data['attribute_label'] != '' ? sanitize_title( wpsl_clean( $data['attribute_label'] ) ) : wpsl_clean( $data['attribute_name'] );
	$data['attribute_value']      = wpsl_clean( $data['attribute_value'] );
	$data['attribute_measure']    = wp_slash( wpsl_clean( $data['attribute_measure'] ) );
	$data['attribute_desc']       = wp_slash( esc_textarea( trim( $data['attribute_desc'] ) ) );
	$data['attribute_type']       = wpsl_clean( $data['attribute_type'] );
	$data['attribute_term_id']    = (int)wpsl_clean( $data['attribute_term_id'] );
	$data['attribute_filterable'] = (int)wpsl_clean( $data['attribute_filterable'] );
	$data['attribute_variable']   = (int)wpsl_clean( $data['attribute_variable'] );
	$data['attribute_position']   = (int)wpsl_clean( $data['attribute_position'] );
	$data['attribute_count']      = (int)wpsl_clean( $data['attribute_count'] );
	return $data;
}


/*
 * Update product attribute
 * @param   integer|string  $attr_id  - id of attribute
 *                   array  $array    - Associated array with replacement condition (WHERE) ('column name' => 'new value')
 *
 * @since 2.7.0
 */
function wpsl_update_attr( $attr_id, $array ) {
	$data = new WPSL_Product_Attributes();
	$array = wpsl_sanitize_attr( $array );
	if ( $array ) {
		foreach( $array as $column_name => $new_value ) {
			$result[] = $data->update( $attr_id, $column_name, $new_value );
		}
		return $result;
	}
}


/*
 * Update single parameter of product attribute
 * @param   integer|string  $attr_id  - id of attribute
 *                   array  $array    - Associated array with replacement condition (WHERE) ('column name' => 'new value')
 *
 * @since 2.7.0
 */
function wpsl_update_attr_param( $attr_id, $array ) {
	$data = new WPSL_Product_Attributes();
	if ( is_array( $array ) ) {
		foreach( $array as $column_name => $new_value ) {
			$result[] = $data->update( $attr_id, $column_name, $new_value );
		}
		return $result;
	}
}


/*
 * Set product attribute
 *
 * @since 2.7.0
 */
function wpsl_insert_attr( $data, $format = '' ) {
	$defaults = array(
		'attribute_id'         => '',
		'attribute_name'       => '',
		'attribute_label'      => '',
		'attribute_value'      => '',
		'attribute_measure'    => '',
		'attribute_desc'       => '',
		'attribute_type'       => 'checkbox', // checkbox, select, color or image
		'attribute_term_id'    => '',
		'attribute_filterable' => '1',
		'attribute_variable'   => '0',
		'attribute_position'   => '',
		'attribute_count'      => '',
	);
	$data = wp_parse_args( $data, $defaults );
	if ( !isset( $format ) ) {
		$format = array(
			'%d',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%d',
			'%d',
			'%d',
			'%d',
			'%d',
		);
	}
	
	/**
	 * If the attribute id is defined - update it, if not - add to the database
	 */
	if ( isset( $data['attribute_id'] ) && $data['attribute_id'] != '' ) {
		$id = (int) $data['attribute_id'];
		// return how many rows were processed 
		return wpsl_update_attr( $id, $data );
	} else {
		$attr = new WPSL_Product_Attributes();
		$data = wpsl_sanitize_attr( $data );
		// return id of new attribute
		return $attr->set( $data, $format );
	}
}


/*
 * Get all attributes. Work with object cache (30-60 times faster)
 *
 * @since 2.7.0
 */
function wpsl_get_atts( $args = null, $cache = true ) {
	
	$cache_key = 'wpsl_attributes';
	
	if ( $cache = wp_cache_get( $cache_key ) && $cache == true ) {
		return $cache;
	} else {
		$where = '';
		$defaults = array(
			'attribute_id'         => '',
			'attribute_name'       => '',
			'attribute_label'      => '',
			'attribute_value'      => '',
			'attribute_measure'    => '',
			'attribute_desc'       => '',
			'attribute_type'       => '',
			'attribute_term_id'    => '',
			'attribute_filterable' => '',
			'attribute_variable'   => '',
			'attribute_position'   => '',
			'attribute_count'      => '',
		);
		$array = wp_parse_args( $args, $defaults );
		$i = 0;
		foreach ( $array as $key => $val ) {
			if ( $val != '' ) {
				if ( is_string( $val ) ) {
					$val = "'$val'";
				}
				$where .= $i == 0 ? "$key = $val" : " AND $key = $val";
				$i++;
			}
		}
		$attr = new WPSL_Product_Attributes();
		$attributes = $attr->get( $where );
		
		// set cache
		wp_cache_set( $cache_key, $attributes );
		return $attributes;
	}
}


/*
 * Sort attributes by any characteristic.
 *
 * @param          array       $array - array of characteristics
 *                 string      $param - parameter by which the sorting is performed (can be: attribute_id, attribute_name, attribute_term_id, attribute_position, etc... )
 *
 * @retun          array       Returns an array of objects sorted by the passed parameters
 *
 * @since 2.7.0
 */
function wpsl_sort_atts_by( $array, $param = 'attribute_name' ) {
	uasort( $array, function ( $a, $b ) use ( $param ) {
			return ( $a->$param > $b->$param );
		}
	);
	return $array;
}


/*
 * Get single attribute by id
 *
 * @param       required   int      $attr_id - id of attribute
 * @retun                  object
 *
 * @since 2.7.0
 */
function wpsl_get_attr_by_id( $attr_id ) {
	$atts = wpsl_get_atts();
	if ( is_array( $atts ) ) {
		foreach( $atts as $attr ) {
			if ( $attr->attribute_id == $attr_id ) {
				return $attr;
			}
		}
	}
}


/*
 * Get all attributes from product category
 *
 * @param       required   int      $term_id - id of product category
 * @retun                  object
 *
 * @since 2.7.0
 */
function wpsl_get_atts_by_term_id( $term_id ) {
	$atts = wpsl_get_atts( null,  false );
	$obj = array();
	if ( is_array( $atts ) ) {
		foreach( $atts as $attr ) {
			if ( $attr->attribute_term_id == $term_id ) {
				$obj[] = $attr;
			}
		}
		return $obj;
	}
}


/*
 * Get single attribute by slug
 *
 * @param       required   string   $slug - name of slug
 * @retun                  object
 *
 * @since 2.7.0
 */
function wpsl_get_attr_by_slug( $slug ) {
	$atts = wpsl_get_atts();
	if ( is_array( $atts ) ) {
		foreach( $atts as $attr ) {
			if ( $attr->attribute_label == $slug ) {
				return $attr;
			}
		}
	}
}


/*
 * Check if there is an attribute by name
 *
 * @param   string $name - attribute name
 *
 * @since 2.7.0
 */
function wpsl_get_attr_by_name( $name, $cache = true ) {
	$atts = wpsl_get_atts( null, $cache );
	if ( is_array( $atts ) ) {
		foreach( $atts as $attr ) {
			if ( $attr->attribute_name === $name ) {
				return $attr;
			}
		}
	}
}


/*
 * Remove attribute by ID or slug
 *
 * @param   integer|string  $data - id or slug of attribute
 * @retun              int  Number of rows deleted, or 0 if nothing is deleted.
 *
 * @since 2.7.0
 */
function wpsl_remove_attr( $data ) {
	if ( is_int( $data ) ) {
		$where = array( 'attribute_id' => $data );
	}
	if ( is_string( $data ) ) {
		$where = array( 'attribute_label' => $data );
	}
	$attr = new WPSL_Product_Attributes();
    return $attr->remove( $where );
}