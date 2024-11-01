<?php
/**
 * Email constructor
 *
 * @author   wpStore
 * @since    2.7.0
 */


if ( ! defined( 'ABSPATH' ) ) exit; 


class WPSL_Email {
	
    /**
	 * Who is the recipient: admin, customer, or custom email
	 */
    public $to = '';
	
    /**
	 * The subject of the message (header)
	 */
    public $subject = '';
	
    /**
	 * Message of mail
	 */
	public $message = '';
	
    /**
	 * Email headers indicating its attributes.
	 */
    public $headers = array();
	
    /**
	 * Files to attach to the email
	 */
	public $attachments = array();
	
    /**
	 * Construct
	 */
    function __construct( $args = false ) {
		$this->init_properties( $args );
		add_filter( 'wp_mail_from',         array( $this, 'change_sender_email' ), 10, 1 );
		add_filter( 'wp_mail_from_name',    array( $this, 'change_name_sender' ), 10 );
		add_filter( 'wp_mail_content_type', array( $this, 'set_mail_type' ), 10 );
    }
	
    function init_properties( $args ) {
        $properties = get_class_vars( get_class( $this ) );
        foreach ( $properties as $name => $val ) {
            if( isset( $args[$name] ) ) $this->$name = $args[$name];
        }
    }

    /**
	 * Change sender email
	 */
	function change_sender_email( $from_email ) {
		return 'noreply@' . $_SERVER['HTTP_HOST'];
	}

    /**
	 * Create social links
	 */
	function change_name_sender( $name ) {
		return wpsl_opt( 'email_from_name' );
	}

    /**
	 * Format of email
	 */
	function set_mail_type() {
		return 'text/html';
	}
	
	/**
	 * Send email to...
	 */
	function get_to() {
		// send mail to
		$to = $this->to;
		if ( in_array( $to, ['admin', 'administrator', 'editor', 'author', 'contributor', 'subscriber', 'wpsl_manager', 'wpsl_consultant', 'wpsl_cashier'] ) ) {
			$users = get_users(
				array(
					'role' => ( $to == 'admin' ) ? 'administrator' : $to
				)
			);
			$emails = array();
			foreach( $users as $user ) {
				$emails[] = $user->user_email;
			}
			$to = $emails;
		}
		return $to;
	}
	
    /**
	 * Phone of shop
	 */
	function get_phone() {
		$content = '';
		if ( wpsl_opt( 'phone' ) ){
			$content = apply_filters( 'wpsl_get_phone', __( 'Support to resolve issues on availability, payment and receipt of products', 'wpsl' ) . ': ' . wpsl_opt( 'phone' ) );
		}
		return $content;
	}
	
    /**
	 * Work schedule shop
	 */
	function get_schedule() {
		$content = '';
		if ( wpsl_opt( 'schedule' ) ){
			$content = apply_filters( 'wpsl_get_schedule', wpsl_opt( 'schedule' ) ) . '<br>';
		}
		return $content;
	}
	
    /**
	 * Shop address
	 */
	function get_address() {
		$content = '';
		if ( wpsl_opt( 'store_address' ) ){
			$content = apply_filters( 'wpsl_get_address', wpsl_opt( 'store_address' ) ) . '<br>';
		}
		return $content;
	}
	
    /**
	 * Account text
	 */
	function get_account() {
		$content = __( 'You can find information about your order in your personal account', 'wpsl' );
		$content .= '<a href="' . home_url() . '/wp-login.php" target="_blank">' . __( 'Login to account', 'wpsl' ) . '</a>';
		return $content;
	}
	
    /**
	 * Support text
	 */
	function get_support() {
		$content = '';
		$texts = array();
		$texts['default'] = __( 'This letter is generated automatically, do not answer it.', 'wpsl' );
		if ( wpsl_opt( 'support' ) == true && wpsl_opt( 'ticket_system' ) != true ) {
			$texts['email'] =
				wp_sprintf (
					__( 'If you need to contact technical support, please send a request to email', 'wpsl' ) . ' <a href="mailto:%s" style="color:#007cff; font-weight:normal; text-decoration:underline; font-family: Open Sans, Verdana, Arial;">%s</a>',
					wpsl_opt( 'operator_email' ),
					wpsl_opt( 'operator_email' )
				);
		}
		if ( wpsl_opt( 'support' ) == true && wpsl_opt( 'ticket_system' ) == true ) {
			$texts['tickets'] = 
				wp_sprintf (
					__( 'Technical support is provided through the system of internal tickets in your account.', 'wpsl' ) . ' <a href="%s">%s</a>',
					get_permalink( wpsl_opt( 'pageaccount' ) ),
					__( 'Go to account', 'wpsl' )
				);
		}
		$texts = apply_filters( 'wpsl_get_support_text', $texts );
		foreach ( $texts as $text ) {
			$content .= $text . '<br>';
		}
		return $content;
	}

