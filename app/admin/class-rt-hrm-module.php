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
 * Description of Rt_HRM_Module
 *
 * @author Dipesh
 */
if( !class_exists( 'Rt_HRM_Module' ) ) {
	class Rt_HRM_Module {

		var $post_type = 'rt_leave';
		var $name = 'HRM';
		var $labels = array();
		var $statuses = array();

		public function __construct() {
			$this->get_custom_labels();
			$this->get_custom_statuses();
			$this->init_hrm();
			$this->hooks();
		}

		function init_hrm() {
			$menu_position = 30;
			$this->register_custom_post( $menu_position );
			$this->register_custom_statuses();

			$settings = get_site_option( 'rt_wp_hrm_settings', false );
			if ( isset( $settings['attach_contacts'] ) && $settings['attach_contacts'] == 'yes' && function_exists( 'rt_contacts_register_person_connection' ) ) {
				rt_contacts_register_person_connection( $this->post_type, $this->labels['name'] );
			}
			if ( isset( $settings['attach_accounts'] ) && $settings['attach_accounts'] == 'yes' && function_exists( 'rt_contacts_register_organization_connection' ) ) {
				rt_contacts_register_organization_connection( $this->post_type, $this->labels['name'] );
			}

		}

		function hooks() {
			add_action( 'admin_menu', array( $this, 'register_custom_pages' ), 1 );
			add_filter( 'custom_menu_order', array($this, 'custom_pages_order') );
			add_filter( 'post_row_actions', array( $this, 'post_row_action' ), 10, 2 );
		}

		function post_row_action( $action, $post ) {
			$post_type = get_post_type( $post );
			if ( $post_type != $this->post_type ) {
				return $action;
			}
			$title = __( 'Edit' );
			$action['edit'] = "<a href='" . admin_url("edit.php?post_type={$this->post_type}&page=rthrm-add-{$this->post_type}&{$this->post_type}_id=" . $post->ID) . "' title='" . $title . "'>" . $title . "</a>";
			return $action;
		}

		function register_custom_pages() {
			global $rt_hrm_dashboard, $rt_hrm_calendar;

			$screen_id = add_submenu_page( 'edit.php?post_type='.$this->post_type, __( 'Dashboard' ), __( 'Dashboard' ), 'read_'.$this->post_type, 'rthrm-'.$this->post_type.'-dashboard', array( $this, 'dashboard' ) );
			$rt_hrm_dashboard->add_screen_id( $screen_id );
			$rt_hrm_dashboard->setup_dashboard();

			/* Metaboxes for dashboard widgets */
			add_action( 'add_meta_boxes', array( $this, 'add_dashboard_widgets' ) );

			$screen_id = add_submenu_page( 'edit.php?post_type='.$this->post_type, __( 'Calendar' ), __( 'Calendar' ), 'read_'.$this->post_type, 'rthrm-'.$this->post_type.'-calendar', array( $this, 'calendar_view' ) );
			$rt_hrm_calendar->add_screen_id( $screen_id );
			$rt_hrm_calendar->setup_calendar();
		}

		function footer_scripts() { ?>
			<script>postboxes.add_postbox_toggles(pagenow);</script>
		<?php }

		function leads_table_set_option($status, $option, $value) {
			return $value;
		}

		function add_screen_options() {

			$option = 'per_page';
			$args = array(
				'label' => $this->labels['all_items'],
				'default' => 10,
				'option' => $this->post_type.'_per_page',
			);
			add_screen_option($option, $args);
			//new Rt_HRM_Leads_List_View();
		}

		function custom_pages_order($menu_order) {
			global $submenu;
			if ( isset( $submenu['edit.php?post_type='.$this->post_type] ) && !empty( $submenu['edit.php?post_type='.$this->post_type] ) ) {
				$module_menu = $submenu['edit.php?post_type='.$this->post_type];
				foreach ( $module_menu as $p_key => $item ) {
					$key = array_search( 'rthrm-'.$this->post_type.'-dashboard', $item );
					if ( $key != FALSE ) {
						$submenu['edit.php?post_type='.$this->post_type] = array( 2 => $submenu['edit.php?post_type='.$this->post_type][$p_key] ) + $submenu['edit.php?post_type='.$this->post_type];
						unset($submenu['edit.php?post_type='.$this->post_type][$p_key]);
					}
				}
			}
			return $menu_order;
		}

		function register_custom_post( $menu_position ) {
			$hrm_logo_url = get_site_option( 'rthrm_logo_url' );

			if ( empty( $hrm_logo_url ) ) {
				$hrm_logo_url = RT_HRM_URL.'app/assets/img/hrm-16X16.png';
			}

			$args = array(
				'labels' => $this->labels,
				'public' => false,
				'publicly_queryable' => false,
				'show_ui' => true, // Show the UI in admin panel
				'menu_icon' => $hrm_logo_url,
				'menu_position' => $menu_position,
				'supports' => array('title', 'editor', 'comments', 'custom-fields'),
				'capability_type' => $this->post_type,
			);
			register_post_type( $this->post_type, $args );
		}

		function register_custom_statuses() {
			foreach ($this->statuses as $status) {

				register_post_status($status['slug'], array(
					'label' => $status['slug']
					, 'protected' => true
					, '_builtin' => false
					, 'label_count' => _n_noop("{$status['name']} <span class='count'>(%s)</span>", "{$status['name']} <span class='count'>(%s)</span>"),
				));
			}
		}

		function get_custom_labels() {
			$this->labels = array(
				'name' => __( 'Leave' ),
				'singular_name' => __( 'Leave' ),
				'menu_name' => __( 'HRM' ),
				'all_items' => __( 'Leaves' ),
				'add_new' => __( 'Add Leave' ),
				'add_new_item' => __( 'Add Leave' ),
				'new_item' => __( 'Add Leave' ),
				'edit_item' => __( 'Edit Leave' ),
				'view_item' => __( 'View Leave' ),
				'search_items' => __( 'Search Leave' ),
			);
			return $this->labels;
		}

		function get_custom_statuses() {
			$this->statuses = array(
				array(
					'slug' => 'new',
					'name' => 'New',
					'description' => 'New Leave application is created',
				),
				array(
					'slug' => 'pending',
					'name' => 'pending',
					'description' => 'Leave application is pending',
				),
				array(
					'slug' => 'approved',
					'name' => 'approved',
					'description' => 'Leave application is approved',
				),
				array(
					'slug' => 'rejected',
					'name' => 'rejected',
					'description' => 'Leave application is rejected',
				),
			);
			return $this->statuses;
		}

		function calendar_view(){
			global $rt_hrm_calendar;
			$rt_hrm_calendar->ui( $this->post_type );
		}

		function dashboard() {
			global $rt_hrm_dashboard;
			$rt_hrm_dashboard->ui( $this->post_type );
		}

		function add_dashboard_widgets() {
			global $rt_hrm_dashboard;


		}
	}
}
