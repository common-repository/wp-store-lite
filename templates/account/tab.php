<?php
/**
 * Displays account
 *
 * This template can be overridden by copying it to yourtheme/wpstore/account/tab.php.
 *
 * @author	wpStore
 * @since	2.7
 */


if ( ! defined( 'ABSPATH' ) ) exit;


$fields = apply_filters( 'wpsl_get_account', $fields = array() );
?>

<div class="wpsl-account">
	<div class="wpsl-account__menu">
	<?php
	foreach ( $fields as $key => $field ) {
		if ( isset( $field['must_logged'] ) && $field['must_logged'] == true && !is_user_logged_in() ) continue;
?>
		<?php if ( $field['fill_in_menu'] == true && $field['fill'] != '' ) : ?>
		<div class="wpsl-account__menu_item <?php echo $field['class']; ?>" data-type="<?php echo $key; ?>" title="<?php echo $field['name']; ?>"><?php wpsl_get_template( 'account', $field['fill'] ); ?></div>
		<?php else: ?>
		<div class="wpsl-account__menu_item <?php echo $field['class']; ?>" data-type="<?php echo $key; ?>" title="<?php echo $field['name']; ?>"><i class="<?php echo $field['icon']; ?>"></i><?php echo $field['name']; ?><?php echo $field['notif']; ?></div>
		<?php endif;
	}
	?>
	</div>
	
	<div class="wpsl-account__tabs">
	<?php
	foreach ( $fields as $key => $field ) {
		if ( isset( $field['must_logged'] ) && $field['must_logged'] == true && !is_user_logged_in() ) continue;

		if ( $field['fill_in_menu'] != true && $field['fill'] != '' ) {
			echo '<div class="wpsl-account__tabs_item ' . $field['class'] . '" data-type="' . $key . '">' . wpsl_get_template_html( 'account', $field['fill'] ) . '</div>';
		}
	}
	?>
	</div>
</div>