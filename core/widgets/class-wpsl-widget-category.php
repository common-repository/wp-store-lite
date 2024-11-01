<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Widget for output categories
 *
 * @author	wpStore
 * @since	2.2
 */
class WPSL_Widget_Category extends WP_Widget {
 
	/**
	 * Create
	 */
	function __construct() {
		parent::__construct( 
			'wpsl_category', 
			'03 WPSL: ' . __( 'Products sections', 'wpsl' ),
			array(
				'description' => __( 'Display sections of product in your online store', 'wpsl' )
			)
		);
	}
 
	/**
	 * Backend
	 */
	public function form( $instance ) {
		
		$title = $hide_in_cat = $hide_in_front = '';
		
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		if ( isset( $instance[ 'hide_in_cat' ] ) ) {
			$hide_in_cat = $instance[ 'hide_in_cat' ];
		}
		if ( isset( $instance[ 'hide_in_front' ] ) ) {
			$hide_in_front = $instance[ 'hide_in_front' ];
		}
		?>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'wpsl' ); ?></label> 
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_name( 'hide_in_cat' ); ?>">
				<input type="checkbox" value="<?php _e( 'Hide in category page', 'wpsl' ); ?>" name="<?php echo $this->get_field_name( 'hide_in_cat' ); ?>" id="<?php echo $this->get_field_name( 'hide_in_cat' ); ?>" <?php checked( $hide_in_cat ); ?>>
				<?php _e( 'Hide in category page', 'wpsl' ); ?>
			</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_name( 'hide_in_front' ); ?>">
				<input type="checkbox" value="<?php _e( 'Hide in front page', 'wpsl' ); ?>" name="<?php echo $this->get_field_name( 'hide_in_front' ); ?>" id="<?php echo $this->get_field_name( 'hide_in_front' ); ?>" <?php checked( $hide_in_front ); ?>>
				<?php _e( 'Hide in front page', 'wpsl' ); ?>
			</label>
		</p>
		
		<?php 
	}
 
	/**
	 * Update widget
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title']		 = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['hide_in_cat']   = isset( $new_instance['hide_in_cat'] ) ? ( bool ) $new_instance['hide_in_cat'] : false;
		$instance['hide_in_front'] = isset( $new_instance['hide_in_front'] ) ? ( bool ) $new_instance['hide_in_front'] : false;
		return $instance;
	}
	
	/**
	 * Frontend
	 */
	public function widget( $args, $instance ) {
		
		$title = apply_filters( 'widget_title', $instance['title'] );
		
		$columns = isset( $instance['columns'] ) ? $instance['columns'] : false;
		
		$instance['hide_in_cat'] = isset( $instance['hide_in_cat'] ) ? ( bool ) $instance['hide_in_cat'] : false;
		$instance['hide_in_front'] = isset( $instance['hide_in_front'] ) ? ( bool ) $instance['hide_in_front'] : false;
		
		if ( $instance['hide_in_front'] == false && !is_tax( 'product_cat' ) || is_tax( 'product_cat' ) && $instance['hide_in_cat'] == false ) {
		
			echo $args['before_widget'];
	 
			if ( ! empty( $title ) ) {
				echo $args['before_title'] . $title . $args['after_title'];
			}
			
			echo wpsl_get_template_html( 'widgets', 'product-categories' );
			
			echo $args['after_widget'];
		
		}
	}
}


/*
 * Register widget
 */
add_action( 'widgets_init', 'wpsl_category_widget' );
function wpsl_category_widget() {
	register_widget( 'WPSL_Widget_Category' );
}