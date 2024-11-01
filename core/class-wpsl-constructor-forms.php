<?php
/**
 * Form constructor
 */
class WPSL_Forms{
    
    public $action     = '';
    public $method     = 'post';
    public $nonce_name = '';
    public $fields     = array();
    public $values     = array();
    public $ajax       = true;
    public $class      = '';
    public $args       = '';
    public $submit;
    public $onclick;

    function __construct( $args = false ) {
        
        $this->init_properties( $args );

    }
    
    function init_properties( $args ){
        
        $properties = get_class_vars( get_class( $this ) );

        foreach ( $properties as $name=>$val ){
            if( isset( $args[$name] ) ) $this->$name = $args[$name];
        }
        
    }

    function get_form( $args = false ) {
		
		if ( !$this->fields ) return;
            
		$content = '<form name="' . $this->onclick . '" id="' . $this->onclick . '" action="' . $this->action . '" method="' . $this->method . '" class="wpsl-form ' . $this->class . '" data-ajax="' . $this->ajax . '" ' . $this->args . '>';
		
			$anchor = false;

			foreach( $this->fields as $field ) {

				$value = ( isset( $field['name'] ) && isset( $this->values[$field['name']] ) ) ? $this->values[$field['name']] : false;

				$is_required = ( isset( $field['required'] ) && $field['required'] == 1 ) ? 'required data-validate' : '';
				
				if ( isset( $field['type'] ) && $field['type'] == '' ) continue;
				
				$class = isset( $field['class'] ) ? $field['class'] : '';

				$content .= '<div class="wpsl-form__row ' . $class . '">';

					if( isset( $field['title'] ) && $field['type'] != 'hidden' ){
						$required = ( isset( $field['required'] ) && $field['required'] == 1 ) ? '<span class="required">*</span>': '';
						$content .= '<label class="wpsl-form__row_title" for="' . $field['name'] . '">';
						$content .= $field['title'] . ' ' . $required;
						$content .= '</label>';
					}

					switch ( $field['type'] ) {
						case('text'):
							$content .= '<input id="' . $field['name'] . '" type="text" name="' . $field['name'] . '" value="' . $field['value'] . '" placeholder="' . $field['placeholder'] . '" ' . $is_required . ' />';
							break;
						case('cart'):
							$content .= '<input id="' . $field['name'] . '" type="number" name="' . $field['name'] . '" value="' . $field['value'] . '" placeholder="' . $field['placeholder'] . '" ' . $is_required . ' /><button type="submit" data-action="' . $this->onclick . '" value="' . esc_html( wp_strip_all_tags( $this->submit ) ) . '">' . $this->submit . '</button>';
							$anchor = true;
							break;
						case('coupon'):
							$content .= '<input id="' . $field['name'] . '" type="text" name="' . $field['name'] . '" value="' . $field['value'] . '" placeholder="' . $field['placeholder'] . '" ' . $is_required . ' /><button type="submit" data-action="update_cart" class="apply">' . __( 'Apply', 'wpsl' ) . '</button>';
							break;
						case('number'):
							$content .= '<input id="' . $field['name'] . '" type="number" name="' . $field['name'] . '" value="' . $field['value'] . '" placeholder="' . $field['placeholder'] . '" ' . $field['attr'] . ' ' . $is_required . ' />';
							break;
						case('password'):
							$content .= '<input id="' . $field['name'] . '" type="password" name="' . $field['name'] . '" value="' . $field['value'] . '" placeholder="' . $field['placeholder'] . '" ' . $is_required . ' />';
							break;
						case('uploader'):
							$content .= '
							<table class="wpsl-metabox uploader wp-list-table widefat fixed striped tags active">
								<thead>
									<tr>
										<th scope="col" class="manage-column column-id" style="width: 25px;">ID</th>
										<th scope="col" class="manage-column column-url">' . __( 'URL', 'wpsl' ) . '</th>
										<th scope="col" class="manage-column column-btn" style="width: 15px;"></th>
										<th scope="col" class="manage-column column-remove" style="width: 15px;"></th>
									</tr>
								</thead>
								<tbody class="files-list">		
									<tr class="column-row">
										<td class="column-id"></td>
										<td class="column-url">
											<input id="' . $field['name'] . '" type="text" name="' . $field['name'] . '" placeholder="' . $field['placeholder'] . '" value="' . $field['value'] . '" />
										</td>
										<td class="column-btn" title="' . __( 'Upload', 'wpsl' ) . '"><i class="upload_image_button dashicons dashicons-download"></i></td>
										<td class="column-remove" title="' . __( 'Delete', 'wpsl' ) . '"><i class="remove_image_button dashicons dashicons-no"></i></td>
									</tr>
								</tbody>
							</table>';
							break;
						case('radio'):
							$i = 1;
							if ( $field['args'] && is_array( $field['args'] ) ) {
								foreach ( $field['args'] as $elem ) {
									$is_required = ( isset( $field['required'] ) && $field['required'] == 1 && $i == 1 ) ? 'required data-validate' : '';
									$content .= '<input type="radio" id="' . $field['name'] . '-' . $i . '" value="' . $elem . '" name="' . $field['name'] . '" ' . $is_required . ' />';
									$content .= apply_filters( 'wpsl_constructor_forms_radio', '<label class="' . $field['name'] . ' ' . $field['name'] . '-' . $i . '" for="' . $field['name'] . '-' . $i . '">' . $elem . '</label>', $field, $elem, $i );
									$i++;
								}
							}
							unset( $i );
							break;
						case('checkbox'):
							$content .= '<input id="' . $field['name'] . '" type="checkbox" name="' . $field['name'] . '" value="' . $field['value'] . '" ' . $is_required . ' />';
							break;
						case('raiting'):
							$names = apply_filters( 'wpsl_rating_names',
								array(
									'1' => __( 'Very poor', 'wpsl' ),
									'2' => __( 'Not that bad', 'wpsl' ),
									'3' => __( 'Average', 'wpsl' ),
									'4' => __( 'Good', 'wpsl' ),
									'5' => __( 'Perfect', 'wpsl' ),
								)
							);
							$content .= '<div class="wpsl-rating">';
							for( $i = 5; $i > 0; $i-- ) {
								$is_required = ( $i == 5 && isset( $field['required'] ) && $field['required'] == 1 ) ? 'required data-validate' : '';
								$content .= '<input class="wpsl-rating__input" id="' . $field['name'] . '-' . $i . '" type="radio" name="' . $field['name'] . '" value="' . $i . '" ' . $is_required . ' /><label class="wpsl-rating__icon icon-star" for="' . $field['name'] . '-' . $i . '" title="' . $names[$i] . '"></label>';
							}
							$content .= '</div>';
							break;
						case('email'):
							$content .= '<input id="' . $field['name'] . '" type="email" name="' . $field['name'] . '" value="' . $field['value'] . '" placeholder="' . $field['placeholder'] . '" ' . $is_required . ' />';
							break;
						case ('select'):
							$content .= '<select id="' . $field['name'] . '" name="' . $field['name'] . '">';
							foreach( $field['value'] as $key => $val ){
								$content .= '<option value="' . $key . '">' . $val . '</option>';
							}
							$content .= '</select>';
							break;
						case ('textarea'):
							$content .= '<textarea id="' . $field['name'] . '" name="' . $field['name'] . '" placeholder="' . $field['placeholder'] . '" ' . $is_required . '>' . $field['value'] . '</textarea>';
							break;
						case('hidden'):
							$content .= '<input id="' . $field['name'] . '" type="hidden" name="' . $field['name'] . '" value="' . $field['value'] . '" />';
							break;
						case ('empty'):
							$content .= '';
							break;
						case('custom'):
							$content .= $field['fill'];
							break;
					}
					//$content .= $this->get_input( $field, $value );
					
					if ( isset( $field['notice'] ) && $field['notice'] != '' ) {
						$content .= '<div class="wpsl-warning wpsl-hidden">' . $field['notice'] . '</div>';
					}

				$content .= '</div>';

			}

			if ( $this->submit && $anchor != true ) {
				$content .= '<div class="wpsl-form__row submit-box">';
				$content .= '<button type="submit" data-action="' . $this->onclick . '" value="' . esc_html( wp_strip_all_tags( $this->submit ) ) . '">' . $this->submit . '</button>';				
				$content .= '</div>';
			}
			
			if( $this->nonce_name )
				$content .= wp_nonce_field( $this->nonce_name, '_wpnonce', true, false );

		$content .= '<div class="wpsl-result"></div>';
		$content .= '</form>';
        
        return $content;
        
    }
    
}


/**
 * Constructor of forms
 */ 
function wpsl_get_form( $fields = array(), $args = array() ) {
	
	$default = array(
		'method'    => 'post',
		'action'    => '/',
		'submit'    => __( 'Send', 'wpsl' ),
		'fields'    => $fields,
		'onclick'   => '',
		'ajax'      => true,
		'class'     => '',
	);
	$args = array_merge( $default, $args );
	
    $form = new WPSL_Forms( $args );	
    return $form->get_form();
}