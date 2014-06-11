<?php
/**
 * Don't load this file directly!
 */
if ( ! defined( 'ABSPATH' ) )
	exit;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Rt_HRM_Admin
 *
 * @author Dipesh
 */
if( !class_exists( 'Rt_HRM_Admin' ) ) {
    /**
     * Class Rt_HRM_Admin
     */
    class Rt_HRM_Admin {

        /**
         * Object initialization
         */
        public function __construct() {
			if ( is_admin() ) {
				$this->hooks();
			}
		}

        /**
         * Load script & style fot rt-hrm plugin
         */
        function load_styles_scripts() {
			global $post, $rt_hrm_module;
			$pagearray = array( 'rthrm-'.$rt_hrm_module->post_type.'-calendar', 'rthrm-'.$rt_hrm_module->post_type.'-dashboard' );
			if( ( isset( $post->post_type ) && $post->post_type == $rt_hrm_module->post_type )
					|| ( isset( $_REQUEST['page'] ) && in_array( $_REQUEST['page'], $pagearray ) )
					|| ( isset( $_REQUEST['post_type'] ) && $_REQUEST['post_type'] == $rt_hrm_module->post_type ) ) {

				if ( !  wp_style_is( 'rt-jquery-ui-css' ) ) {
	                wp_enqueue_style('rt-jquery-ui-css', RT_HRM_URL . 'app/assets/css/jquery-ui-1.9.2.custom.css', false, RT_HRM_VERSION, 'all');
				}
				wp_enqueue_style('rthrm-admin-css', RT_HRM_URL . 'app/assets/css/admin.css', false, RT_HRM_VERSION, 'all');

                if( !wp_script_is('jquery-ui-datepicker') ) {
                    wp_enqueue_script( 'jquery-ui-datepicker' );
                }

                if( !wp_script_is('jquery-ui-autocomplete') ) {
                    wp_enqueue_script('jquery-ui-autocomplete', '', array('jquery-ui-widget', 'jquery-ui-position'), '1.9.2',true);
                }

				wp_enqueue_script('rthrm-admin-js', RT_HRM_URL . 'app/assets/javascripts/admin.js','jquery', RT_HRM_VERSION, true);
				wp_enqueue_script('rt-hrm-moment-js', RT_HRM_URL . 'app/assets/javascripts/moment.min.js','jquery', "", true);

				wp_localize_script( 'rthrm-admin-js', 'ajaxurl', admin_url( 'admin-ajax.php' ) );
                wp_localize_script( 'rthrm-admin-js', 'adminurl', admin_url() );

			}

			if ( isset( $_REQUEST['page'] ) && $_REQUEST['page'] == 'rthrm-'.$rt_hrm_module->post_type.'-dashboard' ) {
				wp_localize_script('rthrm-admin-js', 'rt_hrm_top_menu', 'menu-posts-'.$rt_hrm_module->post_type);
				wp_localize_script('rthrm-admin-js', 'rt_hrm_dashboard_url', admin_url( 'edit.php?post_type='.$rt_hrm_module->post_type.'&page='.'rthrm-'.$rt_hrm_module->post_type.'-dashboard' ) );
			}

			if ( isset( $_REQUEST['page'] ) && $_REQUEST['page'] == 'rthrm-'.$rt_hrm_module->post_type.'-calendar' ) {

				wp_enqueue_style('rt-hrm-calendar-css', RT_HRM_URL . 'app/lib/rt-calendar/calendar/fullcalendar.css', false, RT_HRM_VERSION, 'all');
				wp_enqueue_script('rt-hrm-calendar-js', RT_HRM_URL . 'app/lib/rt-calendar/calendar/fullcalendar.js','jquery', "", true);
			}
		}

        /**
         * Apply hooks
         */
        function hooks() {
			add_action( 'admin_enqueue_scripts', array( $this, 'load_styles_scripts' ) );

			add_action( 'admin_menu', array( $this, 'register_menu' ), 1 );
			add_action( 'admin_bar_menu', array( $this, 'register_toolbar_menu' ), 100 );

		}

        /**
         *
         */
        function register_menu() {

		}

        /**
         * @param $admin_bar
         */
        function register_toolbar_menu( $admin_bar ) {

		}

        /**
         *
         */
        function user_settings_ui() {

		}

        /**
         *
         */
        function settings_ui() {

		}
	}
}