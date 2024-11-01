<?php
/**
 * Класс корзины заказа
 */
class WPSL_Cart {
	
	/**
	 * Экземпляр класса
	 * @static
	 */
	private static $instance = NULL;

	/**
	 * Возвращает экземпляр класса
	 * @static
	 * @return WPSL_Cart
	 */
	public static function create() {
		if ( !self::$instance )
			self::$instance = new WPSL_Cart();
		return self::$instance;
	}
	
	const ITEMS		     = 'WPSL_ITEMS';
	const ID		     = 'WPSL_ID';
	const TITLE		     = 'WPSL_TITLE';
	const QUO		     = 'WPSL_QUO';
	const PRICE		     = 'WPSL_PRICE';
	const CATEGORY	     = 'WPSL_CATEGORY';
	const INFO   	     = 'WPSL_INFO';        // дополнительная информация о товаре
	const USER_EMAIL     = 'WPSL_USER_EMAIL';
	const USER_PHONE     = 'WPSL_USER_PHONE';
	const USER_NAME	     = 'WPSL_USER_NAME';
	const USER_COMMENT   = 'WPSL_USER_COMMENT';
	const USER_PAYMENT   = 'WPSL_USER_PAYMENT';
	const USER_ORDER     = 'WPSL_USER_ORDER';
	const USER_DISCOUNTS = 'WPSL_USER_DISCOUNTS';

	/**
	 * Элементы заказа
	 * @var mixed
	 */
	public $items;

	/**
	 * Конструктор класса
	 */
	private function __construct() {
		do_action( 'wpsl_start' );
		$this->items = ( isset( $_SESSION[self::ITEMS] ) ) ? $_SESSION[self::ITEMS] : array();
		$this->userName = ( isset( $_SESSION[self::USER_NAME] ) ) ? $_SESSION[self::USER_NAME] : '';
		$this->userEmail = ( isset( $_SESSION[self::USER_EMAIL] ) ) ? $_SESSION[self::USER_EMAIL] : '';
		$this->userPhone = ( isset( $_SESSION[self::USER_PHONE] ) ) ? $_SESSION[self::USER_PHONE] : '';
		$this->userComment = ( isset( $_SESSION[self::USER_COMMENT] ) ) ? $_SESSION[self::USER_COMMENT] : '';
		$this->userPayment = ( isset( $_SESSION[self::USER_PAYMENT] ) ) ? $_SESSION[self::USER_PAYMENT] : '';
		$this->userOrder = ( isset( $_SESSION[self::USER_ORDER] ) ) ? $_SESSION[self::USER_ORDER] : '';
		$this->userDiscounts = ( isset( $_SESSION[self::USER_DISCOUNTS] ) ) ? $_SESSION[self::USER_DISCOUNTS] : '';
		$this->errorMessages = array();
	}

	/**
	 * Деструктор класса
	 */
	public function __destruct() {
		$_SESSION[self::ITEMS] = $this->items;
		$_SESSION[self::USER_NAME] = $this->userName;
		$_SESSION[self::USER_EMAIL] = $this->userEmail;
		$_SESSION[self::USER_PHONE] = $this->userPhone;
		$_SESSION[self::USER_COMMENT] = $this->userComment;
		$_SESSION[self::USER_PAYMENT] = $this->userPayment;
		$_SESSION[self::USER_ORDER] = $this->userOrder;
		$_SESSION[self::USER_DISCOUNTS] = $this->userDiscounts;
	}

	/**
	 * Имя пользователя
	 * @var string
	 */
	public $userName;
	 
	/**
	 * E-mail пользователя
	 * @var string
	 */
	public $userEmail;
	 
	/**
	 * Телефон пользователя
	 * @var string
	 */
	public $userPhone;	
	 
	/**
	 * Комментарий пользователя
	 * @var string
	 */
	public $userComment;
	 
	 /**
	  * Метод оплаты пользователя
	  * @var string
	  */
	 public $userPayment;
	 
	 /**
	 * Id последнего заказа пользователя
	 * @var int
	 */
	 public $userOrder;
	 
	 /**
	  * Id всех скидок к пользователю
	  * @var array
	  */
	 public $userDiscounts;
	 
	/**
	 * Сообщение об ошибке
	 * @var string
	 */
	public $errorMessages;

	/**
	 * Сохраняет данные пользователя
	 * @param string name наименование товара
	 * @param string email наименование товара
	 * @param string phone наименование товара
	 * @param string comment наименование товара
	 */
	public function setUserData( $name, $email, $phone, $comment = '', $payment = '' ) {
		$this->userName = $name;
		$this->userEmail = $email;
		$this->userPhone = $phone;
		if ( !empty( $comment ) ) $this->userComment = $comment;
		if ( !empty( $payment ) ) $this->userPayment = $payment;
	}
	
	/**
	 * Сохраняет данные последнего заказа пользователя
	 * @param int name наименование товара
	 */
	public function setUserOrder( $order_id ) {
		$this->userOrder = $order_id;
	}
	
