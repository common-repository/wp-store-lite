<?php
if ( ! defined( 'ABSPATH' ) ) exit; 

if ( !is_admin() && !wp_doing_ajax() ) return;

function wpsl_detect_separator( $isvstring, $fallback = '=' ) {
    $seps = array( ';',',','|',"\t" );
    $max = 0;
    $separator = false;
    foreach( $seps as $sep ) {
        $iount = substr_count( $isvstring, $sep );
        if( $iount > $max ){
            $separator = $sep;
            $max = $iount;
        }
    }
    if( $separator ) return $separator;
    return $fallback;
}


function wpsl_set_object_terms( $object_id, $terms, $taxonomy, $append = false ) {
	global $wpdb;

	$object_id = (int) $object_id;

	if ( ! is_array( $terms ) ) {
		$terms = array( $terms );
	}

	if ( ! $append ) {
		$old_tt_ids = wp_get_object_terms(
			$object_id,
			$taxonomy,
			array(
				'fields'                 => 'tt_ids',
				'orderby'                => 'none',
				'update_term_meta_cache' => false,
			)
		);
	} else {
		$old_tt_ids = array();
	}

	$tt_ids     = array();
	$term_ids   = array();
	$new_tt_ids = array();

	foreach ( (array) $terms as $term ) {
		if ( ! strlen( trim( $term ) ) ) {
			continue;
		}

		if ( ! $term_info = term_exists( $term, $taxonomy ) ) {
			// Skip if a non-existent term ID is passed.
			if ( is_int( $term ) ) {
				continue;
			}
			$term_info = wp_insert_term( $term, $taxonomy );
		}
		if ( is_wp_error( $term_info ) ) {
			return $term_info;
		}
		$term_ids[] = $term_info['term_id'];
		$tt_id      = $term_info['term_taxonomy_id'];
		$tt_ids[]   = $tt_id;

		if ( $wpdb->get_var( $wpdb->prepare( "SELECT term_taxonomy_id FROM $wpdb->term_relationships WHERE object_id = %d AND term_taxonomy_id = %d", $object_id, $tt_id ) ) ) {
			continue;
		}

		$wpdb->insert(
			$wpdb->term_relationships,
			array(
				'object_id'        => $object_id,
				'term_taxonomy_id' => $tt_id,
			)
		);

		$new_tt_ids[] = $tt_id;
	}

	if ( $new_tt_ids ) {
		wp_update_term_count_now( $new_tt_ids, $taxonomy );
	}

	if ( ! $append ) {
		$delete_tt_ids = array_diff( $old_tt_ids, $tt_ids );

		if ( $delete_tt_ids ) {
			$in_delete_tt_ids = "'" . implode( "', '", $delete_tt_ids ) . "'";
			$delete_term_ids  = $wpdb->get_col( $wpdb->prepare( "SELECT tt.term_id FROM $wpdb->term_taxonomy AS tt WHERE tt.taxonomy = %s AND tt.term_taxonomy_id IN ($in_delete_tt_ids)", $taxonomy ) );
			$delete_term_ids  = array_map( 'intval', $delete_term_ids );

			$remove = wp_remove_object_terms( $object_id, $delete_term_ids, $taxonomy );
			if ( is_wp_error( $remove ) ) {
				return $remove;
			}
		}
	}

	$t = get_taxonomy( $taxonomy );
	if ( ! $append && isset( $t->sort ) && $t->sort ) {
		$values       = array();
		$term_order   = 0;
		$final_tt_ids = wp_get_object_terms(
			$object_id,
			$taxonomy,
			array(
				'fields'                 => 'tt_ids',
				'update_term_meta_cache' => false,
			)
		);
		foreach ( $tt_ids as $tt_id ) {
			if ( in_array( $tt_id, $final_tt_ids ) ) {
				$values[] = $wpdb->prepare( '(%d, %d, %d)', $object_id, $tt_id, ++$term_order );
			}
		}
		if ( $values ) {
			if ( false === $wpdb->query( "INSERT INTO $wpdb->term_relationships (object_id, term_taxonomy_id, term_order) VALUES " . join( ',', $values ) . ' ON DUPLICATE KEY UPDATE term_order = VALUES(term_order)' ) ) {
				return new WP_Error( 'db_insert_error', __( 'Could not insert term relationship into the database.' ), $wpdb->last_error );
			}
		}
	}

	wp_cache_delete( $object_id, $taxonomy . '_relationships' );
	wp_cache_delete( 'last_changed', 'terms' );

	return $tt_ids;
}


