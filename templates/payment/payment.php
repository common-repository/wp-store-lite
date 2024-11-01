<?php
/**
 * Show payment form
 *
 * This template can be overridden by copying it to yourtheme/wpstore/payment/payment.php.
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

$payment = new WPSL_Payment();
?>

<div class="wpsl-payment">
	<div class="wpsl-payment__head">
		<h3><?php echo __( 'Your order code is №', 'wpsl' ) . $payment->order_code(); ?></h3>
	</div>
	<div class="wpsl-payment__body">
		<p><?php echo __( 'The service provides the payment system', 'wpsl'); ?>: <?php echo $payment->get_methods()['name']; ?></p>
		<p><?php echo __( 'The amount to be paid on order', 'wpsl' ) . '<strong> №' . $payment->order_code() . '</strong>: ' . $payment->order_amount() . ' ' . wpsl_opt(); ?></p>
		<p><?php echo $payment->get_form(); ?></p>
		<p><?php echo __( 'You will be redirected to the payment gateway', 'wpsl' ); ?></p>
	</div>
	<div class="wpsl-payment__footer"><?php _e( 'Please note: if you waive the purchase, for a refund you have to contact the store.', 'wpsl' ); ?></div>
</div>