	/**
	 * Получает id последнего заказа пользователя
	 * @param int id заказа
	 */
	public function get_order_id() {
		return $this->userOrder;
	}
	
	/**
	 * Проверка что в корзине есть товары
	 * @return bool true - товаров нет
	 */
	public function isEmpty() {
		return ( count( $this->items ) == 0 );
	}

	/**
	 * Проверка к готовности заказа
	 * @return bool true - заказ можно оформлять
	 */
	public function isValid() {
		$this->errorMessages = array();
		
		// Заполнение полей
		$fields = array( 'userName', 'userPhone', 'userComment' );
		foreach ( $fields as $field ) {
			if ( empty( $this->userName ) && wpsl_opt( $field ) == 'required' ) {
				$this->errorMessages[] = __( 'Please enter required data!', 'wpsl' );
			}
		}
		
		// Заполнение email
		if ( empty( $this->userEmail ) )
			$this->errorMessages[] = __( 'Please enter required email!', 'wpsl' );

		// Правильность E-mail
		if ( !preg_match( '/^([a-zA-Z0-9_\.-]+)@([a-z0-9_\.-]+)\.([a-z\.]{2,6})$/', $this->userEmail ) )
			$this->errorMessages[] = __( 'Please specify correct E-mail!', 'wpsl' );

		// Правильность телефона
		if ( !preg_match( '/^\+?[ \-\(\)0-9]{11,20}$/', $this->userPhone ) && wpsl_opt( $this->userPhone ) == 'required' )
			$this->errorMessages[] = __( 'Please specify correct phone!', 'wpsl' );
			
		// Корзина пуста
		if ( $this->isEmpty() )
			$this->errorMessages[] = __( 'Cart is empty', 'wpsl' );
		
		if ( count( $this->errorMessages ) > 0 ) {
			return false;
		}

		return true;
	}

	/**
	 * Output a list of errors in the cart
	 */
	public function errors_list() {
		if ( isset( $this->cart->errorMessages ) && count( $this->cart->errorMessages > 0 ) ) {
			$html = '<ul class="error">';
			foreach ( $this->cart->errorMessages as $error ) {
				$html .= '<li>' . $error . '</li>';
			}
			$html .= '</ul>';
			return apply_filters( 'wpsl_cart_errors_list', $html, $this->cart->errorMessages );
		}
	}

	/**
	 * Добавляет в заказ очередной элемент
	 *
	 * @param array $get Data from $_GET request
	 */
	public function add( $get, $count = 1 ) {
		
		// Если пользователь авторизован, пытаемся подставить пустые данные
		global $current_user;
		if ( is_user_logged_in() ) {
			if ( empty( $this->userName ) )
				$this->userName = $current_user->display_name;
			if ( empty( $this->userEmail ) )
				$this->userEmail = $current_user->user_email;
			if ( empty( $this->userPhone ) )
				$this->userPhone = esc_attr( get_the_author_meta( 'phone', $current_user->user_ID ) );
		}
		
		
		// Ищем все подходящие скидки для пользователя
		$discount = WPSL_Discount::getInstance();
		$this->userDiscounts = $discount->get_user_discounts();

		
		if ( !isset( $get['id'] ) ) return;

		$id      = (int)$get['id'];
		$product = get_post( $id );
		$category = '';
		$taxonomies = get_object_taxonomies( $product->post_type );
		foreach ( $taxonomies as $taxonomy ) {
			if ( strpos( $taxonomy, 'tag' ) !== FALSE ) continue;
			// Берем элементы этой таксономии
			$categories = get_the_terms( $id, $taxonomy );
			$category = is_array( $categories ) || is_object( $categories ) && ( count( $categories ) > 0 ) ? $categories[0]->name : '';
			// Следующие таксономии не рассматриваем
			break;
		}
		
		// Нормализуем цену
		$p = ( isset( $get['_price'] ) && $get['_price'] != '' ) ? $get['_price'] : wpsl_get_meta( $id, '_price' );
		$price = ( float ) preg_replace(
			// patterns
			array(
				'/[\s]/',
				'/,/'
			),
			// replacements
			array(
				'',
				' . '
			),
			$p
		);

		// Если такой элемент уже есть...
		if ( array_key_exists( $id, $this->items ) ) {
			if ( wpsl_opt( 'cart_single' ) != '1' ) {
				// Увеличим количество
				$this->items[$id][self::QUO] = $this->items[$id][self::QUO] + (int)$count;
			} else {
				// Оставляем в единичном экземпляре если выставлена настройка
				$this->items[$id][self::QUO] = 1;
			}
		} else {
			// Добавим элемент
			$this->items[$id] = array(
				self::ID       => (int)$id,
				self::TITLE    => $product->post_title,
				self::QUO	   => (int)$count,
				self::PRICE    => $price,
				self::CATEGORY => $category,
				self::INFO     => isset( $get['info'] ) ? $get['info'] : '',
			);
		}
	}