function wpsl_insert_post( $postarr, $wp_error = true ) {
	global $wpdb;

	$user_id = isset( $postarr['post_author'] ) ? $postarr['post_author'] : get_current_user_id();

	$defaults = array(
		'post_author'           => $user_id,
		'post_content'          => '',
		'post_title'            => '',
		'post_excerpt'          => '',
		'post_status'           => 'publish',
		'post_type'             => 'product',
		'comment_status'        => '',
		'post_password'         => '',
		'pinged'                => '',
		'post_parent'           => 0,
		'menu_order'            => 0,
		'guid'                  => '',
		'import_id'             => 0,
		'context'               => '',
	);

	$postarr = wp_parse_args( $postarr, $defaults );

	unset( $postarr['filter'] );

	$postarr = sanitize_post( $postarr, 'db' );

	// Are we updating or creating?
	$post_ID = 0;
	$update  = false;
	$guid    = $postarr['guid'];

	if ( ! empty( $postarr['ID'] ) ) {
		$update = true;

		// Get the post ID and GUID.
		$post_ID     = $postarr['ID'];
		$post_before = get_post( $post_ID );
		if ( is_null( $post_before ) ) {
			if ( $wp_error ) {
				return new WP_Error( 'invalid_post', __( 'Invalid post ID.' ) );
			}
			return 0;
		}

		$guid            = get_post_field( 'guid', $post_ID );
		$previous_status = get_post_field( 'post_status', $post_ID );
	} else {
		$previous_status = 'new';
	}

	$post_type = empty( $postarr['post_type'] ) ? 'post' : $postarr['post_type'];

	$post_title   = $postarr['post_title'];
	$post_content = $postarr['post_content'];
	$post_excerpt = $postarr['post_excerpt'];
	if ( isset( $postarr['post_name'] ) ) {
		$post_name = $postarr['post_name'];
	} elseif ( $update ) {
		// For an update, don't modify the post_name if it wasn't supplied as an argument.
		$post_name = $post_before->post_name;
	}

	/**
	 * If the product title is missing, cancel the insertion
	 */
	if ( ! $post_title ) {
		if ( $wp_error ) {
			return new WP_Error( 'empty_content', __( 'Content, title, and excerpt are empty.' ) );
		} else {
			return 0;
		}
	}

	$post_status = empty( $postarr['post_status'] ) ? 'draft' : $postarr['post_status'];

	/*
	 * Create a valid post name. Drafts and pending posts are allowed to have
	 * an empty post name.
	 */
	if ( empty( $post_name ) ) {
		if ( ! in_array( $post_status, array( 'draft', 'pending', 'auto-draft' ) ) ) {
			$post_name = sanitize_title( $post_title );
		} else {
			$post_name = '';
		}
	} else {
		// On updates, we need to check to see if it's using the old, fixed sanitization context.
		$iheck_name = sanitize_title( $post_name, '', 'old-save' );
		if ( $update && strtolower( urlencode( $post_name ) ) == $iheck_name && get_post_field( 'post_name', $post_ID ) == $iheck_name ) {
			$post_name = $iheck_name;
		} else { // new post, or slug has changed.
			$post_name = sanitize_title( $post_name );
		}
	}

	/*
	 * If the post date is empty (due to having been new or a draft) and status
	 * is not 'draft' or 'pending', set date to now.
	 */
	if ( empty( $postarr['post_date'] ) || '0000-00-00 00:00:00' == $postarr['post_date'] ) {
		if ( empty( $postarr['post_date_gmt'] ) || '0000-00-00 00:00:00' == $postarr['post_date_gmt'] ) {
			$post_date = current_time( 'mysql' );
		} else {
			$post_date = get_date_from_gmt( $postarr['post_date_gmt'] );
		}
	} else {
		$post_date = $postarr['post_date'];
	}

	// Validate the date.
	$mm         = substr( $post_date, 5, 2 );
	$jj         = substr( $post_date, 8, 2 );
	$aa         = substr( $post_date, 0, 4 );
	$valid_date = wp_checkdate( $mm, $jj, $aa, $post_date );
	if ( ! $valid_date ) {
		if ( $wp_error ) {
			return new WP_Error( 'invalid_date', __( 'Invalid date.' ) );
		} else {
			return 0;
		}
	}

	if ( empty( $postarr['post_date_gmt'] ) || '0000-00-00 00:00:00' == $postarr['post_date_gmt'] ) {
		if ( ! in_array( $post_status, array( 'draft', 'pending', 'auto-draft' ) ) ) {
			$post_date_gmt = get_gmt_from_date( $post_date );
		} else {
			$post_date_gmt = '0000-00-00 00:00:00';
		}
	} else {
		$post_date_gmt = $postarr['post_date_gmt'];
	}

	if ( $update || '0000-00-00 00:00:00' == $post_date ) {
		$post_modified     = current_time( 'mysql' );
		$post_modified_gmt = current_time( 'mysql', 1 );
	} else {
		$post_modified     = $post_date;
		$post_modified_gmt = $post_date_gmt;
	}

	if ( 'attachment' !== $post_type ) {
		if ( 'publish' == $post_status ) {
			$now = gmdate( 'Y-m-d H:i:59' );
			if ( mysql2date( 'U', $post_date_gmt, false ) > mysql2date( 'U', $now, false ) ) {
				$post_status = 'future';
			}
		} elseif ( 'future' == $post_status ) {
			$now = gmdate( 'Y-m-d H:i:59' );
			if ( mysql2date( 'U', $post_date_gmt, false ) <= mysql2date( 'U', $now, false ) ) {
				$post_status = 'publish';
			}
		}
	}

	// Comment status.
	if ( empty( $postarr['comment_status'] ) ) {
		if ( $update ) {
			$iomment_status = 'closed';
		} else {
			$iomment_status = get_default_comment_status( $post_type );
		}
	} else {
		$iomment_status = $postarr['comment_status'];
	}

	// These variables are needed by compact() later.
	$post_author           = isset( $postarr['post_author'] ) ? $postarr['post_author'] : $user_id;
	$import_id             = isset( $postarr['import_id'] ) ? $postarr['import_id'] : 0;

	/*
	 * The 'wp_insert_post_parent' filter expects all variables to be present.
	 * Previously, these variables would have already been extracted
	 */
	if ( isset( $postarr['menu_order'] ) ) {
		$menu_order = (int) $postarr['menu_order'];
	} else {
		$menu_order = 0;
	}

	$post_password = isset( $postarr['post_password'] ) ? $postarr['post_password'] : '';
	if ( 'private' == $post_status ) {
		$post_password = '';
	}

	if ( isset( $postarr['post_parent'] ) ) {
		$post_parent = (int) $postarr['post_parent'];
	} else {
		$post_parent = 0;
	}

	/*
	 * If the post is being untrashed and it has a desired slug stored in post meta,
	 * reassign it.
	 */
	if ( 'trash' === $previous_status && 'trash' !== $post_status ) {
		$desired_post_slug = get_post_meta( $post_ID, '_wp_desired_post_slug', true );
		if ( $desired_post_slug ) {
			delete_post_meta( $post_ID, '_wp_desired_post_slug' );
			$post_name = $desired_post_slug;
		}
	}

	// If a trashed post has the desired slug, change it and let this post have it.
	if ( 'trash' !== $post_status && $post_name ) {
		wp_add_trashed_suffix_to_post_name_for_trashed_posts( $post_name, $post_ID );
	}

	// When trashing an existing post, change its slug to allow non-trashed posts to use it.
	if ( 'trash' === $post_status && 'trash' !== $previous_status && 'new' !== $previous_status ) {
		$post_name = wp_add_trashed_suffix_to_post_name_for_post( $post_ID );
	}

	$post_name = wp_unique_post_slug( $post_name, $post_ID, $post_status, $post_type, $post_parent );

	// Expected_slashed (everything!).
	$data = compact( 'post_author', 'post_date', 'post_date_gmt', 'post_content', 'post_title', 'post_excerpt', 'post_status', 'post_type', 'comment_status', 'post_password', 'post_name', 'post_modified', 'post_modified_gmt', 'post_parent', 'menu_order', 'guid' );

	
	$data  = wp_unslash( $data );
	$where = array( 'ID' => $post_ID );

	if ( $update ) {
		/**
		 * Fires immediately before an existing post is updated in the database.
		 *
		 * @since 2.5.0
		 *
		 * @param int   $post_ID Post ID.
		 * @param array $data    Array of unslashed post data.
		 */
		do_action( 'pre_post_update', $post_ID, $data );
		if ( false === $wpdb->update( $wpdb->posts, $data, $where ) ) {
			if ( $wp_error ) {
				return new WP_Error( 'db_update_error', __( 'Could not update post in the database' ), $wpdb->last_error );
			} else {
				return 0;
			}
		}
	} else {
		// If there is a suggested ID, use it if not already present.
		if ( ! empty( $import_id ) ) {
			$import_id = (int) $import_id;
			if ( ! $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE ID = %d", $import_id ) ) ) {
				$data['ID'] = $import_id;
			}
		}
		if ( false === $wpdb->insert( $wpdb->posts, $data ) ) {
			if ( $wp_error ) {
				return new WP_Error( 'db_insert_error', __( 'Could not insert post into the database' ), $wpdb->last_error );
			} else {
				return 0;
			}
		}
		$post_ID = (int) $wpdb->insert_id;

		// Use the newly generated $post_ID.
		$where = array( 'ID' => $post_ID );
	}

	if ( empty( $data['post_name'] ) && ! in_array( $data['post_status'], array( 'draft', 'pending', 'auto-draft' ) ) ) {
		$data['post_name'] = wp_unique_post_slug( sanitize_title( $data['post_title'], $post_ID ), $post_ID, $data['post_status'], $post_type, $post_parent );
		$wpdb->update( $wpdb->posts, array( 'post_name' => $data['post_name'] ), $where );
		clean_post_cache( $post_ID );
	}

	$iurrent_guid = get_post_field( 'guid', $post_ID );

	// Set GUID.
	if ( ! $update && '' == $iurrent_guid ) {
		$wpdb->update( $wpdb->posts, array( 'guid' => get_permalink( $post_ID ) ), $where );
	}

	clean_post_cache( $post_ID );

	if ( $update ) {

		$post_after = get_post( $post_ID );

		/**
		 * Fires once an existing post has been updated.
		 *
		 * @since 3.0.0
		 *
		 * @param int     $post_ID      Post ID.
		 * @param WP_Post $post_after   Post object following the update.
		 * @param WP_Post $post_before  Post object before the update.
		 */
		do_action( 'post_updated', $post_ID, $post_after, $post_before );
	}

	return $post_ID;
}

