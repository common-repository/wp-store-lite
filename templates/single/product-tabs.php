<?php 
/**
 * wpStore
 *
 * Product tabs of single product
 *
 * @author	wpStore
 * @since	2.1
 */

// required description tab
$tabs = array();
if ( wpautop( get_the_content() ) ) {
	$tabs['description'] = array(
		'name'     => __( 'Description', 'wpsl' ),
		'icon'     => 'icon-book-open',
		'fill'     => wpautop( get_the_content() ),
	);
}

// attributes to tabs
if ( wpsl_opt( 'atts_to_tab' ) == true && ( get_post_meta( $post->ID, '_atts', true ) || get_post_meta( $post->ID, '_sku', true ) ) ) {
	$tabs['atts'] = array(
		'name'     => __( 'Characteristics', 'wpsl' ),
		'icon'     => 'icon-grid',
		'fill'     => wpsl_get_template_html( 'single', 'product-side-atts' ),
	);
}

// reviews tab
if ( wpsl_opt( 'tab_reviews_enable', true ) == true ) {
	$tabs['reviews'] = array(
		'name'     => wpsl_opt( 'tab_reviews_title', __( 'Reviews', 'wpsl' ) ) . ' (' . count( wpsl_get_reviews( $post->ID ) ) . ')',
		'icon'     => wpsl_opt( 'tab_reviews_icon', 'icon-star' ),
		'fill'     => wpsl_get_template_html( 'single', 'product-reviews' ),
	);
}

// custom tabs
if ( $count = wpsl_opt( 'tab_count' ) ) {
	for ( $i = 0; $i < $count; $i++ ) {
		if ( wpsl_opt( 'tab_enable_' . $i ) == true ) {
			$tabs['tab_' . $i] = array(
				'name'     => wpsl_opt( 'tab_title_' . $i ),
				'icon'     => wpsl_opt( 'tab_icon_' . $i ),
				'fill'     => wpsl_opt( 'tab_desc_' . $i ),
			);
		}
	}
}

echo do_shortcode(
	wpsl_counstructor_tabs (
		apply_filters( 'wpsl_single_product_tabs_box', $tabs )
	)
);