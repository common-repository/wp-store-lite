<?php
if ( ! defined( 'ABSPATH' ) ) exit;


if ( !is_admin() ) return;


/*
 * Disabling cropping of html tags in the category description
 * @since	2.4.5
 */
remove_filter( 'pre_term_description', 'wp_filter_kses' );
remove_filter( 'pre_term_description', 'wp_kses_data' );


/**
 * wpStore
 *
 * Edit taxonomy page in admin
 *
 * @author	http://truemisha.ru/blog/wordpress/metadannyie-v-taksonomiyah.html and https://wp-kama.ru/question/meta-i-title-dlya-rubrik
 * @since	2.2
 */
class WPSL_Terms_Metabox {
	
	private $opt;
	private $prefix;

	function __construct( $option ) {
		$this->opt    = ( object ) $option;
		$this->prefix = $this->opt->id .'_'; // префикс настроек

		foreach( $this->opt->taxonomy as $taxonomy ){
			add_action( $taxonomy . '_edit_form_fields', array( &$this, 'fill' ), 10, 2 ); // хук добавления полей
		}

		// установим таблицу в $wpdb, если её нет
		global $wpdb;
		if( ! isset( $wpdb->termmeta ) ) $wpdb->termmeta = $wpdb->prefix .'termmeta';

		add_action( 'edit_term', array( &$this, 'save' ), 10, 1 ); // хук сохранения значений полей
	}

	function fill( $term, $taxonomy ){

		foreach( $this->opt->args as $param ){
			$def   = array( 'id'=>'', 'title'=>'', 'type'=>'', 'desc'=>'', 'std'=>'', 'args'=>array() );
			$param = ( object ) array_merge( $def, $param );

			$meta_key   = $this->prefix . $param->id;
			$meta_value = get_metadata( 'term', $term->term_id, $meta_key, true ) ?: $param->std;

			echo '<tr class ="form-field">';
				echo '<th scope="row"><label for="'. $meta_key .'">'. $param->title .'</label></th>';
				echo '<td>';

				// select
		if( $param->type == 'wp_editor' ){
		  wp_editor( $meta_value, $meta_key, array( 
			'wpautop' => 1,
			'media_buttons' => false,
			'textarea_name' => $meta_key, //нужно указывать!
			'textarea_rows' => 10,
			//'tabindex'      => null,
			//'editor_css'    => '',
			//'editor_class'  => '',
			'teeny'         => 0,
			'dfw'           => 0,
			'tinymce'       => 1,
			'quicktags'     => 1,
			//'drag_drop_upload' => false
		  ) );
		}
		// select
				elseif( $param->type == 'select' ){
					echo '<select name="'. $meta_key .'" id="'. $meta_key .'">
							<option value="">...</option>';

							foreach( $param->args as $val => $name ){
								echo '<option value="'. $val .'" '. selected( $meta_value, $val, 0 ) .'>'. $name .'</option>';
							}
					echo '</select>';
					if( $param->desc ) echo '<p class="description">' . $param->desc . '</p>';
				}
				// checkbox
				elseif( $param->type == 'checkbox' ){
					echo '
						<label>
							<input type="hidden" name="'. $meta_key .'" value="">
							<input name="'. $meta_key .'" type="'. $param->type .'" id="'. $meta_key .'" '. checked( $meta_value, 'on', 0 ) .'>
							'. $param->desc .'
						</label>
					';
				}
				// textarea
				elseif( $param->type == 'textarea' ){
					echo '<textarea name="'. $meta_key .'" type="'. $param->type .'" id="'. $meta_key .'" value="'. $meta_value .'" class="large-text">'. esc_html( $meta_value ) .'</textarea>';                    
					if( $param->desc ) echo '<p class="description">' . $param->desc . '</p>';
				}
				// text
				else{
					echo '<input name="'. $meta_key .'" type="'. $param->type .'" id="'. $meta_key .'" value="'. $meta_value .'" class="regular-text">';

					if( $param->desc ) echo '<p class="description">' . $param->desc . '</p>';
				}
				echo '</td>';
			echo '</tr>';         
		}

	}