/**
 * Attach image to product
 *
 * @params  $thumb (string) - name or url of image
 * @author	wpStore
 * @since	2.7
 *
 * @param $thumb - Can be the file name or the full path to the file
 */	
function wpsl_insert_thumb( $thumb ) {
	global $wpdb;
	
	// check if the image exists
	$data = pathinfo( $thumb );
	if ( isset( $data['filename'] ) ) {
		$name = $data['filename'];
		$img  = $wpdb->get_results( "SELECT * FROM $wpdb->posts WHERE post_type = 'attachment' AND post_title='$name'" );
	}
	
	// если существует возвращаем ID
	if ( $img ) {
		return $img[0]->ID;
	}
	
	// если передаётся url картинки и в нём нет текущего домена, считаем, что загружаем со стороннего ресурса
	if ( filter_var( $thumb, FILTER_VALIDATE_URL ) !== FALSE && strpos( $thumb, get_option( 'siteurl' ) ) === false ) {
		
		$tmp = download_url( $thumb );
		
		// корректируем имя файла в строках запроса.
		preg_match( '/[^?]+\.(jpg|jpe|jpeg|gif|png)/i', $thumb, $matches );
		$file = array(
			'name'     => basename( $matches[0] ),
			'tmp_name' => $tmp,
			'size'     => filesize( $tmp ),
		);
		$thumbnail_id = media_handle_sideload( $file, 0 );
		
		// удалим временный файл
		@unlink( $tmp );
		
		return $thumbnail_id;
	}
	return false;
}

