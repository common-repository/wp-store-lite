<?php
/**
 * wpStore
 *
 * Change permalinks of products and taxonomies
 *
 * @author	wpStore
 * @since	2.6.0
 */
 
if ( ! defined( 'ABSPATH' ) ) exit;


function wpsl_settings_html() {
	
	echo wpautop( __( 'These settings control the permalinks used specifically for products.', 'wpsl' ) );
	$base_slug      = 'product';

	$structures = array( 
		0 => '',
		1 => '/' . trailingslashit( $base_slug ),
		2 => '/' . trailingslashit( '%category%' ),
		3 => '/' . trailingslashit( '%categories%' ),
		4 => '/' . trailingslashit( '%author%' ),
	 );
	?>
	<table class="form-table wc-permalink-structure">
		<tbody>
			<tr>
				<th><label><input name="product_permalink" type="radio" value="<?php echo esc_attr( $structures[0] ); ?>" class="wpsl-tog" <?php checked( $structures[0], get_option( 'product_permalink' ) ); ?> /> <?php _e( 'Without slug', 'wpsl' ); ?></label></th>
				<td><code class="default-example"><?php echo esc_html( home_url() ); ?>/?product=sample-product</code> <code class="non-default-example"><?php echo esc_html( home_url() ); ?>/sample-product/</code></td>
			</tr>
			<tr>
				<th><label><input name="product_permalink" type="radio" value="<?php echo esc_attr( $structures[1] ); ?>" class="wpsl-tog" <?php checked( $structures[1], get_option( 'product_permalink' ) ); ?> /> <?php _e( 'Custom slug', 'wpsl' ); ?></label></th>
				<td><code><?php echo esc_html( home_url() ); ?>/<?php echo esc_html( $base_slug ); ?>/sample-product/</code></td>
			</tr>
			<tr>
				<th><label><input name="product_permalink" type="radio" value="<?php echo esc_attr( $structures[2] ); ?>" class="wpsl-tog" <?php checked( $structures[2], get_option( 'product_permalink' ) ); ?> /> <?php _e( 'Without slug with category', 'wpsl' ); ?></label></th>
				<td><code><?php echo esc_html( home_url() ); ?>/product-category/sample-product/</code></td>
			</tr>
			<tr>
				<th><label><input name="product_permalink" type="radio" value="<?php echo esc_attr( $structures[3] ); ?>" class="wpsl-tog" <?php checked( $structures[3], get_option( 'product_permalink' ) ); ?> /> <?php _e( 'Without slug with categories', 'wpsl' ); ?></label></th>
				<td><code><?php echo esc_html( home_url() ); ?>/parent-category/children-category/sample-product/</code></td>
			</tr>
			<tr>
				<th><label><input name="product_permalink" type="radio" value="<?php echo esc_attr( $structures[4] ); ?>" class="wpsl-tog" <?php checked( $structures[4], get_option( 'product_permalink' ) ); ?> /> <?php _e( 'Without slug with seller', 'wpsl' ); ?></label></th>
				<td><code><?php echo esc_html( home_url() ); ?>/admin/sample-product/</code></td>
			</tr>
			<tr>
				<th><label><input name="product_permalink" id="wpsl_custom_selection" type="radio" value="custom" class="tog" <?php checked( in_array( get_option( 'product_permalink' ), $structures ), false ); ?> />
					<?php _e( 'Custom base', 'wpsl' ); ?></label></th>
				<td>
					<input name="product_permalink" id="wpsl_permalink_structure" type="text" value="<?php echo esc_attr( get_option( 'product_permalink' ) ? trailingslashit( get_option( 'product_permalink' ) ) : '' ); ?>" class="regular-text code"> <span class="description"><?php _e( 'You can use any base slug or completely abandon it', 'wpsl' ); ?></span>
				</td>
			</tr>
		</tbody>
	</table>
	<script type="text/javascript">
		jQuery( function() {
			jQuery( 'input.wpsl-tog' ).change( function() {
				jQuery( '#wpsl_permalink_structure' ).val( jQuery( this ).val() );
			} );
			jQuery( '.permalink-structure input' ).change( function() {
				jQuery( '.wc-permalink-structure' ).find( 'code.non-default-example, code.default-example' ).hide();
				if ( jQuery( this ).val() ) {
					jQuery( '.wc-permalink-structure code.non-default-example' ).show();
					jQuery( '.wc-permalink-structure input' ).removeAttr( 'disabled' );
				} else {
					jQuery( '.wc-permalink-structure code.default-example' ).show();
					jQuery( '.wc-permalink-structure input:eq( 0 )' ).click();
					jQuery( '.wc-permalink-structure input' ).attr( 'disabled', 'disabled' );
				}
			} );
			jQuery( '.permalink-structure input:checked' ).change();
			jQuery( '#wpsl_permalink_structure' ).focus( function(){
				jQuery( '#wpsl_custom_selection' ).click();
			} );
		} );
	</script>
	<?php
}


