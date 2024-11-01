<?php
/**
 * The contents of the letter to the administrators on receipt of order
 *
 * This template can be overridden by copying it to yourtheme/wpstore/email/admin-order-received.php.
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

<p><?php printf( __( 'New order with number №%s', 'wpsl' ), get_the_title( $wpsl_order_id ) ); ?></p>
<p><a href="<?php echo get_site_url( null, '/wp-admin/post.php?post=' . $wpsl_order_id . '&action=edit' ); ?>" target="_blank"><?php _e( 'View details', 'wpsl' ); ?></a></p>
<p><?php _e( 'List of products in order', 'wpsl' ); ?>:</p>
<?php echo $email->get_order( $wpsl_order_id ); ?>