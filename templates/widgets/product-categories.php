<?php 
/**
 * wpStore
 *
 * The output of filter products through widget "Products Categories"
 *
 * This template can be overridden by copying it to yourtheme/wpstore/widgets/product-categories.php.
 *
 * @author	wpStore
 * @since	2.8.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

$terms = get_terms( 'product_cat',
	array(
		'hide_empty' => false,
		'parent'     => 0,
		'order'      => 'ASC',
		'meta_key'   => 'tax_position',
		'meta_type'  => 'NUMERIC',
		'orderby'    => 'meta_value_num',
	)
);
if( !$terms && count( $terms ) < 1 ) return;
?>
<div class="wpsl-sections">
	<ul class="wpsl-sections__list">
	<?php
	$i = 0;
	$tax = '';
	foreach ( $terms as $term ) {
		$termchildren = get_term_children( $term->term_id, 'product_cat' );
		$indicator = ( count( $termchildren ) > 0 ) ? '<span class="submenu-indicator">+</span>' : '';
		$class = $i == 0 ? ' class="active"' : '';
		$tax .= '<li' . $class . '><a href="' . get_term_link( $term->term_id, 'product_cat' ) . '" data-id="' . $term->term_id . '"><span class="wpsl-sections-label">' . $term->count . '</span>' . $term->name . ' ' . $indicator . '</a>';
		if ( count( $termchildren ) > 0 ) {
			$tax .= '<ul class="submenu">';
			foreach ( $termchildren as $child ) {
				$term = get_term_by( 'id', $child, 'product_cat' );
				$tax .= '<li><a href="' . get_term_link( $term->term_id, 'product_cat' ) . '">' . $term->name . '</a></li>';
			}
			$tax .= '</ul>';
		}
		$tax .= '</li>';
		$i++;
	}
	?>
	<?php echo $tax; ?>
	</ul>
</div>