function wpsl_tax_permalinks() {
	
	echo wpautop( __( 'These settings control the permalinks used specifically for category of products.', 'wpsl' ) );
	$base_slug      = 'product_cat';

	$structures = array( 
		0 => '/' . trailingslashit( $base_slug ),
		1 => '',
		2 => '/' . trailingslashit( '%category%' ),
	);
	?>
	<table class="form-table wc-permalink-structure">
		<tbody>
			<tr>
				<th><label><input name="tax_permalink" type="radio" value="<?php echo esc_attr( $structures[0] ); ?>" class="wpsl-tax" <?php checked( $structures[0], get_option( 'tax_permalink' ) ); ?> /> <?php _e( 'Custom slug', 'wpsl' ); ?></label></th>
				<td><code><?php echo esc_html( home_url() ); ?>/<?php echo esc_html( $base_slug ); ?>/product-category/</code></td>
			</tr>
			<tr>
				<th><label><input name="tax_permalink" type="radio" value="<?php echo esc_attr( $structures[1] ); ?>" class="wpsl-tax" <?php checked( $structures[1], get_option( 'tax_permalink' ) ); ?> /> <?php _e( 'Without slug', 'wpsl' ); ?></label></th>
				<td><code class="default"><?php echo esc_html( home_url() ); ?>/product-category/</code></td>
			</tr>
			<tr>
				<th><label><input name="tax_permalink" type="radio" value="<?php echo esc_attr( $structures[2] ); ?>" class="wpsl-tax" <?php checked( $structures[2], get_option( 'tax_permalink' ) ); ?> /> <?php _e( 'Without slug with category hierarchy', 'wpsl' ); ?></label></th>
				<td><code><?php echo esc_html( home_url() ); ?>/parent-category/children-category/</code></td>
			</tr>
			<tr>
				<th><label><input name="tax_permalink" id="wpsl_tax_custom_selection" type="radio" value="custom" class="tax" <?php checked( in_array( get_option( 'tax_permalink' ), $structures ), false ); ?> />
					<?php _e( 'Custom base', 'wpsl' ); ?></label></th>
				<td>
					<input name="tax_permalink" id="wpsl_tax_permalink_structure" type="text" value="<?php echo esc_attr( get_option( 'tax_permalink' ) ? trailingslashit( get_option( 'tax_permalink' ) ) : '' ); ?>" class="regular-text code"> <span class="description"><?php _e( 'You can use any base slug or completely abandon it', 'wpsl' ); ?></span>
				</td>
			</tr>
		</tbody>
	</table>
	<script type="text/javascript">
		jQuery( function() {
			jQuery( 'input.wpsl-tax' ).change( function() {
				jQuery( '#wpsl_tax_permalink_structure' ).val( jQuery( this ).val() );
			} );
			jQuery( '#wpsl_tax_permalink_structure' ).focus( function(){
				jQuery( '#wpsl_tax_custom_selection' ).click();
			} );
		} );
	</script>
	<?php
}


// добавление опции на страницу "Настройки постоянных ссылок"
add_action( 'load-options-permalink.php', 'wpsl_custom_load_permalinks' );
function wpsl_custom_load_permalinks() {
    if( isset( $_POST['product_permalink'] ) ) {
        update_option( 'product_permalink', $_POST['product_permalink'] );
    }
    if( isset( $_POST['tax_permalink'] ) ) {
        update_option( 'tax_permalink', $_POST['tax_permalink'] );
    }
	
	add_settings_section( 
		'wpsl_permalinks',
		__( 'Permalinks of single products', 'wpsl' ),
		'wpsl_settings_html',
		'permalink'
	 );
 	add_settings_section( 
		'wpsl_tax_permalinks',
		__( 'Permalinks of categories products', 'wpsl' ),
		'wpsl_tax_permalinks',
		'permalink'
	 );
}


