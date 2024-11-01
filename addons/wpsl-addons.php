<?php
/**
 * wpStore
 *
 * File for display addons in admin panel
 *
 * @author	wpStore
 * @since	2.1
 */


if ( ! defined( 'ABSPATH' ) ) exit;


if ( !is_admin() ) return;


add_action( 'admin_menu', 'wpsl_display_addons', 30 );
function wpsl_display_addons() {
	add_submenu_page( 'wpsl_options', __( 'Addons to the plugin wpStore', 'wpsl' ), __( 'Marketplace', 'wpsl' ), 'install_plugins', 'wpsl-addons', 'wpsl_addons_page' ); 
}


function wpsl_addons_page() {
	
	$addons = array(
		'wp-store-booking' => array(
			'price' => '4000 ₽',
			'name'  => 'wpStore Booking',
			'img'   => WPSL_URL . '/addons/img/open-book.png',
			'url'   => 'https://codyshop.ru/product/wp-store-booking/',
			'desc'  => __( 'Addon for wpStore allows you to deploy a system of appointment to a specialist or booking time for any event', 'wpsl' ),
			'ver'   => '4.8+'
		),
		'wp-store-notification' => array(
			'price' => __( 'Free', 'wpsl' ),
			'name'  => 'wpStore Notification',
			'img'   => WPSL_URL . '/addons/img/notification.png',
			'url'   => 'https://codyshop.ru/product/wp-store-notification/',
			'desc'  => __( 'The addon allows you to display dynamic notifications about promotions, news, and other special conditions of your online store', 'wpsl' ),
			'ver'   => '4.8+'
		),
	);
	
	echo '<div class="wrap">';
		echo '<h2>'. get_admin_page_title() .'</h2>';
	echo '</div>';
	
	echo '<div class="wrap wps-plugin-install-tab-featured">';
	foreach ( $addons as $slug => $arr ) {
		?>
		<div id="wps-the-list paid-addons">
			<div class="wps-plugin-card plugin-card-<?php echo $slug; ?>">
				<div class="plugin-card-top">
					<div class="name column-name">
						<h3><?php echo $arr['name']; ?><img src="<?php echo $arr['img']; ?>" class="plugin-icon" alt="">
						</h3>
					</div>
					<div class="action-links">
						<ul class="plugin-action-buttons">
							<li><a class="install-now button" target="_blank" href="<?php echo $arr['url']; ?>"><?php echo $arr['price'] == __( 'Free', 'wpsl' ) ? __( 'Download', 'wpsl' ) : __( 'Buy', 'wpsl' ) ?></a></li>
						</ul>
					</div>
					<div class="desc column-description">
						<p><?php echo $arr['desc']; ?></p>
					</div>
				</div>
				<div class="plugin-card-bottom">
					<div class="vers column-rating">
						<strong><?php _e( 'Price', 'wpsl' ); ?>:</strong> <?php echo $arr['price']; ?>
					</div>
					<div class="column-compatibility">
						<span class="compatibility-compatible"><strong><?php _e( 'WordPress version', 'wpsl' ); ?>:</strong> <?php echo $arr['ver']; ?></span>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
	?>
	<style>
		.wps-plugin-install-tab-featured{
			float:left;
			margin: 20px 10px 0 0;
		}
		.wps-plugin-install-tab-featured > div{
			width: 50% !important;
			float:left !important;
			display: inline-block !important;
		}
		.wps-plugin-card{
			float: left;
			margin: 0;
			width: calc(100% - 10px);
			background-color: #fff;
			border: 1px solid #ddd;
			-webkit-box-sizing: border-box;
			-moz-box-sizing: border-box;
			box-sizing: border-box;
		}
		.wps-plugin-card .desc, .wps-plugin-card .name{
			margin-left: 148px;
		}
		.wps-plugin-card .action-links{
			margin-left: 148px;
			width: auto;
			top: 20px;
			right: 20px;
		}
		.plugin-action-buttons{
			float:left;
			margin: 0;
			width: 100%;
			clear: right;
			text-align: left;
		}
		.plugin-card-bottom > div{
			width: 50% !important;
			float: left !important;
			display: inline-block !important;
		}
		.plugin-card-bottom .column-compatibility{
			text-align: right;
		}
		@media screen and (max-width: 640px) {
			.wps-plugin-install-tab-featured > div{
				width: 100% !important;
				margin-bottom: 10px;
			}
		}
	</style>
	<?php
	echo '</div>';
}