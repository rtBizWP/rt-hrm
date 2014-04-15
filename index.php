<?php

/*
  Plugin Name: WordPress HRM
  Plugin URI: http://rtcamp.com/
  Description: Manage leave & user profile.
  Version: 0.0.0
  Author: rtCamp
  Author URI: http://rtcamp.com
  License: GPL
  Text Domain: rt_hrm
 */

if ( !defined( 'RT_HRM_VERSION' ) ) {
    /**
     * define HRM version
     */
    define( 'RT_HRM_VERSION', '0.0.0' );
}
if ( !defined( 'RT_HRM_TEXT_DOMAIN' ) ) {
    /**
     * define HRM text domain
     */
    define( 'RT_HRM_TEXT_DOMAIN', 'rt_hrm' );
}
if ( !defined( 'RT_HRM_PATH' ) ) {
    /**
     * define HRM plugin path
     */
    define( 'RT_HRM_PATH', plugin_dir_path( __FILE__ ) );
}
if ( !defined( 'RT_HRM_URL' ) ) {
    /**
     * define HRM plugin url
     */
    define( 'RT_HRM_URL', plugin_dir_url( __FILE__ ) );
}
if ( !defined( 'RT_HRM_PATH_APP' ) ) {
    /**
     * define HRM App folder path
     */
    define( 'RT_HRM_PATH_APP', plugin_dir_path( __FILE__ ) . 'app/' );
}
if ( !defined( 'RT_HRM_PATH_ADMIN' ) ) {
    /**
     * define HRM admin folder path
     */
    define( 'RT_HRM_PATH_ADMIN', plugin_dir_path( __FILE__ ) . 'app/admin/' );
}
if ( !defined( 'RT_HRM_PATH_MODELS' ) ) {
    /**
     * define HRM models folder path
     */
    define( 'RT_HRM_PATH_MODELS', plugin_dir_path( __FILE__ ) . 'app/models/' );
}
if ( !defined( 'RT_HRM_PATH_SCHEMA' ) ) {
    /**
     * define HRM schema folder path
     */
    define( 'RT_HRM_PATH_SCHEMA', plugin_dir_path( __FILE__ ) . 'app/schema/' );
}
if ( !defined( 'RT_HRM_PATH_LIB' ) ) {
    /**
     * define HRM lib folder path
     */
    define( 'RT_HRM_PATH_LIB', plugin_dir_path( __FILE__ ) . 'app/lib/' );
}
if ( !defined( 'RT_HRM_PATH_HELPER' ) ) {
    /**
     * define HRM helper folder path
     */
    define( 'RT_HRM_PATH_HELPER', plugin_dir_path( __FILE__ ) . 'app/helper/' );
}
if ( !defined( 'RT_HRM_PATH_TEMPLATES' ) ) {
    /**
     * define HRM template folder path
     */
    define( 'RT_HRM_PATH_TEMPLATES', plugin_dir_path( __FILE__ ) . 'templates/' );
}

/**
 * auto-loader for rt-hrm plugin classes
 */
function rt_hrm_include() {

	include_once RT_HRM_PATH_LIB . 'wp-helpers.php';

	include_once RT_HRM_PATH_HELPER . 'rthrm-functions.php';

	global $rthrm_app_autoload, $rthrm_admin_autoload, $rthrm_models_autoload, $rthrm_helper_autoload, $rt_calendar_autoload, $rt_form_autoload, $rtb_settings_autoload;
	$rthrm_app_autoload = new RT_WP_Autoload( RT_HRM_PATH_APP );
	$rthrm_admin_autoload = new RT_WP_Autoload( RT_HRM_PATH_ADMIN );
	$rthrm_models_autoload = new RT_WP_Autoload( RT_HRM_PATH_MODELS );
	$rthrm_helper_autoload = new RT_WP_Autoload( RT_HRM_PATH_HELPER );
	$rt_form_autoload = new RT_WP_Autoload( RT_HRM_PATH_LIB . 'rtformhelpers/' );
	$rt_calendar_autoload = new RT_WP_Autoload( RT_HRM_PATH_LIB . 'rt-calendar/' );
	$rtb_settings_autoload = new RT_WP_Autoload( RT_HRM_PATH . 'app/settings/' );
}

/**
 * Initialization of HRM plugins
 */
function rt_hrm_init() {

	rt_hrm_include();

	global $rt_wp_hrm;
	$rt_wp_hrm = new RT_WP_HRM();

}
add_action( 'rt_biz_init', 'rt_hrm_init', 1 );
