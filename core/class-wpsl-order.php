<?php
/**
 * Класс обработки заказа
 */
class WPSL_Order {
	
	/**
	 * Корзина
	 * @var WPSL_Cart
	 */
	 private $cart;
	 
	/**
	 * Способ доставки
	 * @var string
	 */
	 private $deliveryType;

	/**
	 * Код заказа
	 * @var string
	 */
	 private $orderId;

	/**
	 * Параметр формы UserName
	 * @var WPSL_Cart
	 */
	const USER_NAME = 'username';

	/**
	 * Параметр формы UserEmail
	 * @var WPSL_Cart
	 */
	const USER_EMAIL = 'useremail';

	/**
	 * Параметр формы UserPhone
	 * @var WPSL_Cart
	 */
	const USER_PHONE = 'userphone';
	
	/**
	 * Параметр формы UserPhone
	 * @var WPSL_Cart
	 */
	const USER_SHIPPING_TYPE = 'usershipping';

	/**
	 * Параметр формы UserComment
	 * @var WPSL_Cart
	 */
	const USER_COMMENT = 'usercomment';
	
	/**
	 * Параметр формы UserPayment
	 * @var WPSL_Cart
	 */
	const USER_PAYMENT = 'userpayment';
	
	/**
	 * Конструктор класса
	 */	
	public function __construct() {
		$this->cart = WPSL_Cart::create();
		$this->cartURL = get_permalink( wpsl_opt( 'order_page' ) );
		$this->deliveryType = 0;
		$this->orderId = '';
	}
	
	/**
	 * Fill elements
	 */	
	public function get_fields( $fields ) {
		$content = '';
		foreach ( $fields as $field ) {
		
			if ( $field['type'] == 'empty' ) continue;
		
			$is_required = ( isset( $field['required'] ) && $field['required'] == true ) ? 'required data-validate' : '';
		
			$content .= '<div class="wpsl-order__row ' . $field['class'] . '">';
			switch ( $field['type'] ) {
				case('text'):
					$content .= '<label for="' . $field['name'] . '">' . $field['title'] . '</label>
					<input id="' . $field['name'] . '" type="text" name="' . $field['name'] . '" value="' . $field['value'] . '" placeholder="' . $field['placeholder'] . '" ' . $is_required . ' />';
					break;
				case('radio'):
					$i = 1;
					if ( $field['args'] && is_array( $field['args'] ) ) {
						foreach ( $field['args'] as $elem ) {
							$is_required = ( isset( $field['required'] ) && $field['required'] == 1 && $i == 1 ) ? 'required data-validate' : '';
							$content .= '<input type="radio" id="' . $field['name'] . '-' . $i . '" value="' . $elem . '" name="' . $field['name'] . '" ' . $is_required . ' />
							<label for="' . $field['name'] . '-' . $i . '">' . $elem . '</label>';
							$i++;
						}
					}
					unset( $i );
					break;
				case('button'):
					$i = 1;
					if ( $field['args'] && is_array( $field['args'] ) ) {
						foreach ( $field['args'] as $k => $val ) {
							$content .= '<label for="' . $field['name'] . '-' . $i . '" class="wpsl-order__row_col">';
							$is_required = ( isset( $field['required'] ) && $field['required'] == 1 && $i == 1 ) ? 'required data-validate' : '';
							$content .= '<div class="name"><input type="radio" id="' . $field['name'] . '-' . $i . '" value="' . $k . '" name="' . $field['name'] . '" ' . $is_required . ' />' . $val['name'] . '<img src="' . $val['img'] . '" alt="' . $val['name'] . '" /></div>
							<div class="desc">' . $val['desc'] . '</div>';
							$i++;
							$content .= '</label>';
						}
					}
					unset( $i );
					break;
				case('checkbox'):
					$content .= '<input id="' . $field['name'] . '" type="checkbox" name="' . $field['name'] . '" value="' . $field['value'] . '" ' . $is_required . checked( $field['value'], true, false ) . ' />
					<label for="' . $field['name'] . '">' . $field['title'] . '</label>';
					break;
				case('email'):
					$content .= '<label for="' . $field['name'] . '">' . $field['title'] . '</label>
					<input id="' . $field['name'] . '" type="email" name="' . $field['name'] . '" value="' . $field['value'] . '" placeholder="' . $field['placeholder'] . '" ' . $is_required . ' />';
					break;
				case('sms'):
					$content .= '<label for="sms_password">' . $field['placeholder'] . '</label>
					<input id="sms_password" type="text" name="sms_password" value="' . $field['value'] . '" placeholder="' . $field['placeholder'] . '" ' . $is_required . ' />
					<span id="sms-send-code" class="update">' . $field['title'] . '</span>
					<div id="sms-verification"></div>';
					break;
				case ('select'):
					$content .= '<label for="' . $field['name'] . '">' . $field['title'] . '</label>
					<select id="' . $field['name'] . '" type="email" name="' . $field['name'] . '">';
					foreach( $field['value'] as $key => $val ){
						$content .= '<option value="' . $key . '">' . $val . '</option>';
					}
					$content .= '</select>';
					break;
				case ('textarea'):
					$content .= '<label for="' . $field['name'] . '">' . $field['title'] . '</label>
					<textarea id="' . $field['name'] . '" type="email" name="' . $field['name'] . '" placeholder="' . $field['placeholder'] . '" ' . $is_required . '>' . $field['value'] . '</textarea>';
					break;
				case('hidden'):
					$content .= '<input id="' . $field['name'] . '" type="hidden" name="' . $field['name'] . '" value="' . $field['value'] . '" />';
					break;
				case('submit'):
					$content .= '<input type="submit" name="submit" value="' . $field['value'] . '" />';
					break;
				case('custom'):
					$content .= $field['fill'];
					break;
			}
			if ( isset( $field['notice'] ) && $field['notice'] != '' ) {
				$content .= '<div class="wpsl-notice wpsl-hidden">' . $field['notice'] . '</div>';
			}
			$content .= '</div>';
		}
		return $content;
	}
	
