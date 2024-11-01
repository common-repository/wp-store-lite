<?php
/**
 * Adds the ability to upload images for product categories and labels
 *
 * Пример получения ID и URL картинки термина:
 * $image_id = get_term_meta( $term_id, '_thumbnail_id', 1 );
 * $image_url = wp_get_attachment_image_url( $image_id, 'thumbnail' );
 *
 * @author: Kama ( http://wp-kama.ru )
 *
 * @ver: 2.7
 */


if ( ! defined( 'ABSPATH' ) ) exit;


if ( !is_admin() ) return;


class WPSL_Terms_Images {

	/**
	 * For which taxonomies to include the code. By default, all public
	 */
	static $taxes = array( 'product_cat', 'product_tag' ); // пример: array( 'category', 'product_tag' );
 
	/**
	 * The name of the meta key
	 */
	static $meta_key = '_thumbnail_id';

	/**
	 * Construct
	 */
	public function __construct(){
		if( isset( $GLOBALS['WPSL_Terms_Images'] ) ) return $GLOBALS['WPSL_Terms_Images']; // once

		$taxes = self::$taxes ? self::$taxes : get_taxonomies( array( 'public'=>true ), 'names' );

		foreach( $taxes as $taxname ){
			add_action( "{$taxname}_add_form_fields",   array( &$this, 'add_term_image' ),     10, 2 );
			add_action( "{$taxname}_edit_form",         array( &$this, 'update_term_image' ),  10, 2 );
			add_action( "created_{$taxname}",           array( &$this, 'save_term_image' ),    10, 2 );
			add_action( "edited_{$taxname}",            array( &$this, 'updated_term_image' ), 10, 2 );

			add_filter( "manage_edit-{$taxname}_columns",  array( &$this, 'add_image_column' ) );
			add_filter( "manage_{$taxname}_custom_column", array( &$this, 'fill_image_column' ), 10, 3 );
		}
	}

	/**
	 * Fields when creating a term
	 */
	public function add_term_image( $taxonomy ) {
		
		wp_enqueue_media();

		add_action( 'admin_print_footer_scripts', array( &$this, 'add_script' ), 99 );
		$this->add_css();
		?>
		<div class="form-field term-group">
			<label><?php _e( 'Image', 'wpsl' ); ?></label>
			<div class="term__image__wrapper">
				<a class="termeta_img_button" href="#">
					<img src="<?php echo wpsl_opt( 'placeholder_image', WPSL_URL . '/assets/img/no-photo.png' ); ?>" alt="">
				</a>
				<input type="button" class="button button-secondary termeta_img_remove" value="<?php _e( 'Remove', 'wpsl' ); ?>" />
			</div>

			<input type="hidden" id="term_imgid" name="term_imgid" value="" >
		</div>
		<?php
	}

	/**
	 * Fields when editing a term
	 */
	public function update_term_image( $term, $taxonomy ){
		
		wp_enqueue_media();

		add_action( 'admin_print_footer_scripts', array( &$this, 'add_script' ), 99 );

		$image_id = get_term_meta( $term->term_id, self::$meta_key, true );
		$image_url = $image_id ? wp_get_attachment_image_url( $image_id, 'thumbnail' ) : wpsl_opt( 'placeholder_image', WPSL_URL . '/assets/img/no-photo.png' );
		$this->add_css();
		?>
		<div class="form-field term-group-wrap">
			<h2><?php _e( 'Image', 'wpsl' ); ?></h2>
			<div>
				<div class="term__image__wrapper">
					<a class="termeta_img_button" href="#">
						<?php echo '<img src="'. $image_url .'" alt="">'; ?>
					</a>
					<input type="button" class="button button-secondary termeta_img_remove" value="<?php _e( 'Remove', 'wpsl' ); ?>" />
				</div>

				<input type="hidden" id="term_imgid" name="term_imgid" value="<?php echo $image_id; ?>">
			</div>
		</div>
		<div class="clear"></div>
		<?php
	}

