<?php
/**
 * Class for send sms
 */
class WPSL_Sms {
	
    public  $phone    = '';
    public  $msg      = '';
    public  $result   = '';
	public  $type     = 'http';
	private $secret_code;
	public  $login    = '';

    function __construct( $args = false ) {
        $this->init_properties( $args );
    }
    
    function init_properties( $args ){
        $properties = get_class_vars( get_class( $this ) );
        foreach ( $properties as $name=>$val ){
            if( isset( $args[$name] ) ) $this->$name = $args[$name];
        }
    }

	/**
	 * Validate phone numbers
	 *
	 * @param   string   $phone
	 */
    function validate_phone() {
		return apply_filters( 'wpsl_sms_validate_phone', str_replace( array( '+', '(', ')', '-', '_', '.', ' ' ), '', $this->phone ) );
    }

	/**
	 * Validate text of sms
	 *
	 * @param   string   $msg
	 */
    function validate_msg() {
		return apply_filters( 'wpsl_sms_validate_msg', urlencode( $this->msg ), $this->msg );
    }
	
	/**
	 * Result of sms sendng
	 *
	 * @param   string   $result
	 */
	function check_errors( $json = '' ) {
		$result = '';
		$json = json_decode( $json );
		if ( $json ) {
			if ( $json->status == 'OK' ) {
				foreach ( $json->sms as $phone => $data ) {
					if ( $data->status == 'OK' ) {
						$result .= __( 'SMS was sent!', 'wpsl' );
					} else {
						$result .= __( 'Something went wrong, SMS is not sent. Try again.', 'wpsl' );
						$result .= $data->status_text;
					}
				}
			} else {
				$result .= __( 'Something went wrong, the query is not executed', 'wpsl' );
				$result .= $json->status_text;
			}
		} else { 
			$result .= __( 'The query is not executed. The connection to the server could not be established. Contact the store.', 'wpsl' );
		}
		return apply_filters( 'wpsl_sms_validate_msg', $result, $json );
	}

	/**
	 * Send sms
	 *
	 * @param       string   $msg
	 */
    function send() {
		
		$sms_url = wpsl_opt( 'sms_url' );
		$replacemets = array(
			'[secret_code]' => empty( $this->secret_code ) ? wpsl_opt( 'secret_code' ) : $this->secret_code,
			'[login]'       => $this->login ? $this->login : wpsl_opt( 'sms_login' ),
			'[phone]'       => $this->validate_phone(),
			'[msg]'         => $this->validate_msg(),
		);
		foreach ( $replacemets as $shortcode => $replacement ) {
			$sms_url = str_replace( $shortcode, $replacement, $sms_url );
		}
		
		$sms_url = apply_filters( 'wpsl_sms_http_request', $sms_url, $replacemets );
	
		if ( $this->type == 'http' ) {
			$json = file_get_contents( $sms_url );
			$result = $this->check_errors( $json );
		} else {
			$result = do_action( 'wpsl_sending_sms', $sms_url, $replacemets );
		}
		if ( $this->result == true ) {
			return $result;
		}
    }
}


/*
 * Send sms
 *
 * @param  string $secret_code SMS gateway secret code.
 * @param  string $login       If the formation of the http request requires login.
 * @param  string $phone       Phone to send SMS.
 * @param  string $msg         Message.
 * @param  bool   $result      Output the result?
 * @param  string $type        Type of query. If you are not using http, you can specify your type of SMS sending via wpsl_sending_sms action
 * @return string
 */
function wpsl_send_sms( $phone, $msg, $result = false, $type = 'http', $secret_code = '', $login = '' ) {
	$args = apply_filters( 'wpsl_send_sms', array(
		'phone'       => $phone,
		'msg'         => $msg,
		'result'      => $result,
		'type'        => $type,
		'secret_code' => $secret_code,
		'login'       => $login,
	) );
	$sms = new WPSL_Sms( $args );
    return $sms->send();
}