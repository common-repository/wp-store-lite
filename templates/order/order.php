<?php
/**
 * Show order form
 *
 * This template can be overridden by copying it to yourtheme/wpstore/order/order.php.
 *
 * Please change the contents of the file only if you know what you are doing.
 * For customization, use other files from the same directory.
 *
 * @author  wpStore
 * @version 2.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


$orderForm = new WPSL_Order();
echo $orderForm->getHTML();