	/**
	 * Обновляем в заказе количество товаров
	 *
	 * @param string id Код продукта
	 * @param int quo Число товаров
	 */
	public function update( $id, $quo ) {
		// Если такой элемент уже есть...
		if ( array_key_exists( $id, $this->items ) ) {
			if ( $quo == 0 ) {
				// Удаляем элемент
				unset( $this->items[$id] );
			} else {
				// Изменяем количество
				$this->items[$id][self::QUO] = $quo;				
			}
		}
	}

	/**
	 * Очистка заказа
	 */
	public function clear() {
		$this->items = array();
		$this->userComment = '';
	}

	/**
	 * Обработка обращения к корзине
	 */	
	public function handle() {
		// Add product to cart
		if ( isset( $_GET['id'] ) ) {
			$this->create->add( wpsl_clean( $_GET ) );
		}
	}
	 
	/**
	 * Возвращает HTML код корзины
	 * @return string HTML код корзины
	 */
	public function get_table( $fields, $output = '' ) {
		$output .= '<div class="wpsl-table">';
		foreach ( $fields as $row ) {
			$output .= '<div class="wpsl-table__row">';
			foreach ( $row as $key => $col ) {
				$output .= '<div class="wpsl-table__row_col ' . strtolower( $key ) . '">' . $col . '</div>';
			}
			$output .= '</div>';
		}
		$output .= '</div>';
		return $output;
	}
	 
	/**
	 * Подготавливает массив товаров для вывода в корзине
	 * @return array
	 */
	public function get_items() {
		$new = array();
		foreach ( $this->items as $id => $item ) {
			$new[] = array(
				'photo'  => wpsl_get_thumbnail( $id, 'thumbnail' ),
				'title'  => apply_filters( 'wpsl_show_product_details', '<a href="' . wpsl_get_permalink( wpsl_product_id( $id ) ) . '" target="_blank">' . $item[self::TITLE] . '</a>', $id, $item ),
				'price'  => wpsl_price( $item[self::PRICE] ),
				'quo'    => '<input max="99999" min="0" step="1" type="number" name="' . $id  . '" value="' . $item[self::QUO]  . '" />',
				'summ'   => wpsl_price( $item[self::QUO] * $item[self::PRICE] ),
				'delete' => '<span class="wpsl-delete icon-x" title="' . __( 'Remove from cart', 'wpsl' ) . '"></span>',
			);
		}
		return $new;
	}
	 
	/**
	 * Возвращает уведомления корзины
	 *
	 * @return string HTML код уведомлений
	 */
	public function get_notif() {
		$html = '';
		$discount = WPSL_Discount::getInstance();
		$errors = $discount->errors;
		if ( is_array( $errors ) ) {
			foreach ( $errors as $error ) {
				$html .= '<div class="wpsl-notice">' . $error . '</div>';
			}
		}
		return $html;
	}
	 
	/**
	 * Возвращает подытог корзины
	 *
	 * @return string HTML код подытога корзины
	 */
	public function get_subtotal() {
		$discount = WPSL_Discount::getInstance();
		$html = '';
		if ( $discount->get_discount() != '0' ) {
			$html .= '<div class="title">' . __( 'Merchandise total', 'wpsl' ) . ':</div>';
			$html .= '<div class="price">' . wpsl_price( $this->getTotal() ) . '</div>';
			if ( !is_array( $this->get_notif() ) ) {
				$html .= '<div class="title">' . __( 'Discount total', 'wpsl' ) . ':</div>';
				$html .= '<div class="price discount">- ' . wpsl_price( $discount->get_discount() ) . '</div>';
			}
		}
		$html .= '<div class="title">' . __( 'Subtotal', 'wpsl' ) . ':</div>';
		$html .= '<div class="total">' . wpsl_price( $this->getTotal() ) . '</div>';
		return $html;
	}
	 
	/**
	 * Возвращает HTML код корзины
	 *
	 * @return string HTML код корзины
	 */
	public function getHTML() {
		
		$output = '<div class="wpsl-c">';
		
		/**
		 * If cart is empty - display a message
		 */
		if ( $this->isEmpty() ) {
			return wpsl_get_template_html( 'cart', 'cart-empty' );
		}
		
		/**
		 * Hook wpsl_before_cart_form
		 */
		//$output = apply_filters( 'wpsl_before_cart_form', $output );

		/**
		 * Show cart
		 */
		$output .= wpsl_get_template_html( 'cart', 'cart' );
		
		/**
		 * Hook wpsl_after_cart_form
		 */
		//$output = apply_filters( 'wpsl_after_cart_form', $output );
		
		$output .= '</div>';
		
		return $output;
	}

	/**
	 * Возвращает общую сумму заказа
	 *
	 * @return float сумма заказа
	 */
	public function getTotal() {
	 	$total = 0;
		foreach ( $this->items as $id => $item ) {
			$total += $item[self::QUO] * $item[self::PRICE];
		}
		return $total;
	}

}