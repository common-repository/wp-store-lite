<?php
/**
 * Show order form parts
 *
 * This template can be overridden by copying it to yourtheme/wpstore/order/order-form.php.
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

<form class="wpsl-order" action="<?php echo get_permalink( wpsl_opt( 'order_page' ) ); ?>" method="post">

	<div class="wpsl-order__group contact">
		<div class="wpsl-order__group_title"><span><?php _e( 'Leave your contact details for ordering', 'wpsl' ); ?></span></div>
		<div class="wpsl-order__group_box">
			<?php wpsl_get_template( 'order', 'order-contact' ); ?>
		</div>	
	</div>

	<?php if ( wpsl_opt( 'shipping' ) == '1' ) : ?>
	<div class="wpsl-order__group shipping">
		<div class="wpsl-order__group_title"><span><?php _e( 'Shipping details', 'wpsl' ); ?></span></div>
		<div class="wpsl-order__group_box">
			<?php wpsl_get_template( 'order', 'order-shipping' ); ?>
		</div>	
	</div>
	<?php endif; ?>

	<?php if ( wpsl_opt( 'payment_methods' ) == '1' ) : ?>
	<div class="wpsl-order__group payment">
		<div class="wpsl-order__group_title"><span><?php _e( 'Payment details', 'wpsl' ); ?></span></div>
		<div class="wpsl-order__group_box">
			<?php wpsl_get_template( 'order', 'order-pay' ); ?>
		</div>	
	</div>
	<?php endif; ?>
	
	<?php if ( wpsl_opt( 'policy' ) == '1' ) : ?>
	<div class="wpsl-order__group policy">
		<input id="policy" type="checkbox" name="policy" value="1" required="" data-validate="" checked="checked">
		<label for="policy"><?php echo __( 'I have read and agree to the website', 'wpsl' ) . ' <a href="' . get_permalink( wpsl_opt( 'policy_page' ) ) . '" target="_blank"> ' . __( 'terms and conditions', 'wpsl' ) . '</a>'; ?></label>
		<div class="wpsl-notice wpsl-hidden"><?php echo wpsl_opt( 'policy_text' ); ?></div>
	</div>
	<?php endif; ?>
	
	<div class="wpsl-order__row">
		<input id="mode" type="hidden" name="mode" value="checkout" >
		<input type="submit" name="submit" value="<?php _e( 'Checkout order', 'wpsl' ); ?>">
	</div>
	
</form>