/**
 * Url for product categories
 */
add_filter( 'request', 'wpsl_change_request_for_product_cat' );
add_filter( 'term_link', 'wpsl_term_link_filter', 10, 3 );
add_filter( 'generate_rewrite_rules', 'wpsl_rewriting_product_cat_urls' ); // Create http://yor-domain.com/parent-category/children-category/children-category/
function wpsl_change_request_for_product_cat( $vars ) {
    global $wpdb;
    if ( ! empty( $vars[ 'pagename' ] ) || ! empty( $vars[ 'category_name' ] ) || ! empty( $vars[ 'name' ] ) || ! empty( $vars[ 'attachment' ] ) ) {
		$slug   = ! empty( $vars[ 'pagename' ] ) ? $vars[ 'pagename' ] : ( ! empty( $vars[ 'name' ] ) ? $vars[ 'name' ] : ( ! empty( $vars[ 'category_name' ] ) ? $vars[ 'category_name' ] : $vars[ 'attachment' ] ) );
		$exists = $wpdb->get_var( $wpdb->prepare( "SELECT t.term_id FROM $wpdb->terms t LEFT JOIN $wpdb->term_taxonomy tt ON tt.term_id = t.term_id WHERE tt.taxonomy = 'product_cat' AND t.slug = %s", array( $slug ) ) );
		if ( $exists ) {
			$old_vars = $vars;
			$vars     = array( 'product_cat' => $slug );
			if ( ! empty( $old_vars[ 'paged' ] ) || ! empty( $old_vars[ 'page' ] ) ) {
				$vars[ 'paged' ] = ! empty( $old_vars[ 'paged' ] ) ? $old_vars[ 'paged' ] : $old_vars[ 'page' ];
			}
			if ( ! empty( $old_vars[ 'orderby' ] ) ) {
				$vars[ 'orderby' ] = $old_vars[ 'orderby' ];
			}
			if ( ! empty( $old_vars[ 'order' ] ) ) {
				$vars[ 'order' ] = $old_vars[ 'order' ];
			}
		}
    }

    return $vars;
}

function wpsl_term_link_filter( $url, $term, $taxonomy ) {
	if ( get_option( 'tax_permalink' ) == '' ) {
		return str_replace( "/product_cat/", "/", $url );
	}
	
	$url = str_replace( "/product_cat/", get_option( 'tax_permalink' ), $url );
	
	if ( strpos( get_option( 'tax_permalink' ), '/%category%/' ) !== false ) {
		$slugs = '';
		if ( $parents_terms = get_ancestors( $term->term_id, 'product_cat', 'taxonomy' ) ) {
			krsort( $parents_terms );
			foreach ( $parents_terms as $parent_term_id ) {
				$term = get_term( $parent_term_id, 'product_cat' );
				$slugs .= '/' . $term->slug;
			}
		}
		$url = str_replace( "/%category%/", $slugs . '/', $url );
	}
    return $url;
}

function wpsl_rewriting_product_cat_urls( $wp_rewrite ) {
	
	if ( strpos( get_option( 'tax_permalink' ), '/%category%/' ) === false ){
		return $wp_rewrite;
	}
	
	$rules = array();
	$terms = get_terms(
		array(
			'taxonomy'   => 'product_cat',
			'hide_empty' => false,
		)
	);
   
	$post_type = 'product';
	foreach ( $terms as $term ) {
		$hierarchical_slugs = array();
		// Is it a child term?
		if( $term->parent ) {
			$ancestors = get_ancestors( $term->term_id, $term->taxonomy, 'taxonomy' );
			foreach ( (array)$ancestors as $ancestor ) {
				$ancestor_term = get_term( $ancestor, $term->taxonomy );
				$hierarchical_slugs[] = $ancestor_term->slug;
			}
			$hierarchical_slugs = array_reverse( $hierarchical_slugs );
			$hierarchical_slugs[] = $term->slug;
			 
			//print_r( str_replace( "%category%/", implode( '/', $hierarchical_slugs ), get_option( 'tax_permalink' ) ) );
			 
			$url = trim( str_replace( "%category%/", implode( '/', $hierarchical_slugs ), get_option( 'tax_permalink' ) ), '/\\' );
		} else {
			$url = trim( str_replace( "%category%/", $term->slug, get_option( 'tax_permalink' ) ), '/\\' );
		}
		$rules[$url . '/([^/]*)$'] = 'index.php?post_type=' . $post_type. '&' . $post_type . '=$matches[1]&name=$matches[1]';
		
	}
	// merge with global rules
	$wp_rewrite->rules = $rules + $wp_rewrite->rules;
	//print_r( $wp_rewrite->rules );
}


