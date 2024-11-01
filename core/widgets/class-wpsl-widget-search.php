<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Widget for paymet methods widget
 *
 * @author	wpStore
 * @since	2.2
 */
class WPSL_Widget_Search extends WP_Widget {
 
	/*
	 * Create
	 */
	function __construct() {
		parent::__construct( 
			'wpsl_smart_search', 
			'01 WPSL: ' . __( 'Smart search', 'wpsl' ),
			array(
				'description' => __( 'Displays the product search form', 'wpsl' )
			)
		);
	}
 
	/*
	 * Backend
	 */
	public function form( $instance ) {
		
		$title = $columns = '';
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		if ( isset( $instance[ 'columns' ] ) ) {
			$columns = $instance[ 'columns' ];
		}
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'wpsl' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<!--p>
			<label for="< ?php echo $this->get_field_id( 'columns' ); ?>">< ?php _e( 'Count products in search', 'wpsl' ); ?></label>
			<select name="< ?php echo $this->get_field_name( 'columns' ); ?>" id="< ?php echo $this->get_field_id( 'columns' ); ?>">
				<option value="< ?php echo esc_attr( $columns ); ?>">< ?php echo esc_attr( $columns ); ?></option>
				<option value="1">1</option>
				<option value="2">2</option>
				<option value="3">3</option>
				<option value="4">4</option>
				<option value="5">5</option>
				<option value="6">6</option>
				<option value="7">7</option>
				<option value="8">8</option>
				<option value="9">9</option>
				<option value="10">10</option>
				<option value="11">11</option>
				<option value="12">12</option>
			</select>
		</p-->
		<?php 
	}
 
	/*
	 * Update widget
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['columns'] = strip_tags( $new_instance['columns'] );
		return $instance;
	}

	/*
	 * Frontend
	 */
	public function widget( $args, $instance ) {
		
		$title = apply_filters( 'widget_title', $instance['title'] );
		$columns = isset( $instance['columns'] ) ? $instance['columns'] : false;
		
		echo $args['before_widget'];
 
		if ( ! empty( $title ) )
			echo $args['before_title'] . $title . $args['after_title'];
		
		$count = wp_count_posts( 'product' )->publish;
		$placeholder = __( 'Search among', 'wpsl' ) . ' ' . sprintf( _n( '%s product', '%s products', $count, 'wpsl' ), $count );
 
		?>
		<form id="wpsl-search" class="wpsl-search" action="<?php echo home_url( '/' ) ?>" method="get">
			<input type="search" name="s" placeholder="<?php echo $placeholder; ?>" value="<?php echo get_search_query() ?>" autocomplete="off" class="wpsl-search__text" />
			<input type="submit" class="wpsl-search__submit" value="<?php _e( 'Search', 'wpsl' ); ?> ">
			<ul class="wpsl-search__result"></ul>
		</form>
			
		<?php echo $args['after_widget'];
	}
}


/*
 * Register widget
 */
add_action( 'widgets_init', 'wpsl_smart_search_widget' );
function wpsl_smart_search_widget() {
	register_widget( 'WPSL_Widget_Search' );
}