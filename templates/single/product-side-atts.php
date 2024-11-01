<?php
/**
 * Single product attributes list
 *
 * This template can be overridden by copying it to yourtheme/wpstore/single/product-side-atts.php.
 *
 * @author  wpStore
 * @version 2.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( wpsl_opt( 'atts_to_tab' ) != true && wpsl_is_has_atts() ) :
?>

<div class="wpsl-sep">
	<span><?php _e( 'Characteristics', 'wpsl' ); ?></span>
</div>

<?php

endif;

$default_atts = array(
	'_sku'    => wpsl_post( '_sku' ),
	'_length' => wpsl_post( '_length' ),
	'_width'  => wpsl_post( '_width' ),
	'_height' => wpsl_post( '_height' ),
	'_weight' => wpsl_post( '_weight' ),
	'_stock'  => wpsl_post( '_stock_status' ),
);

// sku
if ( $default_atts['_sku'] ) {
	echo '<div class="wpsl-attr"><span class="char-name">' . __( "Sku", "wpsl" ) . '</span><span class="wpsl-attr__val">' . $default_atts['_sku'] . '</span></div>';
}

// custom attributes
if ( $atts = wpsl_post( '_atts' ) ) {
	/**
	 * First, show non-variable attributes
	 */
	foreach ( $atts as $attr ) {
		$attr = json_decode( $attr );
		if ( $attr->attribute_variable != 1 ) {
			$next_part = $attr->attribute_value == '-' ? ' next-part' : '';
			echo '<div class="wpsl-attr' . $next_part . '"><span class="wpsl-attr__name">' . $attr->attribute_name . '</span><span class="wpsl-attr__val">' . $attr->attribute_value . ' ' . $attr->attribute_measure . '</span></div>';
		}
	}

}
?>

<?php if( $default_atts['_length'] != '' or $default_atts['_width'] != '' or $default_atts['_height'] != '' or $default_atts['_weight'] ) : ?>
<div class="wpsl-attr next-part">
	<span class="wpsl-attr__name"><?php _e( 'Sizes', 'wpsl' ); ?></span>
	<span class="wpsl-attr__val">-</span>
</div>
<?php endif; ?>

<?php if( $default_atts['_length'] ) : ?>
<div class="wpsl-attr">
	<span class="wpsl-attr__name"><?php _e( 'Length', 'wpsl' ); ?></span>
	<span class="wpsl-attr__val"><?php printf( '%s %s', $default_atts['_length'], wpsl_dimensions_unit( wpsl_opt( 'dimension_unit', 'mm' ) ) ); ?></span>
</div>
<?php endif; ?>

<?php if( $default_atts['_width'] ) : ?>
<div class="wpsl-attr">
	<span class="wpsl-attr__name"><?php _e( 'Width', 'wpsl' ); ?></span>
	<span class="wpsl-attr__val"><?php printf( '%s %s', $default_atts['_width'], wpsl_dimensions_unit( wpsl_opt( 'dimension_unit', 'mm' ) ) ); ?></span>
</div>
<?php endif; ?>

<?php if( $default_atts['_height'] ) : ?>
<div class="wpsl-attr">
	<span class="wpsl-attr__name"><?php _e( 'Height', 'wpsl' ); ?></span>
	<span class="wpsl-attr__val"><?php printf( '%s %s', $default_atts['_height'], wpsl_dimensions_unit( wpsl_opt( 'dimension_unit', 'mm' ) ) ); ?></span>
</div>
<?php endif; ?>

<?php if( $default_atts['_weight'] ) : ?>
<div class="wpsl-attr">
	<span class="wpsl-attr__name"><?php _e( 'Weight', 'wpsl' ); ?></span>
	<span class="wpsl-attr__val"><?php printf( '%s %s', $default_atts['_weight'], wpsl_weight_unit( wpsl_opt( 'weight_unit', 'kg' ) ) ); ?></span>
</div>
<?php endif; ?>

<?php if( $default_atts['_stock'] ) : ?>
<div class="wpsl-attr">
	<span class="wpsl-attr__name"><?php _e( 'Stock', 'wpsl' ); ?></span>
	<span class="wpsl-attr__val"><?php echo wpsl_get_stock_status( $default_atts['_stock'] ); ?></span>
</div>
<?php endif; ?>