/**
 * Url for single products
 */
add_filter( 'post_link', 'wpsl_post_type_permalink', 20, 3 );
add_filter( 'post_type_link', 'wpsl_post_type_permalink', 20, 3 );
function wpsl_post_type_permalink( $permalink, $post_id, $leavename ) {

	$post_type_name = 'product'; // post type name, you can find it in admin area or in register_post_type() function
	$post_type_slug = 'product'; // the part of your product URLs, not always matches with the post type name

	$post = get_post( $post_id );

	// do not make changes if the post has different type or its URL doesn't contain the given post type slug
	if ( strpos( $permalink, $post_type_slug ) === FALSE || $post->post_type != $post_type_name ) {
		return $permalink;
	}
	
	$custom_slug = get_option( 'product_permalink' );

	/**
	 * Works only in the admin panel when changing the structure of permanent links or creating/updating the product
	 * In the frontend to display links to products using $post->guid
	 * Relevant if the structure of permalinks are used %category% or %categories%
	 */
	if ( is_admin() ) {
		// get all terms (product categories) of this post (product) by hierarchicaly
		// change %category%
		if ( strpos( get_option( 'product_permalink' ), '%category%' ) !== false && $terms = wpsl_get_terms_hierarchicaly( $post->ID, 'product_cat' ) ) {
			$custom_slug = str_replace( '%category%', isset( $terms[0] ) && is_object( $terms[0] ) ? $terms[0]->slug : '', $custom_slug );
		}
		
		// change %categories%
		if ( strpos( get_option( 'product_permalink' ), '%categories%' ) !== false && $terms = wpsl_get_terms_hierarchicaly( $post->ID, 'product_cat' ) ) {
			foreach( $terms as $term ) {
				$hierarchical_slugs[] = $term->slug;
			}
			$custom_slug = str_replace( '%categories%', implode( '/', $hierarchical_slugs ), $custom_slug );
		} else {
			$custom_slug = str_replace( '%categories%', 'product', $custom_slug );
		}
	}
	
	// change %author%
	if ( strpos( get_option( 'product_permalink' ), '%author%' ) !== false ) {
		$authordata = get_userdata( $post->post_author );
		$custom_slug = str_replace( '%author%', $authordata->user_nicename, $custom_slug );
	}
 
	// rewrite only if this product has categories
	if ( $custom_slug ) {
		$permalink = str_replace( '/' . $post_type_slug . '/', $custom_slug, $permalink );
	}

	return $permalink;
}
 
 
add_filter( 'request', 'wpsl_post_type_request', 1, 1 );
function wpsl_post_type_request( $query ){
	global $wpdb;
 
	$post_type_name = 'product'; // specify your own here
	$tax_name = 'product_cat'; // and here
 
	$slug = @$query['attachment']; // when we change the post type link, WordPress thinks that these are attachment pages
 
	// get the post with the given type and slug from the database
	$post_id = $wpdb->get_var(
		"
		SELECT ID
		FROM $wpdb->posts
		WHERE post_name = '$slug'
		AND post_type = '$post_type_name'
		"
	);
 
	$terms = wp_get_object_terms( $post_id, $tax_name ); // our post should have the terms
 
	// change the query
	if( isset( $slug ) && $post_id && !is_wp_error( $terms ) && !empty( $terms ) ) {
		unset( $query['attachment'] );
		$query[$post_type_name] = $slug;
		$query['post_type'] = $post_type_name;
		$query['name'] = $slug;
	}
 
	return $query;
}

