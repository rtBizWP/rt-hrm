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

	global $rthrm_app_autoload, $rthrm_admin_autoload, $rthrm_models_autoload, $rthrm_helper_autoload, $rthrm_calendar_autoload, $rthrm_form_autoload, $rthrm_settings_autoload;
        $rthrm_app_autoload = new RT_WP_Autoload( RT_HRM_PATH_APP );
	$rthrm_admin_autoload = new RT_WP_Autoload( RT_HRM_PATH_ADMIN );
	$rthrm_models_autoload = new RT_WP_Autoload( RT_HRM_PATH_MODELS );
	$rthrm_helper_autoload = new RT_WP_Autoload( RT_HRM_PATH_HELPER );
	$rthrm_form_autoload = new RT_WP_Autoload( RT_HRM_PATH_LIB . 'rtformhelpers/' );
	$rthrm_calendar_autoload = new RT_WP_Autoload( RT_HRM_PATH_LIB . 'rt-calendar/' );
	$rthrm_settings_autoload = new RT_WP_Autoload( RT_HRM_PATH . 'app/settings/' );
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


function check_rt_biz_dependecy() {
            $flag = true;
            $used_function = array(
                'rt_biz_sanitize_module_key',
                'rt_biz_get_access_role_cap',
                'rt_biz_get_person_for_wp_user',
                'rt_biz_get_access_role_cap',
                'rt_biz_get_wp_user_for_person',
                'rt_biz_search_employees'
            );

            foreach ( $used_function as $fn ) {
                if ( ! function_exists( $fn ) ) {
                    $flag = false;
                }
            }

            if ( ! class_exists( 'Rt_Biz' ) ) {
                $flag = false;
            }

            if ( ! $flag ) {
                add_action( 'admin_notices','rt_biz_admin_notice' );
            }

            return $flag;
}

    add_action( 'admin_init', 'check_rt_biz_dependecy', 1 );

    function rt_biz_admin_notice() { ?>
			<div class="updated">
				<p><?php _e( sprintf( 'WordPress HRM : It seems that WordPress rt-biz plugin is not installed or activated. Please %s / %s it.', '<a href="'.admin_url( 'plugin-install.php?tab=search&s=rt-biz' ).'">'.__( 'install' ).'</a>', '<a href="'.admin_url( 'plugins.php' ).'">'.__( 'activate' ).'</a>' ) ); ?></p>
			</div>
		<?php }