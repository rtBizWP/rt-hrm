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
 * Description of Rt_HRM_Dashboard
 *
 * @author Dipesh
 */
if ( !class_exists( 'Rt_HRM_Dashboard' ) ) {
	class Rt_HRM_Dashboard {

		var $screen_id;
		var $charts = array();

		public function __construct() {
			$this->screen_id = '';
		}

		function setup_dashboard() {
			/* Add callbacks for this screen only */
			add_action( 'load-'.$this->screen_id, array( $this, 'page_actions' ), 9 );
			add_action( 'admin_footer-'.$this->screen_id, array( $this, 'footer_scripts' ) );

			/* Setup Google Charts */
			add_action( 'rthrm_after_dashboard', array( $this, 'render_google_charts' ) );
		}

		/**
		 *
		 */
		function add_screen_id( $screen_id ) {
			$this->screen_id = $screen_id;
		}

		/**
		 * Prints the jQuery script to initiliase the metaboxes
		 * Called on admin_footer-*
		 */
		function footer_scripts() { ?>
			<script> postboxes.add_postbox_toggles(pagenow);</script>
		<?php }

		/*
		* Actions to be taken prior to page loading. This is after headers have been set.
		* call on load-$hook
		* This calls the add_meta_boxes hooks, adds screen options and enqueues the postbox.js script.
		*/
		function page_actions() {
			global $rt_hrm_module;

			if ( isset( $_REQUEST['page'] ) && $_REQUEST['page'] === 'rthrm-'.$rt_hrm_module->post_type.'-dashboard' ) {
				do_action('add_meta_boxes_' . $this->screen_id, null);
				do_action('add_meta_boxes', $this->screen_id, null);

				/* Enqueue WordPress' script for handling the metaboxes */
				wp_enqueue_script('postbox');
			}
		}

		function ui( $post_type ) {
			//rthrm_get_template( 'admin/dashboard.php', array( 'post_type' => $post_type ) );
			echo 'Dashboard';
		}

		function render_google_charts() {
			global $rt_reports;
			//$rt_reports->render_chart( $this->charts );
		}
	}
}