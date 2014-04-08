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
 * Description of Rt_HRM_Calendar
 *
 * @author Dipesh
 */
if ( !class_exists( 'Rt_HRM_Calendar' ) ) {
	class Rt_HRM_Calendar {

		var $screen_id;

		public function __construct() {
			$this->screen_id = '';
		}

		function setup_calendar() {
			/* Add callbacks for this screen only */
			add_action( 'load-'.$this->screen_id, array( $this, 'page_actions' ), 9 );
			add_action( 'admin_footer-'.$this->screen_id, array( $this, 'footer_scripts' ) );

			/* render data into calendar */
			add_action( 'rthrm_after_calendar', array( $this, 'render_calendar' ) );
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

		<?php }

		/*
		* Actions to be taken prior to page loading. This is after headers have been set.
		* call on load-$hook
		* This calls the add_meta_boxes hooks, adds screen options and enqueues the postbox.js script.
		*/
		function page_actions() {
			global $rt_hrm_module, $rt_hrm_attributes;
			if ( isset( $_REQUEST['page'] ) && $_REQUEST['page'] === 'rthrm-'.$rt_hrm_module->post_type.'-calendar' && isset( $_REQUEST['form-add-leave'] ) && !empty( $_REQUEST['form-add-leave'] ) ) {

				$leave_meta = $_REQUEST['post'];

				$newLeave = array(
					'comment_status' =>  'closed',
					'post_author' => get_current_user_id(),
					'post_date' => date('Y-m-d H:i:s'),
					'post_content' => $leave_meta['leave_description'],
					'post_status' => 'pending',
					'post_title' => $leave_meta['leave_user'],
					'post_type' => $rt_hrm_module->post_type,
				);

				$newLeaveID = wp_insert_post($newLeave);

				$attributes = rthrm_get_attributes( $rt_hrm_module->post_type );
				foreach ( $attributes as $attr ){
					$rt_hrm_attributes->save_attributes( $attr, isset($newLeaveID) ? $newLeaveID : '', $leave_meta );
				}
				update_post_meta( $newLeaveID, 'leave-duration', $leave_meta['leave-duration'] );
				update_post_meta( $newLeaveID, 'leave-start-date', $leave_meta['leave-start-date'] );

				if ( $leave_meta['leave-duration'] == 'other' ){
					update_post_meta( $newLeaveID, 'leave-end-date', $leave_meta['leave-end-date'] );
				}else {
					delete_post_meta( $newLeaveID, 'leave-end-date' );
				}
			}
		}

		function ui( $post_type ) {
			global $current_user;
			$arg = array(
				'post_type' => $post_type,
				'is_hrm_manager' => in_array( 'rt_wp_hrm_manager', $current_user->roles )
			);
			rthrm_get_template( 'admin/calendar.php', $arg );
		}

		function render_calendar() {
			global $rt_calendar;
			$event= array(
				array(
					'title'=> 'Dipesh: Leave',
					'start'=>'2014-04-04',
					'end'=> '2014-04-07',
					'color'=> 'green',
					'textColor'=> 'black',
				),
				array(
					'title'=> 'udit: Leave',
					'end'=> '2014-04-05',
					'color'=> 'red',
					'textColor'=> 'black',
				),
			);
			$rt_calendar->setDomElement("#calendar-container");
			$rt_calendar->setPopupElement(".leave-insert-dialog");
			$rt_calendar->setEvent($event);
			$rt_calendar->render_calendar();
		}

	}
}