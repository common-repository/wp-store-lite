<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * wpStore
 *
 * Widget for products filter
 *
 * @author	wpStore
 * @since	2.2
 */
class WPSL_Widget_Filter extends WP_Widget {
 
	/**
	 * Create
	 */
	function __construct() {
		parent::__construct( 
			'wpsl_product_filter', 
			'02 WPSL: ' . __( 'Products filter', 'wpsl' ),
			array(
				'description' => __( 'Allows display the filter products by attributes', 'wpsl' )
			)
		);
	}
 
	/**
	 * Backend
	 */
	public function form( $instance ) {
		$title = $place = '';
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		if ( isset( $instance[ 'place' ] ) ) {
			$place = $instance[ 'place' ];
		}
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'wpsl' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'place' ); ?>"><?php _e( 'Place on count', 'wpsl' ); ?></label>
			<select name="<?php echo $this->get_field_name( 'place' ); ?>" id="<?php echo $this->get_field_id( 'place' ); ?>">
				<option value="<?php echo esc_attr( $place ); ?>"><?php echo esc_attr( $place ); ?></option>
				<option value="left"><?php _e( 'Left', 'wpsl' ); ?></option>
				<option value="right"><?php _e( 'Right', 'wpsl' ); ?></option>
				<option value="center"><?php _e( 'Center', 'wpsl' ); ?></option>
			</select>
		</p>
		<?php 
	}
 
	/**
	 * Update widget
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['place'] = strip_tags( $new_instance['place'] );
		return $instance;
	}
 
	/**
	 * Frontend
	 */
	public function widget( $args, $instance ) {
		if( is_tax( 'product_cat' ) || wp_doing_ajax() ) {
		
			$title = apply_filters( 'widget_title', $instance['title'] ); // к заголовку применяем фильтр ( необязательно )
	 
			echo $args['before_widget'];
	 
			if ( ! empty( $title ) ) {
				echo $args['before_title'] . $title . $args['after_title'];
			}
				
			$place = isset( $instance['place'] ) ? $instance['place'] : 'left';
			
			//$start = microtime( true );
			echo wpsl_get_template_html( 'widgets', 'product-filter' );
			//echo '<p style="width: 100%;">Время обработки всех данных: ' . ( microtime( true ) - $start ) . ' sec.</p>';
			
			echo $args['after_widget'];
		}
	}
}


/*
 * Register widget
 */
add_action( 'widgets_init', 'wpsl_product_filter_widget' );
function wpsl_product_filter_widget() {
	register_widget( 'WPSL_Widget_Filter' );
}