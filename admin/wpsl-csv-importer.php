<?php
if ( ! defined( 'ABSPATH' ) ) exit; 

if ( !is_admin() && !wp_doing_ajax() ) return;



add_action( 'admin_menu', 'wpsl_importer', 40 );
function wpsl_importer() {
	add_submenu_page( null, __( 'Import', 'wpsl' ), __( 'Import', 'wpsl' ), 'install_plugins', 'wpsl-import', 'wpsl_import_page' ); 
}


function wpsl_import_page() {
	?>
	<div class="wrap">
		<div class="wpsl-import">
			<h2><?php _e( 'Import form', 'wpsl' ); ?></h2>
			<input type="file" class="wpsl-import__input" accept=".csv">
			<a href="#" class="wpsl-import__button button button-primary"><?php _e( 'Load file', 'wpsl' ); ?></a>
			<div class="wpsl-import__result"></div>
		</div>
	</div>
	<?php
}