add_action( 'init', 'wpsl_product_permastructure', 9999 );
function wpsl_product_permastructure() {
	global $wp_rewrite;
	if ( get_option( 'product_permalink' ) == '' ) {
		$wp_rewrite->add_rewrite_tag( "%product%", '([^/]+)', "product=" );
		$wp_rewrite->add_permastruct( 'product', get_option( 'product_permalink' ) . '%product%', false );
	}
}

add_action( 'pre_get_posts', 'wpsl_rewrite_product_rules', 9999, 1 );
function wpsl_rewrite_product_rules( $query ) {
	
	if( is_admin() || ! $query->is_main_query() ) return;

	if( ! isset( $query->query['page'] ) || empty( $query->query['name'] ) || count( $query->query ) != 2 )
		return;
	
	if ( !empty( $query->query['name'] ) && get_option( 'product_permalink' ) == '' ) {
		$query->set( 'post_type', array( 'post', 'page', 'product' ) );
	}
}

/**
 * Rewrite rules for products
 */
/* add_action( 'init', 'wpsl_product_permastructure', 1 );
function wpsl_product_permastructure() {
	global $wp_rewrite;
	if ( get_option( 'product_permalink' ) == '' ) {
		$wp_rewrite->add_rewrite_tag( "%product%", '([^/]+)', "product=" );
		$wp_rewrite->add_permastruct( 'product', get_option( 'product_permalink' ) . '%product%', false );
	}
}

add_action( 'pre_get_posts', 'wpsl_rewrite_product_rules', 10, 1 );
function wpsl_rewrite_product_rules( $query ) {
	if( is_admin() || ! $query->is_main_query() ) return;

	if( ! isset( $query->query['page'] ) || empty( $query->query['name'] ) || count( $query->query ) != 2 )
		return;
	if (!empty( $query->query['name'])) {
		$query->set( 'post_type', array( 'post', 'page', 'product' ) );
	}
}

// Add filter to plugin init function
add_filter( 'post_type_link', 'gallery_permalink', 20, 3 );
function gallery_permalink( $permalink, $post_id, $leavename ) {
	
    $post = get_post( $post_id );
 
    if ( $post->post_type == 'product' && '' != $permalink && !in_array( $post->post_status, array( 'draft', 'pending', 'auto-draft' ) ) ) {
		
		$rewritecode = array(
			'%year%',
			'%monthnum%',
			'%day%',
			'%hour%',
			'%minute%',
			'%second%',
			$leavename? '' : '%postname%',
			'%post_id%',
			'%category%',
			'%author%',
			$leavename? '' : '%pagename%',
		);
		
        $unixtime = strtotime( $post->post_date );

        $category = '';
		if ( strpos( $permalink, '%category%' ) !== false ) {
			if ( $post->post_type == 'product' ) {
				if ( $terms = wpsl_get_terms_hierarchicaly( $post->ID ) ) {
					foreach( $terms as $term ) {
						$category .= $term->slug . '/';
					}
					$category = untrailingslashit( $category );
				} 
				if ( empty( $terms ) ) {
					$category = '';
				}
			} else {
				$cats = get_the_category( $post->ID );
				if ( $cats ) {
					usort($cats, '_usort_terms_by_ID'); // order by ID
					$category = $cats[0]->slug;
					if ( $parent = $cats[0]->parent )
						$category = get_category_parents($parent, false, '/', true) . $category;
				}
				// show default category in permalinks, without
				// having to assign it explicitly
				if ( empty($category) ) {
					$default_category = get_category( get_option( 'default_category' ) );
					$category = is_wp_error( $default_category ) ? '' : $default_category->slug;
				}
			}
		}
     
        $author = '';
        if ( strpos( $permalink, '%author%' ) !== false ) {
            $authordata = get_userdata( $post->post_author );
            $author = $authordata->user_nicename;
        }
     
        $date = explode(" ",date('Y m d H i s', $unixtime));
        $rewritereplace =
        array(
            $date[0],
            $date[1],
            $date[2],
            $date[3],
            $date[4],
            $date[5],
            $post->post_name,
            $post->ID,
            $category,
            $author,
            $post->post_name,
        );
        $permalink = str_replace( $rewritecode, $rewritereplace, $permalink );
		
		if ( strpos( get_option( 'product_permalink' ), '/product/' ) === false ) {
			$permalink = str_replace('/' . $post->post_type . '/', '/', $permalink );
		}
		
    } else {
		// if they're not using the fancy permalink option
    }
    return $permalink;
} */



