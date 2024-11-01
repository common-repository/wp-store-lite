<?php
/**
 * Shipping
 */
class WPSL_Shipping{
    
	public $action = '';

	function __construct( $args = false ) {
		$this->init_properties( $args );
	}

	function init_properties( $args ){
		$properties = get_class_vars( get_class( $this ) );
		foreach ( $properties as $name=>$val ){
			if( isset( $args[$name] ) ) $this->$name = $args[$name];
		}
	}

	function get_delivery() {
		$cache_key = 'wpsl_shipping';
		if ( $cache = wp_cache_get( $cache_key ) ) {
			return $cache;
		} else {
			$shipping = get_posts(
				array(
					'post_type'   => array( 'delivery' ),
					'numberposts' => -1,
					'post_status' => 'publish',
				)
			);
			wp_cache_set( $cache_key, $shipping );
			return $shipping;
		}
	}

	function get_shipping() {
		$list = array();
		if ( $deliverys = $this->get_delivery() ) {
			$list[] = __( 'Shipping type', 'wpsl' );
			foreach ( $deliverys as $del ) {
				$list[$del->ID] = $del->post_title . ' - ' . get_post_meta( $del->ID, 'delivery_price', true ) . wpsl_opt();
			}
		}
		return $list;
	}

	function get_delivery_list() {
		if ( $deliverys = $this->get_delivery() ) {
			foreach ( $deliverys as $del ) {
				$delivery_list[$del->ID] = array(
					'name'   => $del->post_title . ' - ' . get_post_meta( $del->ID, 'delivery_price', true ) . wpsl_opt(),
					'img'    => wpsl_get_thumbnail_url( $del->ID ),
					'desc'   => $del->post_content,
				);
			}
		}
		return $delivery_list;
	}
	
}