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
 * Description of class-rt-hrm-settings
 *
 * @author udit
 */
if ( ! class_exists( 'Rt_HRM_Settings' ) ) {

	/**
	 * Class Rt_HRM_Settings
	 */
	class Rt_HRM_Settings {

		/**
		 * @var TitanFramework
		 */
		public static $titan_obj;

		/**
		 * @var - saved Settings
		 */
		public static $settings;

		/**
		 *
		 */
		public function __construct() {

			// Proceed only if Titan Framework Found

			if ( ! $this->embedd_titan_framework() ) {
				return;
			}

			// Init Titan Instance
			self::$titan_obj = $this->get_settings_instance();

			// Init Titan Settings
			add_action( 'plugins_loaded', array( $this, 'init_settings' ), 20 );
			// Load Saved Settings Values
			add_action( 'after_setup_theme', array( $this, 'load_settings' ) );
		}

		/**
		 *  Load Settings
		 */
		function load_settings() {
			self::$settings['menu_label'] = ( isset( self::$titan_obj ) && ! empty( self::$titan_obj ) ) ? self::$titan_obj->getOption( 'menu_label' ) : '';
			self::$settings['logo_url'] = ( isset( self::$titan_obj ) && ! empty( self::$titan_obj ) ) ? self::$titan_obj->getOption( 'logo_url' ) : '';
			global $rt_hrm_module;
			foreach ( $rt_hrm_module->statuses as $key => $status ) {
				self::$settings[$status['slug'].'_leaves_color'] = ( isset( self::$titan_obj ) && ! empty( self::$titan_obj ) ) ? self::$titan_obj->getOption( $status['slug'].'_leaves_color' ) : '';
			}
			self::$settings['leaves_text_color'] = ( isset( self::$titan_obj ) && ! empty( self::$titan_obj ) ) ? self::$titan_obj->getOption( 'leaves_text_color' ) : '';
			self::$settings['leaves_quota_per_user'] = ( isset( self::$titan_obj ) && ! empty( self::$titan_obj ) ) ? intval( self::$titan_obj->getOption( 'leaves_quota_per_user' ) ) : 0;
			self::$settings['is_user_allowed_to_upload_edit_docs'] = ( isset( self::$titan_obj ) && ! empty( self::$titan_obj ) ) ? self::$titan_obj->getOption( 'is_user_allowed_to_upload_edit_docs' ) : '';
			self::$settings['storage_quota_per_user'] = ( isset( self::$titan_obj ) && ! empty( self::$titan_obj ) ) ? intval( self::$titan_obj->getOption( 'storage_quota_per_user' ) ) : 0;
		}

		/**
		 *  Init Settings
		 */
		function init_settings() {

			global $rt_hrm_module;

			if ( ! isset( self::$titan_obj ) || empty( self::$titan_obj ) ) {
				return;
			}

			$admin_cap = rt_biz_get_access_role_cap( RT_HRM_TEXT_DOMAIN, 'admin' );

			$settings_page = self::$titan_obj->createAdminPanel( array(
				'name' => __( 'Settings' ), // Name of the menu item
				'title' => __( 'Settings' ), // Title displayed on the top of the admin panel
				'parent' => 'edit.php?post_type='.$rt_hrm_module->post_type, // id of parent, if blank, then this is a top level menu
				'id' => RT_WP_HRM::$settings_page_slug, // Unique ID of the menu item
				'capability' => $admin_cap, // User role
				'position' => 10, // Menu position. Can be used for both top and sub level menus
				'use_form' => true, // If false, options will not be wrapped in a form
			) );
			$general_tab = $settings_page->createTab( array(
				'name' => __( 'General' ), // Name of the tab
				'id' => 'general', // Unique ID of the tab
				'title' => __( 'General' ), // Title to display in the admin panel when tab is active
			) );
			$general_tab->createOption( array(
				'name' => __( 'Menu Label' ), // Name of the option
				'desc' => 'This label will be used for the Menu Item label for rtBiz', // Description of the option
				'id' => 'menu_label', // Unique ID of the option
				'type' => 'text', //
				'default' => __( 'rtHRM' ), // Menu icon for top level menus only
				'example' => '', // An example value for this field, will be displayed in a <code>
				'livepreview' => '', // jQuery script to update something in the site. For theme customizer only
			) );
			$general_tab->createOption( array(
				'name' => __( 'Icon (Logo) URL' ), // Name of the option
				'desc' => 'This logo will be used for all the Menu, Submenu, Post Types Menu Icons in WordPress HRM', // Description of the option
				'id' => 'logo_url', // Unique ID of the option
				'type' => 'text', //
				'default' => RT_HRM_URL . 'app/assets/img/hrm-16X16.png', // Menu icon for top level menus only
				'example' => 'http://google.com/icon.png', // An example value for this field, will be displayed in a <code>
				'livepreview' => '', // jQuery script to update something in the site. For theme customizer only
			) );
			$general_tab->createOption( array(
				'type' => 'save'
			) );
			$leaves_tab = $settings_page->createTab( array(
				'name' => __( 'Leaves' ), // Name of the tab
				'id' => 'leaves', // Unique ID of the tab
				'title' => __( 'Leaves' ), // Title to display in the admin panel when tab is active
			) );
			foreach ( $rt_hrm_module->statuses as $key => $status ) {
				$leaves_tab->createOption( array(
					'name' => __( $status['name'].' Leaves Color' ), // Name of the option
					'desc' => 'This color will be used to mark '.$status['name'].' Leaves', // Description of the option
					'id' => $status['slug'].'_leaves_color', // Unique ID of the option
					'type' => 'color', //
					'default' => $status['color'], // Menu icon for top level menus only
				) );
			}
			$leaves_tab->createOption( array(
				'name' => __( 'Leaves Text Color' ), // Name of the option
				'desc' => 'This color will be used for Leaves Marker Text', // Description of the option
				'id' => 'leaves_text_color', // Unique ID of the option
				'type' => 'color', //
				'default' => '#FFFFFF', // Menu icon for top level menus only
			) );
			$leaves_tab->createOption( array(
				'name' => __( 'Paid Leaves Quota per User' ), // Name of the option
				'desc' => '', // Description of the option
				'id' => 'leaves_quota_per_user', // Unique ID of the option
				'type' => 'number', //
				'default' => 10, // Menu icon for top level menus only
				'example' => 'Every user will get this much paid leaves. It can be overriden for any user from his/her profile.', // An example value for this field, will be displayed in a <code>
				'livepreview' => '', // jQuery script to update something in the site. For theme customizer only
			) );
			$leaves_tab->createOption( array(
				'type' => 'save'
			) );
			$doc_tab = $settings_page->createTab( array(
				'name' => __( 'Documents' ), // Name of the tab
				'id' => 'documents', // Unique ID of the tab
				'title' => __( 'Documents' ), // Title to display in the admin panel when tab is active
			) );
			$doc_tab->createOption( array(
				'name' => __( 'Allow users to upload/remove documents from their profile page ?' ), // Name of the option
				'desc' => 'This is a checkbox which decides whether users/employees are allowed to upload/edit their documents from their profile page or not.', // Description of the option
				'id' => 'is_user_allowed_to_upload_edit_docs', // Unique ID of the option
				'type' => 'checkbox', //
				'default' => 0, // Menu icon for top level menus only
			) );
			$doc_tab->createOption( array(
				'name' => __( 'Storage Quota per User' ), // Name of the option
				'desc' => '', // Description of the option
				'id' => 'storage_quota_per_user', // Unique ID of the option
				'type' => 'number', //
				'default' => 10, // Menu icon for top level menus only
				'example' => 'Every user will get this much storage quota to upload their documents. If 10 is given; 10MB will be calculated.', // An example value for this field, will be displayed in a <code>
				'livepreview' => '', // jQuery script to update something in the site. For theme customizer only
			) );
			$doc_tab->createOption( array(
				'type' => 'save'
			) );
		}

		/**
		 * @return TitanFramework
		 */
		function get_settings_instance() {
			return TitanFramework::getInstance( RT_HRM_TEXT_DOMAIN );
		}

		/**
		 * @return bool
		 */
		function is_plugin_activation_action() {
			// Don't do anything when we're activating a plugin to prevent errors
			// on redeclaring Titan classes
			if ( ! empty( $_GET[ 'action' ] ) && ! empty( $_GET[ 'plugin' ] ) ) {
				if ( $_GET[ 'action' ] == 'activate' ) {
					return true;
				}
			}
			return false;
		}

		/**
		 * @return bool
		 */
		function is_titan_activated() {
			// Check if the framework plugin is activated
			$activePlugins = get_option( 'active_plugins' );
			if ( is_array( $activePlugins ) ) {
				foreach ( $activePlugins as $plugin ) {
					if ( is_string( $plugin ) ) {
						if ( stripos( $plugin, '/titan-framework.php' ) !== false ) {
							return true;
						}
					}
				}
			}
			return false;
		}

		/**
		 * @return bool
		 */
		function embedd_titan_framework() {
			/*
			 * When using the embedded framework, use it only if the framework
			 * plugin isn't activated.
			 */

			if ( $this->is_plugin_activation_action() ) {
				return false;
			}

			// Titan Already available as Plugin
			if ( $this->is_titan_activated() ) {
				return true;
			}

			// Use the embedded Titan Framework
			if ( ! class_exists( 'TitanFramework' ) ) {
				require_once( RT_HRM_PATH . 'app/vendor/titan-framework/titan-framework.php' );
			}
			return true;
		}
	}
}