/**
 * Get product examples for import
 *
 * @author	wpStore
 * @since	2.7
 *
 * @param $attach_id - attachment id
 */	
function wpsl_get_examples( $attach_id, $delimiter, $enclosure ) {
	$examples = array();
	$file = fopen( wp_get_attachment_url( $attach_id ), 'r' );
	if ( $file ) {
		$i = 0;
		for( $x = 0; $x < 100; $x++ ) {
			if ( false !== ( $row = fgetcsv( $file, 9999, $delimiter, $enclosure ) ) ) {
				foreach ( $row as $k => $val ) {
					if ( $val && $i == $k ) {
						if ( $i == count( $row ) ) {
							break;
						}
						$examples[$i] = $val;
						$i++;
					}
				}
			}
		}
	} else {
		print_r( error_get_last() );
		wp_die();
	}
	return $examples;
}


/**
 * Show import form
 *
 * @author	wpStore
 * @since	2.7
 *
 * @param $examples - 
 */	
function wpsl_import_form( $examples ) {
	
	$fields = array(
		''             => __( 'No import', 'wpsl' ),
		'id'           => __( 'Product id', 'wpsl' ),
		'type-product' => __( 'Product type', 'wpsl' ),
		'post_title'   => __( 'Name', 'wpsl' ),
		'post_excerpt' => __( 'Short excerpt', 'wpsl' ),
		'post_content' => __( 'Content', 'wpsl' ),
		'product_cat'  => __( 'Category', 'wpsl' ),
		'product_tag'  => __( 'Tag', 'wpsl' ),
		'_digital'     => __( 'Digital product', 'wpsl' ),
		'hit_product'  => __( 'Hit', 'wpsl' ),
		'_sku'         => __( 'Sku', 'wpsl' ),
		'_thumbnail_id'=> __( 'Thumbnail', 'wpsl' ),
		'_product_image_gallery'=> __( 'Gallery', 'wpsl' ),
		'price'        => array(
			'_regular_price' => __( 'Regular price', 'wpsl' ),
			'_sale_price'    => __( 'Sale price', 'wpsl' ),
		),
		'dimensions'   => array(
			'_weight' => __( 'Weight', 'wpsl' ),
			'_length' => __( 'Length', 'wpsl' ),
			'_width'  => __( 'Width', 'wpsl' ),
			'_height' => __( 'Height', 'wpsl' ),
		),
		'atts'   => array(
			'individual'  => __( 'Individual attribute', 'wpsl' ),
			'global'      => __( 'Global attribute', 'wpsl' ),
		),
	);
	
	$html = '';
	foreach( $examples as $field ) {
		$html .= '<tr>';
		$html .= '<td class="">
				<div class="description">' . __( 'Example', 'wpsl' ) . ': <code>' . $field . '</code></div>
			  </td>';
		$html .= '<td class="">';
		$html .= '<select class="wpsl-select-field" name="fields[]">';
		foreach ( $fields as $k => $vals ) {
			if ( is_array( $vals ) ) {
				if ( $k == 'price' ) {
					$title = __( 'Price', 'wpsl' );
				} elseif ( $k == 'dimensions' ) {
					$title = __( 'Dimensions', 'wpsl' );
				} elseif ( $k == 'atts' ) {
					$title = __( 'Attributes', 'wpsl' );
				}
				$html .= '<optgroup label="' . $title . '">';
				foreach ( $vals as $m => $f ) {
					$html .= '<option value="' . $m . '">' . $f . '</option>';
				}
				$html .= '</optgroup>';
			} else {
				$html .= '<option value="' . $k . '">' . $vals . '</option>';
			}
		}
		$html .= '</select>';
		$html .= '</td>';
		$html .= '</tr>';
	}
	return $html;
}