<?php
/**
 * Get Installed Plugins
 */
function brandography_support_get_plugins() {
	if ( ! function_exists( 'get_plugins' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}
	$allPlugins = get_plugins();
	$activePlugins = get_option('active_plugins');
	foreach($allPlugins as $path => $plugnObj):
		$allPlugins[$path]['active'] = (array_search( $path, $activePlugins) !== false) ? true : false;
		$allPlugins[$path]['location'] = $path;
		unset($allPlugins[$path]['Description']);
		unset($allPlugins[$path]['TextDomain']);
		unset($allPlugins[$path]['DomainPath']);
		unset($allPlugins[$path]['Network']);
	endforeach;
	return $allPlugins;
}

/**
 * Get Theme Information
 */
function brandography_support_get_theme_info() {
	$theme = new stdClass();
	$theme->name = wp_get_theme()->get('Name');
	$theme->uri = wp_get_theme()->get('ThemeURI');
	$theme->version = wp_get_theme()->get('Version');
	$theme->author = wp_get_theme()->get('Author');
	$theme->authorURI = wp_get_theme()->get('AuthorURI');
	return $theme;
}

/**
 * Matinenance Session Status
 */
function brandography_support_session_status() {
	global $wpdb;
	$table_name = $wpdb->prefix . 'brandography_support';
	$start = $wpdb->get_var('SELECT meta_value FROM ' . $table_name . ' WHERE meta_key="maintenance_start";');
	$end = $wpdb->get_var('SELECT meta_value FROM ' . $table_name . ' WHERE meta_key="maintenance_end";');
	return (!empty($start) + !empty($end));
}

/**
 * Maintenance Session Log
 */
function brandography_support_session_log() {
	global $wpdb;
	$table_name = $wpdb->prefix . 'brandography_support';
	$start = $wpdb->get_var('SELECT meta_value FROM ' . $table_name . ' WHERE meta_key="maintenance_start";');
	$end = $wpdb->get_var('SELECT meta_value FROM ' . $table_name . ' WHERE meta_key="maintenance_end";');
	$log = new stdClass();
	$log->start = json_decode( $start );
	$log->end = json_decode( $end );
	return $log;
}

/**
 * Maintenance Session Information Snapshot
 */
function brandography_support_session_info() {
	$data = new stdClass();
	$theme = brandography_support_get_theme_info();

	$wordpress = new stdClass();
	$wordpress->name = get_bloginfo('name');
	$wordpress->wpurl = get_bloginfo('wpurl');
	$wordpress->version = get_bloginfo('version');

	$data->theme = $theme;
	$data->plugins = brandography_support_get_plugins();
	$data->wordpress = $wordpress;
	return $data;
}