	/**
	 * Gets a link to download a digital product
	 */
	function get_active_payment( $order_id ) {
		if ( $order_id ) {
			$payment = new WPSL_Payment();
			$array = array();
			foreach ( $payment->get_methods() as $k => $method ) {
				if ( $method['online'] == true && wpsl_opt( $k ) == true ) {
					$array[$k] = $method;
				}
			}
			return $array;
		}
	}

	/**
	 * Gets a link to download a digital product
	 */
    function get_link( $product_id, $order_id ) {
		$link = '';
		if ( $order_id && get_post_meta( $product_id, '_digital', true ) == true ) {
			if ( wpsl_is_status( $order_id, 'paid' ) || get_post_meta( $product_id, '_price', true ) == '0' ) {
				if ( get_post_meta( $product_id, '_upload_file', true ) ) {
					$link = '<a href="' . wpsl_get_permalink( $product_id ) . '?download=' . $product_id . '&token=' . wpsl_add_token( $product_id ) . '">' . __( 'Download', 'wpsl' ) . '</a>';
				} else {
					$link = __( 'Link to the file is not found. Contact technical support for assistance', 'wpsl' );
				}
			}
		}
		return $link;
	}
	
	/**
	 * Возвращает HTML код заказа
	 * @return mixed   массив с письмом
	 *
	 * @params  order_id - Order ID
	 *          paid     - The contents of the goods after payment. If true, sends the contents of the order with the paid
	 */
    function get_order( $order_id = '', $paid = false ) {

		$headers = array(
			'photo' => __( 'Photo', 'wpsl' ),
			'title' => __( 'Title', 'wpsl' ),
			'quo'   => __( 'Quo', 'wpsl' ),
			'price' => __( 'Price', 'wpsl' ),
			'summ'  => __( 'Summ', 'wpsl' ),
		);
		
		$content = '<table style="width: 100%; margin-top: 15px; border-bottom: 1px solid #f1f1f1; border-top: 1px solid #f1f1f1; padding: 15px 0;">
				      <thead>
						<tr>
							';
						foreach ( $headers as $key => $val ) {
							$content .= '<td class="' . $key . '">' . $val . '</td>
							';
						}
		$content .=	'</tr>
					  </thead>
					  <tbody>
					    ';
					  
		$total = 0;
		
		// details of order
		if ( empty( $order_id ) ) {
			$order_id = $this->order_id;
		}
		$order_detail = get_post_meta( $order_id, 'detail', true );
		foreach ( $order_detail as $product_id => $item ) {
			// fill product
			$product = array(
				'photo'       => wpsl_get_thumbnail( $product_id, 'wpsl-small-thumb', 'style="width: 45px;"' ),
				'title'       => $item['WPSL_TITLE'],
				'details'     => apply_filters( 'wpsl_show_product_details_email', $item['WPSL_INFO'], $item ),
				'permalink'   => wpsl_get_permalink( wpsl_product_id( $product_id ) ),
				'description' => '', //get_post_meta( $product_id, '_purchase_note', true ),
				'quo'         => $item['WPSL_QUO'],
				'price'       => wpsl_price( $item['WPSL_PRICE'] ),
				'summ'        => wpsl_price( $item['WPSL_QUO'] * $item['WPSL_PRICE'] ),
				'coast'       => $item['WPSL_QUO'] * $item['WPSL_PRICE'],
				'link'        => $this->get_link( wpsl_product_id( $product_id ), $order_id ),
			);
			
			$content .= '
						<tr>
						  ';
				  foreach ( $headers as $key => $val ) {
					  if ( $key == 'title' ) {
						  $content .= '<td data-label="' . $val . ':" class="' . $key . '">';
						  $content .= '<a href="' . $product['permalink'] . '" target="_blank">' . $product['title'] . '</a>';
						  $content .= $product['description'] != '' ? '<span style="float:left;width: 100%; color: #666; font-size: 13px; line-height: 120%;">' . $product['description'] . '</span>' : '';
						  $content .= $product['details'] != '' ? '<span style="float:left;width: 100%; color: #666; font-size: 13px; line-height: 120%;">' . $product['details'] . '</span>' : '';
						  $content .= $product['link'] != '' ? '<span style="float:left;width: 100%; color: #666; font-size: 13px; line-height: 120%;">' . $product['link'] . '</span>' : '';
						  $content .= '</td>
						  ';
					  } else {
						  $content .= '<td data-label="' . $val . ':" class="' . $key . '">' . $product[$key] . '</td>
						  ';
					  }
				  }			
			$content .= '</tr>';
			
			$total += $product['coast'];
		}
		$content .= '
						</tbody>
					</table>';
						
		$content .= '
					<div style="padding: 10px 0 20px; line-height: 40px; font-weight: 700; float: left; width: 100%;">
						<div style="width:100%;">
							<div class="wps-sum-title" style="width: 25%; float:left; display:inline-block;">' . __( 'Total', 'wpsl' ) . '</div>
							<div class="wps-sum" style="width: 25%; float:left; display:inline-block;" data-label="' . __( 'Total', 'wpsl' ) . ':" data-value="' . $total . '">' . wpsl_price( $total ) . '</div>
							<div  class="wps-barcode" style="background: repeating-linear-gradient(90deg, #504e4e, #504e4e 1px, #f1f1f1 1px, #f1f1f1 4px, #504e4e 4px, #504e4e 7px, #f1f1f1 7px, #f1f1f1 11px, #504e4e 11px, #504e4e 15px, #f1f1f1 15px, #f1f1f1 16px, #504e4e 16px, #504e4e 17px, #f1f1f1 17px, #f1f1f1 20px, #504e4e 20px, #504e4e 24px, #f1f1f1 24px, #f1f1f1 26px, #504e4e 26px, #504e4e 28px, #f1f1f1 28px, #f1f1f1 32px, #504e4e 32px, #504e4e 34px, #f1f1f1 34px, #f1f1f1 35px); width: 50%; height: 40px; float:left;"></div>
						</div>
					</div>
					';

		return $content;
	}
	