/**
 * If you configure the permalinks of the products contain one or more categories that are written the rules NC in the guid field
 * This reduces the number of SQL queries to the database by the number of products displayed per page
 */
add_action( 'save_post', 'wpsl_guid_rewrite', 100 );
function wpsl_guid_rewrite( $id ) {
	
	if( !is_admin() ) return;
	
	if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return false;

	if ( strpos( get_option( 'product_permalink' ), '%category%' ) !== false || strpos( get_option( 'product_permalink' ), '%categories%' ) !== false ) {
		if( $id && get_post_type( intval( $id ) ) == 'product' ){
			global $wpdb;
			$wpdb->update( $wpdb->posts, [ 'guid' => ( get_permalink( $id ) ) ], [ 'ID' => intval( $id ) ] );
		}

		clean_post_cache( $id );
	}
}


/**
 * Updating guid when changing permalinks of products
 */
add_action( 'update_option_rewrite_rules', 'wpsl_update_permalinks_in_guid', 10, 3 );
function wpsl_update_permalinks_in_guid( $option, $old_value, $value ){
	if ( strpos( get_option( 'product_permalink' ), '%category%' ) !== false || strpos( get_option( 'product_permalink' ), '%categories%' ) !== false ) {
		global $wpdb;
		remove_filter( 'post_type_link', 'wpsl_get_permalink_change', 10 );
		$postids = $wpdb->get_results( "SELECT ID FROM $wpdb->posts WHERE post_type='product'" );
		if ( $postids ) {
			foreach ( $postids as $id ) {
				$wpdb->update( $wpdb->posts, [ 'guid' => ( get_permalink( $id->ID ) ) ], [ 'ID' => intval( $id->ID ) ] );
				clean_post_cache( $id->ID );
			}
		}
	}
}


/**
 * Update the count of products by attributes
 *
 * @since    2.8
 */
add_action( 'wpsl_update_atts', 'wpsl_update_atts_count', 10 );
function wpsl_update_atts_count(){
	if ( $atts = wpsl_get_atts() ) {
		foreach ( $atts as $attr ) {
			$products = get_posts(
				array(
					'numberposts' => -1,
					'meta_key'    => $attr->attribute_label,
					'post_type'   => 'product',
					'post_status' => 'publish',
				)
			);
			if ( !$products ) {
				$products = array();
			}
			wpsl_update_attr_param( $attr->attribute_id, array( 'attribute_count' => count( $products ) ) );
		}
	}
}


/**
 * Update the count of all products by attributes
 * And save in options 'wpsl_attributes'
 *
 * @since    2.8
 */
add_action( 'delete_term', 'wpsl_update_all_atts_count' );
add_action( 'save_post', 'wpsl_update_all_atts_count' );
add_action( 'edited_term', 'wpsl_update_all_atts_count' );
add_action( 'wpsl_update_all_atts', 'wpsl_update_all_atts_count' );
function wpsl_update_all_atts_count(){
	
	if ( !is_admin() ) return;
	
	$terms = get_terms(
		array(
			'taxonomy'   => 'product_cat',
			'hide_empty' => true,
		)
	);
	$count = array();
	if ( $terms ) {
		foreach ( $terms as $term ) {
			if( isset( $term->term_id ) && $atts = wpsl_get_atts_by_term_id( $term->term_id ) ) {
				foreach ( $atts as $attr ) {
					$params = array_unique( wpsl_get_category_atts( $term->term_id, $attr->attribute_label ) );
					foreach ( $params as $val ) {
						$products = get_posts(
							array(
								'numberposts' => -1,
								'meta_key'    => $attr->attribute_label,
								'meta_value'  => $val,
								'post_type'   => 'product',
								'post_status' => 'publish',
							)
						);
						if ( !$products ) {
							$products = array();
						}
						$count[$term->term_id][$attr->attribute_label][$val] = count( $products );
					}
				}
			}
		}
		update_option( 'wpsl_attributes', $count, false );
	}
}