	function save( $term_id ){
		foreach( $this->opt->args as $field ){
			$meta_key = $this->prefix . $field['id'];
			if( ! isset( $_POST[ $meta_key ] ) ) continue;

			if( $meta_value = trim( $_POST[ $meta_key ] ) ){
				update_metadata( 'term', $term_id, $meta_key, $meta_value, '' );
			}
			else {
				delete_metadata( 'term', $term_id, $meta_key, '', false );
			}
		}
	}

}

add_action( 'init', 'wpsl_register_additional_term_fields' );
function wpsl_register_additional_term_fields() { 
	new WPSL_Terms_Metabox( array( 
		'id'       => 'tax',
		'taxonomy' => array( 'product_cat', 'product_tag' ),
		'args'     => array( 
			array( 
				'id'    => 'meta_title',
				'title' => __( 'SEO Title', 'wpsl' ),
				'type'  => 'text',
				'desc'  => 'Укажите альтернативное название термина для SEO.',
				'std'   => '',
			 ),
			/* array( 
				'id'    => 'meta_description',
				'title' => 'SEO Описание',
				'type'  => 'text',
				'desc'  => 'meta тег description.',
				'std'   => '',
			 ), */
			array( 
				'id'    => 'description',
				'title' => __( 'The text on the category page', 'wpsl' ),
				'type'  => 'wp_editor',
				'desc'  => __( 'Description meta tag', 'wpsl' ),
				'std'   => '',
			 ),
		 )
	 ));
}


/*
 * Get attribute constructor
 * @since	2.7
 */
add_action( 'product_cat_edit_form', 'wpsl_add_new_custom_fields', 100 );
function wpsl_add_new_custom_fields( $term ) {
	?>
	<div class="" style="width: 100%; float: left; background-color: #FFF; padding: 15px; border: 1px solid #e1e1e1; box-sizing: border-box;">
		<?php 
		$html = new WPSL_Product_Attributes_Common();
		echo $html->constructor( $term->term_id, $product_id = '' );
		?>
	</div>
	<?php
}
/* 
add_action( 'edited_product_cat', 'save_custom_taxonomy_meta', 100 );
function save_custom_taxonomy_meta( $term_id ) {
	if ( ! isset( $_POST['extra'] ) ) return;
	if ( ! current_user_can( 'edit_term', $term_id ) ) return;
	if ( 
		! wp_verify_nonce( $_POST['_wpnonce'], "update-tag_$term_id" ) && // wp_nonce_field( 'update-tag_' . $tag_ID );
		! wp_verify_nonce( $_POST['_wpnonce_add-tag'], "add-tag" ) // wp_nonce_field( 'add-tag', '_wpnonce_add-tag' );
	 ) return;

	// Все ОК! Теперь, нужно сохранить/удалить данные
	$extra = wp_unslash( $_POST['extra'] );

	foreach( $extra as $key => $val ){
		// проверка ключа
		$_key = sanitize_key( $key );
		if( $_key !== $key ) wp_die( 'bad key'. esc_html( $key ) );

		// очистка
		if( $_key === 'tag_posts_shortcode_links' )
			$val = sanitize_textarea_field( strip_tags( $val ) );
		else
			$val = sanitize_text_field( $val );

		// сохранение
		if( ! $val )
			delete_term_meta( $term_id, $_key );
		else
			update_term_meta( $term_id, $_key, $val );
	}

	return $term_id;
} */



/*
 * Smart title in category page of products
 * @since	2.4.5
 */
add_filter( 'pre_get_document_title', 'wpsl_change_tax_title' );
function wpsl_change_tax_title( $title ) {
	if ( current_theme_supports( 'title-tag' ) ) {
		if ( !is_post_type_archive( 'product' ) && is_tax( 'product_cat' ) || is_tax( 'product_tag' ) ) {
			$term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
			$text = get_term_meta( $term->term_id, 'tax_meta_title', true );
			if ( $text != '' ) {
				$title = $text . ' | ' . get_bloginfo( 'name' );
			}
		}
	}
	return $title;
}