<?php
/**
 * Installation related functions and actions.
 *
 * @author   paresh
 * @category Admin
 * @package  rthrm/Classes
 * @version  1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Rt_HRM_Install Class
 */
class Rt_HRM_Install {

    /**
     * Hook in tabs.
     */
    public static function init() {
        add_action( 'admin_init', array( __CLASS__, 'check_version' ), 5 );
        add_action( 'admin_init', array( __CLASS__, 'install_actions' ) );

    }

    /**
     * check_version function.
     */
    public static function check_version() {
        if ( ! defined( 'IFRAME_REQUEST' ) && ( get_option( 'rthrm_version' ) != RT_HRM_VERSION ) ) {
            self::install();
            do_action( 'rthrm_updated' );
        }
    }

    /**
     * Install actions such as installing pages when a button is clicked.
     */
    public static function install_actions() {
        if ( ! empty( $_GET['do_update_rthrm'] ) ) {
            self::update();
        }
    }

    /**
     * Install RH
     */
    public static function install() {

        if ( ! defined( 'RH_INSTALLING' ) ) {
            define( 'RH_INSTALLING', true );
        }

        self::create_roles();

        self::update_rb_version();

         // Trigger action
        do_action( 'rthrm_installed' );
    }

    /**
     * Update RH version to current
     */
    private static function update_rb_version() {
        delete_option( 'rthrm_version' );
        add_option( 'rthrm_version', RT_HRM_VERSION );
    }

    /**
     * Update DB version to current
     */
    private static function update_db_version( $version = null ) {
        delete_option( 'rthrm_db_version' );
        add_option( 'rthrm_db_version', is_null( $version ) ? RT_HRM_VERSION : $version );
    }

    /**
     * Handle updates
     */
    private static function update() {
        $current_db_version = get_option( 'rthrm_db_version' );

        foreach ( self::$db_updates as $version => $updater ) {
            if ( version_compare( $current_db_version, $version, '<' ) ) {
                include( $updater );
                self::update_db_version( $version );
            }
        }

        self::update_db_version();
    }

    /**
     * Create roles and capabilities
     */
    public static function create_roles() {
        global $wp_roles;

        if ( ! class_exists( 'WP_Roles' ) ) {
            return;
        }

        if ( ! isset( $wp_roles ) ) {
            $wp_roles = new WP_Roles();
        }

        // Customer role
        add_role( 'voxxi_hrm_no_roles', __( 'Voxxi HRM No Roles', 'rtbiz' ), array(
            'read' 						=> true,
        ) );

        add_role( 'voxxi_hrm_author', __( 'Voxxi HRM Author', 'rtbiz' ), array(

            'hrm_calender' => true,

            //leave caps
            'hrm_delete_leaves'  => true,
            'hrm_delete_published_leaves'  => true,
            'hrm_edit_leaves' => true,
            'hrm_edit_leaves' => true,
            'hrm_edit_published_leaves' => true,
            'hrm_publish_leaves' => true,
            'hrm_read_leave' => true,
            'hrm_upload_files' => true,
            'hrm_create_leaves' => true,
            'voxxi_hrm' => true,
        ) );

        add_role( 'voxxi_hrm_editor', __( 'Voxxi HRM Editor', 'rtbiz' ), array(

            'hrm_calender' => true,
            'hrm_leave_approve' => true,

            'hrm_delete_others_leaves' => true,
            'hrm_delete_private_leaves' => true,
            'hrm_delete_published_leaves' => true,
            'hrm_edit_others_leaves' => true,
            'hrm_edit_leaves' => true,
            'hrm_edit_leave' => true,
            'hrm_edit_private_leaves' => true,
            'hrm_edit_published_leaves' => true,
            'hrm_publish_leaves' => true,
            'hrm_read_leaves' => true,
            'hrm_read_private_leaves' => true,
            'hrm_unfiltered_html' => true,
            'hrm_upload_files' => true,
            'hrm_create_leaves' => true,
            'voxxi_hrm' => true,

        ) );

        $capabilities = array(

            'hrm_calender' => true,
            'hrm_leave_approve' => true,

            'hrm_delete_others_leaves' => true,
            'hrm_delete_private_leaves' => true,
            'hrm_delete_published_leaves' => true,
            'hrm_edit_others_leaves' => true,
            'hrm_edit_leaves' => true,
            'hrm_edit_leave' => true,
            'hrm_edit_private_leaves' => true,
            'hrm_edit_published_leaves' => true,
            'hrm_publish_leaves' => true,
            'hrm_read_leaves' => true,
            'hrm_read_private_leaves' => true,
            'hrm_unfiltered_html' => true,
            'hrm_upload_files' => true,
            'hrm_create_leaves' => true,

            'hrm_manage_leave_types' => true,
            'hrm_edit_leave_types' => true,
            'hrm_delete_leave_types' => true,
            'hrm_assign_leave_types' => true,

            'hrm_settings' => true,
            'voxxi_hrm' => true,
            'hrm_attributes' => true,
        );

        // Shop manager role
        add_role( 'voxxi_hrm_administrator', __( 'Voxxi HRM Administrator', 'rtbiz' ), $capabilities );


        foreach ( $capabilities as $cap ) {
            $wp_roles->add_cap( 'administrator', $cap );
        }
    }
}

Rt_HRM_Install::init();
