<?php

if ( ! defined( 'ABSPATH' ) ) exit;

if ( is_admin() ) return;

/**
 * Virtual
 */
class WPSL_Virtual{
	
    public function __construct() {
		if ( isset( $_GET['action'] ) && $_GET['action'] == 'get_order' ) {
			add_action( 'init', array( $this, 'check_license' ) );
		}
    }
	
	public function check_license() {
		if ( isset( $_GET['license'] ) ) {
			$json['status'] = 100;
			$orders = get_posts(
				array(
					'numberposts' => -1,
					'post_type'   => 'shop_order',
					'post_status' => 'publish',
				)
			);
			if ( $orders ) {
				foreach ( $orders as $order ) {
					$keys = get_post_meta( $order->ID, 'keys', true );
					if ( $keys == $_GET['license'] ) {
						$json['status'] = 200;
						break;
					} else {
						$json['status'] = 100;
					}
				}
			}
			wp_send_json( $json );
		}
	}
}
new WPSL_Virtual();