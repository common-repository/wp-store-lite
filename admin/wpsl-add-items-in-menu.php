<?php
/**
 * wpStore
 *
 * Add cart to menu
 *
 * @author	wpStore
 * @since	2.2
 */
 
 
if ( ! defined( 'ABSPATH' ) ) exit;


if ( !is_admin() ) return;

 
/**
 * Fill metabox
 */
function wpsl_nav_menu_metabox( $object ){
	global $nav_menu_selected_id; 
	$nm      = __( 'Cart', 'wpsl');
	$login   = __( 'Login / Logout', 'wpsl');
	$profile = __( 'My account', 'wpsl');
	$elems   = array( 
		'#cart#'        => $nm,
		'#loginlogout#' => $login,
		'#account#'     => $profile
	);

	class WPSL_Menu {
		public $db_id = 0;
		public $object = 'wpsl_menu_item';
		public $object_id;
		public $menu_item_parent = 0;
		public $type = 'custom';
		public $title; // = 'Cart';
		public $url;
		public $target = '';
		public $attr_title = '';
		public $classes = array();
		public $xfn = '';
	}

	$elems_obj = array();
	foreach ( $elems as $value => $title ) {
		$elems_obj[$title] = new WPSL_Menu();
		$obj = &$elems_obj[$title];
		$obj->object_id = esc_attr( $value );
		if(empty($obj->title)) $obj->title = esc_attr( $title );
		$obj->label = esc_attr( $title );
		$obj->url = esc_attr( $value );
	}

	$walker = new Walker_Nav_Menu_Checklist();
?>
<div id="wpsl" class="qtranxslangswdiv">
	<div id="tabs-panel-wpsl-all" class="tabs-panel tabs-panel-view-all tabs-panel-active">
		<ul id="wpslchecklist" class="list:qtranxs-langsw categorychecklist form-no-clear">
			<?php echo walk_nav_menu_tree( array_map( 'wp_setup_nav_menu_item', $elems_obj ), 0, (object)array( 'walker' => $walker ) ) ?>
		</ul>
	</div>
	<span class="list-controls hide-if-no-js">
		<a href="javascript:void(0);" class="help" onclick="jQuery( '#wpsl-help' ).toggle();"><?php _e( 'Help', 'wpsl') ?></a>
		<span class="hide-if-js" id="wpsl-help">
			<p>
				<a name="wpsl-help"></a>
				<?php _e( 'For work plugin, please do not change the value of hashtags', 'wpsl' ); ?>
			</p>
		</span>
	</span>
	<p class="button-controls">
		<span class="add-to-menu">
			<input type="submit"<?php disabled( $nav_menu_selected_id, 0 ) ?> class="button-secondary submit-add-to-menu right" value="<?php esc_attr_e( 'Add', 'wpsl' ) ?>" name="add-wpsl-menu-item" id="submit-wpsl" />
			<span class="spinner"></span>
		</span>
	</p>
</div>
<?php
}


/**
 * Include metabox
 */
add_action( 'admin_head-nav-menus.php', 'wpsl_add_nav_menu_metabox' );
function wpsl_add_nav_menu_metabox(){
	add_meta_box( 'add-qtranxs-language-switcher', __( 'wpStore', 'wpsl' ), 'wpsl_nav_menu_metabox', 'nav-menus', 'side', 'default' );
}