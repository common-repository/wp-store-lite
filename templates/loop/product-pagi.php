<?php 
/**
 * Page navigation output
 *
 * This template can be overridden by copying it to yourtheme/wpstore/loop/product-pagination.php.
 *
 * @author	wpStore
 * @since	2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;


if ( $products->max_num_pages <= 1 ) {
	return;
}
?>

<nav class="wpsl-pagination">
	<?php
		$page = is_front_page() ? 'page' : 'paged';
		echo paginate_links( apply_filters( 'wpsl_pagination_args', array(
			'base'         => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
			'total'        => $products->max_num_pages,
			'current'      => max( 1, get_query_var( $page ) ),
			'format'       => '?paged=%#%',
			'show_all'     => false,
			'type'         => 'plain',
			'end_size'     => 5,
			'mid_size'     => 5,
			'prev_next'    => true,
			'prev_text'    => '<i class="icon-chevron-left"></i>',
			'next_text'    => '<i class="icon-chevron-right"></i>',
			'add_args'     => false,
		) ) );
	?>
</nav>
