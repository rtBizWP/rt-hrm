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
	class Rt_HRM_Admin {
		public function __construct() {
			if ( is_admin() ) {
				$this->hooks();
			}
		}

		function load_styles_scripts() {
			$pagearray = array( 'rthrm-gravity-import', 'rthrm-settings', 'rthrm-user-settings', 'rthrm-logs' );
			global $post, $rt_hrm_module;
			if( ( isset( $post->post_type ) && $post->post_type == $rt_hrm_module->post_type )
					|| ( isset( $_REQUEST['page'] ) && in_array( $_REQUEST['page'], $pagearray ) )
					|| ( isset( $_REQUEST['post_type'] ) && $_REQUEST['post_type'] == $rt_hrm_module->post_type ) ) {

			}
			$pagearray = array( 'rthrm-add-module', 'rthrm-gravity-mapper', 'rthrm-add-'.$rt_hrm_module->post_type );
			if ( isset( $_REQUEST['page'] ) && in_array( $_REQUEST['page'], $pagearray ) ) {

			}

			if ( isset( $_REQUEST['page'] ) && $_REQUEST['page'] == 'rthrm-'.$rt_hrm_module->post_type.'-dashboard' ) {

			}

			if ( isset( $_REQUEST['page'] ) && $_REQUEST['page'] == 'rthrm-'.$rt_hrm_module->post_type.'-calendar' ) {
				wp_enqueue_style('rt-hrm-calendar-css', RT_HRM_URL . 'app/assets/calendar/calendar.css', false, RT_HRM_VERSION, 'all');
				wp_enqueue_script('rt-hrm-calendar-js', RT_HRM_URL . 'app/assets/calendar/calendar.js',null, "", true);
			}


			$this->localize_scripts();
		}

		function localize_scripts() {
			global $rt_hrm_module;

			$pagearray = array( 'rthrm-add-module', 'rthrm-gravity-mapper', 'rthrm-add-'.$rt_hrm_module->post_type, 'rthrm-'.$rt_hrm_module->post_type.'-calendar' );
			if( /*wp_script_is( 'rthrm-admin-js' ) &&*/ isset( $_REQUEST['post_type'] ) && isset( $_REQUEST['page'] ) && in_array( $_REQUEST['page'], $pagearray ) ) {
				$user_edit = false;
				if ( current_user_can( "edit_{$rt_hrm_module->post_type}" ) ) {
					$user_edit = true;
				}
				wp_localize_script( 'rt-hrm-calendar-js', 'rthrm_user_edit', array($user_edit) );

			} else {

			}
		}

		function hooks() {
			add_action( 'admin_enqueue_scripts', array( $this, 'load_styles_scripts' ) );

			add_action( 'admin_menu', array( $this, 'register_menu' ), 1 );
			add_action( 'admin_bar_menu', array( $this, 'register_toolbar_menu' ), 100 );

		}

		function register_menu() {

		}

		function register_toolbar_menu( $admin_bar ) {

		}

		function user_settings_ui() {

		}

		function settings_ui() {

		}
	}
}