	/**
	 * Обработка обращения к форме заказа
	 */	
	public function handle() {
		
		/**
		 * Add product to cart
		 */
		if ( isset( $_GET['id'] ) ) {
			$this->cart->add( $_GET );
		}
		
		/**
		 * Before adding a new order, check SMS
		 * If sending SMS is disabled, skip the step
		 */
		$code_from_sms = isset( $_POST['sms_password'] ) ? wpsl_clean( $_POST['sms_password'] ) : '';
		if ( wpsl_opt( 'sms_confirm' ) == '1' && wpsl_opt( 'sms_code' ) != $code_from_sms ) {
			return '<div class="ok" style="color: #A08000; font-weight: 900; padding: 10px; background-color: #FFE26D; border: 1px solid #FFCC00;">Не верный пароль.</div>';
		}
			
		/**
		 * Create an order form and send the data
		 */
		if ( isset( $_POST[WPSL_MODE] ) && $_POST[WPSL_MODE] == WPSL_CHECKOUT ) {
			
			// Получение данных
			$name    = isset( $_POST[self::USER_NAME] ) ? wpsl_clean( $_POST[self::USER_NAME] ) : '';
			$phone   = isset( $_POST[self::USER_PHONE] ) ? wpsl_clean( $_POST[self::USER_PHONE] ) : '';
			$comment = isset( $_POST[self::USER_COMMENT] ) ? wpsl_clean( $_POST[self::USER_COMMENT] ) : '';
			$payment = isset( $_POST[self::USER_PAYMENT] ) ? wpsl_clean( $_POST[self::USER_PAYMENT] ) : '';
			$this->cart->setUserData( $name, wpsl_clean( $_POST[self::USER_EMAIL] ), $phone, $comment, $payment );
			$this->deliveryType = ( isset( $_POST[self::USER_SHIPPING_TYPE] ) ) ? (int)wpsl_clean( $_POST[self::USER_SHIPPING_TYPE] ) : 0;
			
			// Если данные верны - обрабатываем заказ
			if ( $this->cart->isValid() ) {
				
				// Ищем пользователя и обновляем данные
				$user_id = wpsl_get_user( $this->cart->userEmail );
				update_user_meta( $user_id, 'first_name', $this->cart->userName );
				update_user_meta( $user_id, 'phone', $this->cart->userPhone );

				
				/**
				 * Add the order to the table of orders and fill it
				 * Set the order status
				 * Fill in the custom field of order
				 */
				$this->orderId = wp_insert_post( 
					array( 
						'post_type'		=> 'shop_order',
						'post_title'	=> wpsl_set_order_name( $user_id ),
						'post_content'	=> $this->cart->getHTML(),
						'post_status'	=> 'publish',
						'post_author'	=> $user_id
					)
				);
				wp_set_object_terms( $this->orderId, array( 'new', 'pending' ), 'wpsl_status' );
				update_post_meta( $this->orderId, 'summa', $this->cart->getTotal() );
				update_post_meta( $this->orderId, 'phone', $this->cart->userPhone );
				update_post_meta( $this->orderId, 'email', $this->cart->userEmail );
				update_post_meta( $this->orderId, 'name', $this->cart->userName );
				update_post_meta( $this->orderId, 'payment', $this->cart->userPayment );
				update_post_meta( $this->orderId, 'comments', $this->cart->userComment );
				update_post_meta( $this->orderId, 'detail', $_SESSION['WPSL_ITEMS'] );
				update_post_meta( $this->orderId, 'token', md5( uniqid( rand(), 1 ) ) );
				
				$this->cart->setUserOrder( $this->orderId );
				
				
				// Consider the shipping
				if ( $this->deliveryType > 0 ) {
					$deliveryTitle = get_the_title( $this->deliveryType );
					$deliveryPrice = ( float ) wpsl_get_meta( $this->deliveryType, 'delivery_price' );
					$this->cart->add( 'delivery', $deliveryTitle, $deliveryPrice, __( 'Delivery', 'wpsl' ), $deliveryTitle );
					$delivery_id = get_page_by_title( $deliveryTitle, '', 'delivery' );
					
					update_post_meta( $this->orderId, 'delivery_type', $delivery_id->ID );
				}
				
				// Send email to customer and admins
				set_query_var( 'wpsl_order_id', $this->orderId );
				wpsl_mail( 'admin', wpsl_opt( 'new_order_email_admin' ), wpsl_get_template_html( 'email', 'admin-order-received' ) );
				wpsl_mail( $this->cart->userEmail, wpsl_opt( 'new_order_email_customer' ), wpsl_get_template_html( 'email', 'customer-order-received' ) );
				
				// выполняем команды при успешном заказе из сторонних расширений
				do_action( 'wpsl_order_checkout _successful', $this->orderId, $_SESSION['WPSL_ITEMS'] );

				// Order cleaning
				$this->cart->clear();
				
				// Redirect to payment page
				if ( wpsl_opt( 'payment_methods' ) == '1' ) {
					wp_safe_redirect( get_permalink( wpsl_opt( 'payment_page' ) ) . '?payment=process&order_id=' . $this->orderId );
					exit;
				} else {
					wp_safe_redirect( get_permalink( wpsl_opt( 'order_page' ) ) . '?order=sended' );
					exit;
				}
			}
		}
	}
	
	/**
	 * Возвращает форму заказа
	 * @return string HTML код формы заказа
	 */	
	public function getHTML() {
	
		/**
		 * If cart is empty - display a message
		 */
		if ( $this->cart->isEmpty() && isset( $_GET['order'] ) && $_GET['order'] == 'sended' )
			return wpsl_get_template_html( 'order', 'order-sended' );
	
		/**
		 * If cart is empty - display a message
		 */
		if ( $this->cart->isEmpty() )
			return wpsl_get_template_html( 'cart', 'cart-empty' );
		
		/**
		 * Hook wpsl_before_order_form
		 */
		$output = apply_filters( 'wpsl_before_order_form', $output = '' );
		
		/**
		 * Get order form
		 */
		$output .= wpsl_get_template_html( 'order', 'order-form' );
		
		/**
		 * Hook wpsl_after_order_form
		 */
		$output = apply_filters( 'wpsl_after_order_form', $output );

		return $output;
	}
}