<?php
/**
 * Class for adding products to the wishlist
 *
 * @author	wpStore
 * @since	2.9.4
 */
class WPSL_Wishlist extends WPSL_Cart {

	/**
	 * List 
	 */
	public $wishlist;
	
	/**
	 * Class instance
	 * @static
	 */
	private static $instance = NULL;

	/**
	 * Возвращает экземпляр класса
	 * @static
	 * @return WPSL_Wishlist
	 */
	public static function create() {
		if ( !self::$instance )
			self::$instance = new WPSL_Wishlist();
		return self::$instance;
	}

	/**
	 * Class constructor
	 */
	private function __construct() {
		$this->wishlist = ( isset( $_SESSION['WPSL_USER_WISHLIST'] ) ) ? $_SESSION['WPSL_USER_WISHLIST'] : array();
	}

	/**
	 * Class destructor
	 */
	public function __destruct() {
		$_SESSION['WPSL_USER_WISHLIST'] = $this->wishlist;
	}

	/**
	 * Adds new product to the list
	 */
	 public function add( $get, $count = 1 ) {
		$id      = (int)$get['id'];
		$product = get_post( $id );
		if ( !array_key_exists( $id, $this->wishlist ) ) {
			$this->wishlist[$id] = array(
				self::ID    => $id,
				self::TITLE => $product->post_title,
				self::QUO   => $count,
				self::PRICE => get_post_meta( $id, '_price', true ),
			);
		}
	}

	/**
	 * Clear wishlist
	 */
	public function clear() {
		$this->wishlist = array();
	}

}