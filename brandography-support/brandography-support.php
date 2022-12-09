<?php
/*
Plugin Name: Brandography Support
Plugin URI: https://support.brandography.com/
Description: Easily contact Brandography's Support team.
Version: 0.3.2
Author: Brandography
Author URI: https://brandography.com
Text Domain: brandography-support
*/

// Exit if accessed directly
defined( 'ABSPATH' ) or exit;

// Setup the Support URL for Global Use
define('BRANDO_SUPPORT_URL', 'https://support.brandography.com/'); // Include Trailing Slash

include 'inc/form-parts.php';

include 'data-functions.php';

include 'changelog-recorder.php';

function brandography_support_plugin_install() {
   	global $wpdb;
  	$table_name = $wpdb->prefix . 'brandography_support';
	// If table does not exist
	if($wpdb->get_var("show tables like '$table_name'") != $table_name) 
	{
		// Create Table
		$sql = "CREATE TABLE " . $table_name . " (
		  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
		  `meta_key` text,
		  `meta_value` text,
  		  PRIMARY KEY (`id`)
		);";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
		// Add Default Items
		$wpdb->insert($wpdb->prefix . 'brandography_support', array( 'meta_key' => 'maintenance_start', 'meta_value' => NULL ), array( '%s', NULL ) );
		$wpdb->insert($wpdb->prefix . 'brandography_support', array( 'meta_key' => 'maintenance_end', 'meta_value' => NULL ), array( '%s', NULL ) );
	}
}
register_activation_hook(__FILE__,'brandography_support_plugin_install');

/**
 * Uninstall Process
 */
function brandography_support_plugin_uninstall() {
   	global $wpdb;
   	// Remove the Custom Table
  	$table_name = $wpdb->prefix . 'brandography_support';
    $sql = "DROP TABLE IF EXISTS $table_name";
    $wpdb->query($sql);
}
register_deactivation_hook( __FILE__, 'brandography_support_plugin_uninstall' );

/**
 * Brandography Support Admin Script/Style Enqueues
 */
add_action('admin_enqueue_scripts', 'brandography_support_script_enqueues');
add_action('wp_dashboard_setup', 'brandography_support_dashboard_widget');
function brandography_support_script_enqueues($hook) {
	if( $hook === 'index.php' ) { // Only Load These Files on the dashboard page
		wp_enqueue_script( 'brandography-support', plugin_dir_url( __FILE__ ) . 'main.js', array( 'jquery' ), false, false );
		wp_add_inline_script( 'brandography-support', "var BRANDO_SUPPORT_API_BASE = '" . BRANDO_SUPPORT_URL . "';", true);
		wp_enqueue_style( 'brandograph-support-stylesheet', plugin_dir_url( __FILE__ ) . 'style.css', array(), false );
	}
}

/**
 * Add the Brandography Support Dashboard Widget
 */
function brandography_support_dashboard_widget() {
	global $wp_meta_boxes;
	// wp_add_dashboard_widget('brandography_support', '<div class="brandography-icon-holder"></div>Powered by Brandography', 'brandography_support_widget' );
}

/**
 * Brandography Support Widget Template
 */
function brandography_support_widget() {
	$current_user = wp_get_current_user();
	?>
<div>
		<div id="brandography-dashboard">
			<form id="brandography-support-form" class="brandography-support-form">
<h2 class="brandography-support-title">
			<?php include 'inc/template-assets/brandography-logo-svg.php'; ?>
			<span style="display: block; text-align:left;">Brandography<br/>Support</span>
			<div style="margin: 0 10px 0 auto;font-size: 14px;text-align: right;line-height: 1.5">
				(612) 460-0016<br/>
			</div>
		</h2>
				<div class="brandography-support-feedback">
					<!-- Form Validation Messages Appear Here -->
				</div>
				<div class="form-row">
					<input type="text" id="name" placeholder="Name" />
					<input type="text" id="email_address" placeholder="Email" />
				</div>
				<textarea placeholder="How can we help?" id="message" rows="6" class="brandography-support-message"></textarea>
				<input type="submit" style="font-size: 1.25em; font-weight: lighter;" value="Request Support" />
				<input type="hidden" name="wp_name" id="wp_name" value="<?php echo get_bloginfo('name'); ?>" />
				<input type="hidden" name="client_host" id="client_host" value="<?php echo get_bloginfo('wpurl'); ?>" />
				<input type="hidden" name="wp_version" id="wp_version" value="<?php echo get_bloginfo('version'); ?>" />
				<input type="hidden" name="wp_plugins" id="wp_plugins" value="<?php echo htmlentities( json_encode( brandography_support_get_plugins() ) ); ?>" />
				 <input type="hidden" name="wp_theme" id="wp_theme" value="<?php echo htmlentities( json_encode( brandography_support_get_theme_info() ) ); ?>" />
				<input type="hidden" name="request_type" id="request_type" value="wordpress">
				<div id="brandography-support-loader">
					<svg width="15px" height="15px" version="1.1" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
					 <path d="m50 0c-27.609 0-50 22.391-50 50s22.391 50 50 50 50-22.391 50-50-22.391-50-50-50zm0 90.789c-22.531 0-40.789-18.258-40.789-40.789 0-5.5508 1.1094-10.84 3.1211-15.66 0.19141 5.2812 2.9492 10.352 7.8008 13.289 7.6914 4.6719 17.691 2.2188 22.34-5.4492 4.6562-7.6797 2.207-17.68-5.4727-22.34-3.8086-2.3086-8.1992-2.8711-12.219-1.8984 6.9414-5.4609 15.699-8.7305 25.219-8.7305 22.531 0 40.789 18.262 40.789 40.789 0 22.531-18.258 40.789-40.789 40.789z"/>
					</svg>
				</div>
			</form>	
		</div>
			<?php
				brandography_support_success();
			?>
		</div>
<?php  }

/**
 * Setup Automatic Updates
 */
include 'inc/plugin-update-checker/plugin-update-checker.php';
$brandographySupportUpdates = Puc_v4_Factory::buildUpdateChecker(
	BRANDO_SUPPORT_URL . 'updates/?action=get_metadata&slug=brandography-support',
	__FILE__,
	'brandography-support'
);
