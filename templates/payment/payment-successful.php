<?php
/**
 * Output content upon successful payment of the order
 *
 * This template can be overridden by copying it to yourtheme/wpstore/payment/payment-successful.php.
 *
 * HOWEVER, on occasion wpStore will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @author  wpStore
 * @version 2.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<p><?php printf( __( 'The order with №%s has been successfully paid', 'wpsl' ), get_the_title( $wpsl_order_id ) ); ?></p>
