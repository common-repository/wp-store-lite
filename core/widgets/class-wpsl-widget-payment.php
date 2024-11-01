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
class WPSL_Widget_Payment extends WP_Widget {
 
	/*
	 * Create
	 */
	function __construct() {
		parent::__construct( 
			'wpsl_paymeint_icons', 
			'04 WPSL: ' . __( 'Payment icons', 'wpsl' ),
			array(
				'description' => __( 'Displays the icons of the payment methods in your online store', 'wpsl' )
			)
		);
	}
	
	/**
	 * Get payment methods
	 *
	 * @since	2.7.0
	 */
	public function methods() {
		$methods = apply_filters( 'wpsl_get_payment_icons',
			array(
				'cash' => array(
					'name'   => __( 'Cash', 'wpsl' ),
					'img'    => WPSL_URL . '/assets/img/coins.svg',
				),
				'visa' => array(
					'name'   => __( 'Visa', 'wpsl' ),
					'img'    => WPSL_URL . '/assets/img/visa.svg',
				),
				'mastercard' => array(
					'name'   => __( 'MasterCard', 'wpsl' ),
					'img'    => WPSL_URL . '/assets/img/mastercard.svg',
				),
				'maestro' => array(
					'name'   => __( 'Maestro', 'wpsl' ),
					'img'    => WPSL_URL . '/assets/img/maestro.svg',
				),
				'mir' => array(
					'name'   => __( 'Mir', 'wpsl' ),
					'img'    => WPSL_URL . '/assets/img/mir.svg',
				),
				'ym' => array(
					'name'   => __( 'Yandex.Money', 'wpsl' ),
					'img'    => WPSL_URL . '/assets/img/yandex-money.svg',
				),
				'webmoney' => array(
					'name'   => __( 'WebMoney', 'wpsl' ),
					'img'    => WPSL_URL . '/assets/img/webmoney.svg',
				),
				'paypal' => array(
					'name'   => __( 'PayPal', 'wpsl' ),
					'img'    => WPSL_URL . '/assets/img/paypal.svg',
				),
				'qiwi' => array(
					'name'   => __( 'Qiwi', 'wpsl' ),
					'img'    => WPSL_URL . '/assets/img/qiwi.svg',
				),
				'mailru' => array(
					'name'   => __( 'Mailru', 'wpsl' ),
					'img'    => WPSL_URL . '/assets/img/mailru.svg',
				),
				'cash_on_delivery' => array(
					'name'   => __( 'Cash on delivery', 'wpsl' ),
					'img'    => WPSL_URL . '/assets/img/russian-post.svg',
				),
				'bank_transfer' => array(
					'name'   => __( 'Bank transfer', 'wpsl' ),
					'img'    => WPSL_URL . '/assets/img/bank.svg',
				),
			)
		);
		return $methods;
	}
 
	/**
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
		<p>
			<label for="<?php echo $this->get_field_id( 'columns' ); ?>"><?php _e( 'Count of columns', 'wpsl' ); ?></label>
			<select name="<?php echo $this->get_field_name( 'columns' ); ?>" id="<?php echo $this->get_field_id( 'columns' ); ?>">
				<option value="<?php echo esc_attr( $columns ); ?>"><?php echo esc_attr( $columns ); ?></option>
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
		</p>
		<div class="wps-select-payment">
		<?php 
		foreach ( $this->methods() as $key => $method ) {
			$check = isset( $instance[$key] ) && $instance[$key] ? true : false; ?>
			<label class="wps-cash <?php echo $check == true ? 'active' : ''; ?>" for="<?php echo $this->get_field_name( $key ); ?>">
				<input type="checkbox" value="1" name="<?php echo $this->get_field_name( $key ); ?>" id="<?php echo $this->get_field_name( $key ); ?>" <?php checked( $check ); ?>>
				<img src="<?php echo $method['img']; ?>">
				<?php echo $method['name']; ?>
			</label>
		<?php } ?>
		</div>
		<?php 
	}
 
	/**
	 * Update widget
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title']   = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['columns'] = strip_tags( $new_instance['columns'] );
		foreach ( $this->methods() as $key => $method ) {
			$instance[$key] = isset( $new_instance[$key] ) ? ( bool ) $new_instance[$key] : false;
		}
		return $instance;
	}
 
	/**
	 * Frontend
	 */
	public function widget( $args, $instance ) {
		
		$title = apply_filters( 'widget_title', $instance['title'] );
		$columns = isset( $instance['columns'] ) ? $instance['columns'] : false;
		$payment = '';
		foreach ( $this->methods() as $key => $method ) {
			if ( isset( $instance[$key] ) && $instance[$key] == 1 ) {
				$payment .= '<div class="wpsl-payment-icon wpsl-col-' . $columns . '" ><img src="' . $method['img'] . '" title="' . $method['name'] . '" alt="' . $method['name'] . '"></div>';
			}
		}
		echo $args['before_widget'];
 
		if ( ! empty( $title ) )
			echo $args['before_title'] . $title . $args['after_title'];
 
		echo $payment;
		
		echo $args['after_widget'];
	}
}


/*
 * Register widget
 */
add_action( 'widgets_init', 'wpsl_payment_widget' );
function wpsl_payment_widget() {
	register_widget( 'WPSL_Widget_Payment' );
}