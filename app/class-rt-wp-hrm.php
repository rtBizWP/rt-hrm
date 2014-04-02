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
 * Description of RT_WP_HRM
 *
 * @author Dipesh
 */
if ( ! class_exists( 'RT_WP_HRM' ) ) {
	class RT_WP_HRM {

		public $templateURL;

		public function __construct() {

			$this->check_p2p_dependency();
			$this->check_rt_contacts_dependecy();

			$this->init_globals();

			add_action( 'init', array( $this, 'admin_init' ), 5 );
			add_action( 'init', array( $this, 'init' ), 6 );

			add_action( 'wp_enqueue_scripts', array( $this, 'loadScripts' ) );
		}

		function admin_init() {
			$this->templateURL = apply_filters('rthrm_template_url', 'rthrm/');

			$this->update_database();

			global $rt_hrm_admin;
			$rt_hrm_admin = new Rt_HRM_Admin();

		}

		function check_p2p_dependency() {
			if ( ! class_exists( 'P2P_Box_Factory' ) ) {
				add_action( 'admin_notices', array( $this, 'p2p_admin_notice' ) );
			}
		}

		function check_rt_contacts_dependecy() {
			if ( ! class_exists( 'Rt_Contacts' ) ) {
				add_action( 'admin_notices', array( $this, 'rt_contacts_admin_notice' ) );
			}
		}

		function rt_contacts_admin_notice() { ?>
			<div class="updated">
				<p><?php _e( sprintf( 'WordPress HRM : It seems that WordPress Contacts plugin is not installed or activated. Please %s / %s it.', '<a href="'.admin_url( 'plugin-install.php?tab=search&s=rt-contacts' ).'">'.__( 'install' ).'</a>', '<a href="'.admin_url( 'plugins.php' ).'">'.__( 'activate' ).'</a>' ) ); ?></p>
			</div>
		<?php }

		function p2p_admin_notice() { ?>
			<div class="updated">
				<p><?php _e( sprintf( 'WordPress HRM : It seems that Posts 2 Posts plugin is not installed or activated. Please %s / %s it.', '<a href="'.admin_url( 'plugin-install.php?tab=search&s=posts-2-posts' ).'">'.__( 'install' ).'</a>', '<a href="'.admin_url( 'plugins.php' ).'">'.__( 'activate' ).'</a>' ) ); ?></p>
			</div>
		<?php }

		function init_globals() {
			global $rt_hrm_plugin_info, $rt_hrm_roles, $rt_hrm_module, $rt_hrm_dashboard, $rt_hrm_calendar, $rt_calendar;

			$rt_hrm_plugin_info = new RT_Plugin_Info( trailingslashit( RT_HRM_PATH ) . 'index.php' );

			$rt_hrm_module = new Rt_HRM_Module();
			$rt_hrm_roles = new Rt_HRM_Roles();
			$rt_hrm_dashboard = new Rt_HRM_Dashboard();
			$rt_hrm_calendar = new Rt_HRM_Calendar();
			$rt_calendar = new RT_Calendar();

		}

		function init() {
		}

		function update_database() {
			$updateDB = new RT_DB_Update( trailingslashit( RT_HRM_PATH ) . 'index.php', trailingslashit( RT_HRM_PATH ) . 'schema' );
			$updateDB->do_upgrade();
		}

		function loadScripts() {
			global $wp_query, $rt_hrm_module;

			if ( !isset($wp_query->query_vars['name']) ) {
				return;
			}

			$name = $wp_query->query_vars['name'];

			$post_type = rthrm_post_type_name( $name );
			if( $post_type != $rt_hrm_module->post_type ) {
				return;
			}

			if( !isset( $_REQUEST['rthrm_unique_id'] ) || (isset($_REQUEST['rthrm_unique_id']) && empty($_REQUEST['rthrm_unique_id'])) ) {
				return;
			}

			$args = array(
				'meta_key' => 'rthrm_unique_id',
				'meta_value' => $_REQUEST['rthrm_unique_id'],
				'post_status' => 'any',
				'post_type' => $post_type,
			);

			$leadpost = get_posts( $args );
			if( empty( $leadpost ) ) {
				return;
			}
			$lead = $leadpost[0];
			if( $post_type != $lead->post_type ) {
				return;
			}


			$this->localize_scripts();
		}

		function localize_scripts() {

			$unique_id = $_REQUEST['rthrm_unique_id'];
			$args = array(
				'meta_key' => 'rthrm_unique_id',
				'meta_value' => $unique_id,
				'post_status' => 'any',
				'post_type' => rthrm_get_all_post_types(),
			);
			$leadpost = get_posts( $args );
			if( empty( $leadpost ) ) {
				return;
			}
			$lead = $leadpost[0];

			$user_edit = false;

		}
	}
}