	/**
	 * Add styles
	 */
	public function add_css(){
		?><style>
		.column-image, .image{
			width: 45px !important;
			text-align: center !important;
		}
		tbody .column-description {
			max-height: 54px !important;
			overflow: hidden;
			width: 100%;
			float: left;
			display: table-caption;
		}
		#edittag{max-width: 100% !important;}
		#edittag .form-table{
			width: calc( 100% - 340px );
			margin-right: 20px;
			padding: 15px;
			box-sizing: border-box;
			margin-bottom: 20px;
			border: 1px solid #e1e1e1;
			float: left;
			margin-top: 0;
			background: #fff;
			display: inline-block;
		}
		.term-group-wrap{
			width: 320px;
			margin-bottom: 25px;
			display: inline-block;
			float: left;
			box-sizing: border-box;
			padding: 0 15px 15px;
			background-color: #fff;
			border: 1px solid #e5e5e5;
		}
		.termeta_img_button{ display:inline-block; margin-right:1em; }
		.termeta_img_button img{ display:block; float:left; margin:0; padding:0; width:100%; height:auto; background:rgba( 0,0,0,.07 ); }
		.termeta_img_button:hover img{ opacity:.8; }
		.termeta_img_button:after{ content:''; display:table; clear:both; }
		</style><?php
	}

	/**
	 * Add script
	 */
	public function add_script(){
		// exit if not on the desired page of the taxonomy
		/* $cs = get_current_screen();
		if( ! in_array( $cs->base, array( 'edit-tags','term' ) ) || ! in_array( $cs->taxonomy, ( array ) $this->for_taxes ) )
		return; */

		?>
		<script>
		jQuery( document ).ready( function( $ ){
			var frame,
				$imgwrap = $( '.term__image__wrapper' ),
				$imgid   = $( '#term_imgid' );

			document.addEventListener('click', function(event) {
				var target = event.target;
				if(target.matches('#submit')) {
					var image = target.closest('#addtag').querySelector('.termeta_img_button img');
					image.src = '<?php echo wpsl_opt( 'placeholder_image', WPSL_URL . '/assets/img/no-photo.png' ); ?>';
					//console.log( image );
				}
			}, true);
			jQuery('#addtag').on('click', '#submit', function(){
				$(this).parents('#addtag').find('.termeta_img_button').children('img').attr( 'src', '<?php echo wpsl_opt( 'placeholder_image', WPSL_URL . '/assets/img/no-photo.png' ); ?>' );
				console.log( $(this).parents('#addtag').find('.termeta_img_button').children('img') );
			});
				
			// add
			$( '.termeta_img_button' ).click( function( ev ){
				ev.preventDefault();

				if( frame ){ frame.open(); return; }

				// задаем media frame
				frame = wp.media.frames.questImgAdd = wp.media( {
					states: [
						new wp.media.controller.Library( {
							title:    '<?php _e( 'Featured Image', 'wpsl' ); ?>',
							library:   wp.media.query( { type: 'image' } ),
							multiple: false,
							//date:   false
						} )
					],
					button: {
						text: '<?php _e( 'Set featured image', 'wpsl' ) ?>',
					}
				} );

				// select
				frame.on( 'select', function(){
					var selected = frame.state().get( 'selection' ).first().toJSON();
					if( selected ){
						$imgid.val( selected.id );
						$imgwrap.find( 'img' ).attr( 'src', selected.sizes.thumbnail.url );
					}
				} );

				// open
				frame.on( 'open', function(){
					if( $imgid.val() ) frame.state().get( 'selection' ).add( wp.media.attachment( $imgid.val() ) );
				} );

				frame.open();
			} );

			// remove
			$( '.termeta_img_remove' ).click( function(){
				$imgid.val( '' );
				$imgwrap.find( 'img' ).attr( 'src','<?php echo wpsl_opt( 'placeholder_image', WPSL_URL . '/assets/img/no-photo.png' ); ?>' );
			} );
		} );
		</script>

		<?php
	}

	## Adds an image column to the term table
	public function add_image_column( $columns ){
		$num = 1; // after the number of columns to insert
		$new_columns = array(
			'image' => '<i class="dashicons dashicons-format-gallery"></i>'        // column with dashicon
		);
		return array_slice( $columns, 0, $num ) + $new_columns + array_slice( $columns, $num );
	}
	
	## Fill image column to the term table
	public function fill_image_column( $string, $column_name, $term_id ){
		// if there is a picture
		if( $column_name == 'image' && $image_id = get_term_meta( $term_id, self::$meta_key, 1 ) ) {
			$string = '<img src="'. wp_get_attachment_image_url( $image_id, 'thumbnail' ) .'" width="50" height="50" alt="" style="border-radius:4px;" />';
		}
		return $string;
	}

	## Save the form field
	public function save_term_image( $term_id, $tt_id ){
		if( ! empty( $_POST['term_imgid'] ) && $image = ( int ) $_POST['term_imgid'] )
			add_term_meta( $term_id, self::$meta_key, $image, true );

	}

	## Update the form field value
	public function updated_term_image( $term_id, $tt_id ){
		if( isset( $_POST['term_imgid'] ) && $image = ( int ) $_POST['term_imgid'] ) {
			update_term_meta( $term_id, self::$meta_key, $image );
		} else {
			delete_term_meta( $term_id, self::$meta_key );
		}
	}

}


/*
 * Init class only in admin
 */
//add_action( 'current_screen', 'wpsl_term_meta_image_init' );
add_action( 'admin_init', 'wpsl_term_meta_image_init' );
function wpsl_term_meta_image_init(){
	$GLOBALS['WPSL_Terms_Images'] = new WPSL_Terms_Images();
}