    /*
	 * The forming of the body of the mail
	 */
    function get_mail() {

		$content = apply_filters( 'wpsl_get_mail', wpsl_get_template_html( 'email', 'email' ) );
	  
		$replacemets = apply_filters( 'wpsl_get_mail_replacemets', array(
			'[mail-content]'  => $this->message,
			'[mail-support]'  => $this->get_support(),
			'[mail-phone]'    => $this->get_phone(),
			'[mail-account]'  => $this->get_account(),
			'[mail-schedule]' => $this->get_schedule(),
			'[mail-address]'  => $this->get_address(),
		) );
		foreach ( $replacemets as $shortcode => $replacemet ) {
			$content = str_replace( $shortcode, $replacemet, $content );
		}
        
        return $content;
        
    }

    /*
	 * Send email
	 */
	function send_mail() {
		$to          = $this->get_to();
		$subject     = $this->subject;
		$mail_txt    = $this->get_mail();
		$headers     = apply_filters( 'wpsl_mail_headers', $this->headers );
		$attachments = $this->attachments;
		
		// send email by wp_mail
		$result      = wp_mail( $to, $subject, $mail_txt, $headers, $attachments );
		remove_filter( 'wp_mail_content_type', 'set_mail_type' );
		return $result;
	}
    
}


/*
 * Send email
 *
 * @since 2.7.0
 * @params string, array  $to     Required parameter which specifies the list of email to send
 *                                Can be one from default roles: admin, editor, author, contributor or subscriber
 *                                From new roles: wpsl_manager, wpsl_consultant, wpsl_cashier
 *                                If a role is specified, email will be sent to all users with this role
 *                                Or from custom emails in array()
 *            string  $subject    Subject of mail (can be 'order' or custom subject)
 *                                If you set the 'order' value, you must pass the order ID in the $massage parameter
 *               int  $message    The text of the letter is sent.
 *                                If you specify the order id in this parameter
 *                                The text will automatically form a list of products in the transferred order in the form of a table
 */
function wpsl_mail( $to, $subject, $message, $headers = '', $attachments = '' ) {
	$args = array(
		'to'          => $to,
		'subject'     => $subject,
		'message'     => $message,
		'headers'     => $headers,
		'attachments' => $attachments,
	);
	$email = new WPSL_Email( $args );
    return $email->send_mail();
}