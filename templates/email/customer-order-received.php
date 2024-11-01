<?php
/**
 * The contents of the letter to the customer on receipt of order
 *
 * This template can be overridden by copying it to yourtheme/wpstore/email/customer-order-received.php.
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

if ( !$wpsl_order_id ) return;

$email = new WPSL_Email();
?>

<p><?php printf( __( 'List of products in order №%s:', 'wpsl' ), get_the_title( $wpsl_order_id ) ); ?></p>
<p><?php echo $email->get_order( $wpsl_order_id ); ?></p>
<p><?php 
	if ( $methods = $email->get_active_payment( $wpsl_order_id ) ) {
		_e( 'Links for payment', 'wpsl' ) . ': ';
		foreach( $methods as $k => $method ) {
			echo '<a href="' . get_permalink( wpsl_opt( 'payment_page' ) ) . '?payment=process&order_id=' . $wpsl_order_id . '" target="_blank">' . $method['name'] . '</a> ';
		}
	}
	?>
</p>