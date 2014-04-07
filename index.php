<?php

/*
  Plugin Name: WordPress HRM
  Plugin URI: http://rtcamp.com/
  Description: Manage leave & user profile.
  Version: 0.0.0
  Author: rtCamp
  Author URI: http://rtcamp.com
  License: GPL
  Text Domain: rt-hrm
 */

if ( !defined( 'RT_HRM_VERSION' ) ) {
	define( 'RT_HRM_VERSION', '0.0.0' );
}
if ( !defined( 'RT_HRM_PATH' ) ) {
	define( 'RT_HRM_PATH', plugin_dir_path( __FILE__ ) );
}
if ( !defined( 'RT_HRM_URL' ) ) {
	define( 'RT_HRM_URL', plugin_dir_url( __FILE__ ) );
}
if ( !defined( 'RT_HRM_PATH_APP' ) ) {
	define( 'RT_HRM_PATH_APP', plugin_dir_path( __FILE__ ) . 'app/' );
}
if ( !defined( 'RT_HRM_PATH_ADMIN' ) ) {
	define( 'RT_HRM_PATH_ADMIN', plugin_dir_path( __FILE__ ) . 'app/admin/' );
}
if ( !defined( 'RT_HRM_PATH_MODELS' ) ) {
	define( 'RT_HRM_PATH_MODELS', plugin_dir_path( __FILE__ ) . 'app/models/' );
}
if ( !defined( 'RT_HRM_PATH_SCHEMA' ) ) {
	define( 'RT_HRM_PATH_SCHEMA', plugin_dir_path( __FILE__ ) . 'app/schema/' );
}
if ( !defined( 'RT_HRM_PATH_LIB' ) ) {
	define( 'RT_HRM_PATH_LIB', plugin_dir_path( __FILE__ ) . 'app/lib/' );
}
if ( !defined( 'RT_HRM_PATH_HELPER' ) ) {
	define( 'RT_HRM_PATH_HELPER', plugin_dir_path( __FILE__ ) . 'app/helper/' );
}
if ( !defined( 'RT_HRM_PATH_TEMPLATES' ) ) {
	define( 'RT_HRM_PATH_TEMPLATES', plugin_dir_path( __FILE__ ) . 'templates/' );
}

function rt_hrm_include() {

	include_once RT_HRM_PATH_LIB . 'wp-helpers.php';

	include_once RT_HRM_PATH_HELPER . 'rthrm-functions.php';

	global $rthrm_app_autoload, $rthrm_admin_autoload, $rthrm_models_autoload, $rthrm_helper_autoload, $rt_calendar_autoload;
	$rthrm_app_autoload = new RT_WP_Autoload( RT_HRM_PATH_APP );
	$rthrm_admin_autoload = new RT_WP_Autoload( RT_HRM_PATH_ADMIN );
	$rthrm_models_autoload = new RT_WP_Autoload( RT_HRM_PATH_MODELS );
	$rthrm_helper_autoload = new RT_WP_Autoload( RT_HRM_PATH_HELPER );
	$rt_calendar_autoload = new RT_WP_Autoload( RT_HRM_PATH_LIB . 'rt-calendar/' );
}

function rt_hrm_init() {

	rt_hrm_include();

	global $rt_wp_hrm;
	$rt_wp_hrm = new RT_WP_HRM();

}
add_action( 'rt_biz_init', 'rt_hrm_init', 1 );
