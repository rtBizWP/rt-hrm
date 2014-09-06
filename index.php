<?php

/*
  Plugin Name: Voxxi HRM
  Plugin URI: http://voxxi.me
  Description: Part of the Voxxi Business Suite
  Version: 0.0.1
  Author: Voxxi
  Author URI: http://voxxi.me
  License: GPL
  Text Domain: rt_hrm
 */

if ( !defined( 'RT_HRM_VERSION' ) ) {
    /**
     * define HRM version
     */
    define( 'RT_HRM_VERSION', '0.0.1' );
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

	global $rthrm_app_autoload, $rthrm_admin_autoload, $rthrm_models_autoload, $rthrm_helper_autoload, $rthrm_calendar_autoload, $rthrm_form_autoload, $rthrm_settings_autoload, $rthrm_buddypress_autoload, $rt_hrm_buddypress_hrm;
	$rthrm_app_autoload = new RT_WP_Autoload( RT_HRM_PATH_APP );
	$rthrm_admin_autoload = new RT_WP_Autoload( RT_HRM_PATH_ADMIN );
	$rthrm_models_autoload = new RT_WP_Autoload( RT_HRM_PATH_MODELS );
	$rthrm_helper_autoload = new RT_WP_Autoload( RT_HRM_PATH_HELPER );
	$rthrm_form_autoload = new RT_WP_Autoload( RT_HRM_PATH_LIB . 'rtformhelpers/' );
	$rthrm_calendar_autoload = new RT_WP_Autoload( RT_HRM_PATH_LIB . 'rt-calendar/' );
	$rthrm_settings_autoload = new RT_WP_Autoload( RT_HRM_PATH . 'app/settings/' );
	$rthrm_buddypress_autoload = new RT_WP_Autoload( RT_HRM_PATH . 'app/buddypress-components/rt-hrm-componet/' );
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

function rt_hrm_check_dependency() {
	global $rt_wp_hrm;
	if ( empty( $rt_wp_hrm ) ) {
		rt_hrm_include();
		$rt_wp_hrm = new RT_WP_HRM();
	}
}
add_action( 'init', 'rt_hrm_check_dependency' );
