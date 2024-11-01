<?php 
/**
 * wpStore
 *
 * The output of filter products through widget "Products filter"
 *
 * This template can be overridden by copying it to yourtheme/wpstore/widgets/product-filter.php.
 *
 * @author	wpStore
 * @since	2.8.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$term_id = get_queried_object()->term_id;
$prices = wpsl_get_minmax_prices( $term_id );
$count = get_option( 'wpsl_attributes' );

$val = isset( $_GET['_price'] ) ? $_GET['_price'] : $prices->min_price . ',' . $prices->max_price;
?>

<form class="wpsl-filter <?php echo wpsl_get_widget_opt( 'widget_wpsl_product_filter', 'place' ); ?>" action="" method="get">
	<div class="wpsl-filter__item">
		<div class="wpsl-filter__item_title"><?php _e( 'Price', 'wpsl' ); ?><i class="icon-chevron-down"></i></div>
		<div class="wpsl-filter__item_values wpsl-r">
			<input class="range-slider-price" type="hidden" name="_price" data-from="<?php echo $prices->min_price; ?>" data-to="<?php echo $prices->max_price; ?>" data-step="1" data-scale="[<?php echo implode( ',', wpsl_get_price_step( $prices->min_price, $prices->max_price ) ); ?>]" data-format="%s <?php echo wpsl_opt(); ?>" value="<?php echo $val; ?>"/>
		</div>
	</div>

	<?php if ( $atts = wpsl_sort_atts_by( wpsl_get_atts( array( 'attribute_term_id' => $term_id ) ), 'attribute_position' ) ) : ?>
		<?php foreach ( $atts as $attr ) : 
			$params = array_unique( wpsl_get_category_atts( $term_id, $attr->attribute_label ) );
			if ( $attr->attribute_filterable != true || empty( $params ) ) continue; ?>
			<div class="wpsl-filter__item">
				<div class="wpsl-filter__item_title"><?php echo $attr->attribute_name; ?><i class="icon-chevron-down"></i></div>
				<div class="wpsl-filter__item_values">
					<?php 
					$i = 0;
					foreach ( $params as $val ) {
						$checked = isset( $_GET[$attr->attribute_label] ) && in_array( $val, $_GET[$attr->attribute_label] ) ? 'checked' : '';
						echo '<label title="' . $val . ' ' . $attr->attribute_measure . '"><input class="wpsl-clear" type="checkbox" name="' . $attr->attribute_label . '[' . $i . ']" data-default="' . $val . '" value="' . $val . '" ' . $checked . '>' . $val . ' <sup>' . $count[$term_id][$attr->attribute_label][$val] . '</sup></label>';
						$i++;
					} ?>
					<div><span class="all"><?php _e( 'Select all', 'wpsl' ); ?></span><span class="reset"><?php _e( 'Reset', 'wpsl' ); ?></span></div>
				</div>
			</div>
		<?php endforeach; ?>
	<?php endif; ?>

	<div class="wpsl-filter__submit">
		<input type="hidden" name="term_id" value="<?php echo $term_id; ?>" >
		<input type="submit" class="wpsl-filter__submit_btn" data-term_id="<?php echo $term_id; ?>" value="<?php _e( 'Filter', 'wpsl' ); ?>" >
	</div>
	<div class="wpsl-filter__result <?php echo wpsl_get_widget_opt( 'widget_wpsl_product_filter', 'place' ); ?>"></div>
</form>
