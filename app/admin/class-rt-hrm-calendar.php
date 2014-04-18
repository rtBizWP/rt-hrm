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
    /**
     * Class Rt_HRM_Calendar
     */
    class Rt_HRM_Calendar {

        /**
         * Variable for calendar page screen id
         * @var string
         */
        var $screen_id;

        /**
         * Object initialization
         */
        public function __construct() {
			$this->screen_id = '';
		}

        /**
         *  Add hooks for calendar page like page action or render calendar
         */
        function setup_calendar() {
			/* Add callbacks for this screen only */
			add_action( 'load-'.$this->screen_id, array( $this, 'page_actions' ), 9 );
			add_action( 'admin_footer-'.$this->screen_id, array( $this, 'footer_scripts' ) );

			/* render data into calendar */
			add_action( 'rthrm_after_calendar', array( $this, 'render_calendar' ) );
		}

		/**
		 * Setter method for screen id
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

        /**
         * Actions to be taken prior to page loading. This is after headers have been set.
         * Handle calendar page event
         * call on load-$hook
         */
        function page_actions() {
			global $rt_hrm_module, $rt_hrm_attributes;
			if ( isset( $_REQUEST['page'] ) && $_REQUEST['page'] === 'rthrm-'.$rt_hrm_module->post_type.'-calendar' && isset( $_REQUEST['form-add-leave'] ) && !empty( $_REQUEST['form-add-leave'] ) ) {

				$leave_meta = $_REQUEST['post'];

				$newLeave = array(
					'comment_status' =>  'closed',
					'post_author' => $leave_meta['leave-user-id'],
					'post_date' => date('Y-m-d H:i:s'),
					'post_content' => $leave_meta['leave_description'],
					'post_status' => 'pending',
					'post_title' => ' Leave: ' . $leave_meta['leave-user'],
					'post_type' => $rt_hrm_module->post_type,
				);

				$newLeaveID = wp_insert_post($newLeave);

				if ( isset( $leave_meta[  Rt_HRM_Attributes::$leave_type_tax] ) ) {
					wp_set_post_terms( $newLeaveID, implode( ',', array_map( 'intval', $leave_meta[Rt_HRM_Attributes::$leave_type_tax] ) ), Rt_HRM_Attributes::$leave_type_tax );
				}

                update_post_meta( $newLeaveID, 'leave-user', $leave_meta['leave-user'] );
                update_post_meta( $newLeaveID, 'leave-user-id', $leave_meta['leave-user-id'] );
                update_post_meta( $newLeaveID, 'leave-duration', $leave_meta['leave-duration'] );
				update_post_meta( $newLeaveID, 'leave-start-date', $leave_meta['leave-start-date'] );

				if ( $leave_meta['leave-duration'] == 'other' ){
					update_post_meta( $newLeaveID, 'leave-end-date', $leave_meta['leave-end-date'] );
				}else {
					delete_post_meta( $newLeaveID, 'leave-end-date' );
				}
                wp_redirect( admin_url( 'edit.php?post_type=rt_leave&page=rthrm-rt_leave-calendar' ) );
			}
		}


        /**
         * render calendar template
         * @param $post_type
         */
        function ui( $post_type ) {
			global $current_user;
            $current_employee = rt_biz_get_contact_for_wp_user( get_current_user_id( ) );
            if ( isset( $current_employee ) && !empty( $current_employee ) ){
                $current_employee=$current_employee[0];
            }
			$arg = array(
				'post_type' => $post_type,
				'is_hrm_manager' => current_user_can( rt_biz_get_access_role_cap( RT_HRM_TEXT_DOMAIN, 'admin' ) ),
                'current_employee' => $current_employee
			);
			rthrm_get_template( 'admin/calendar.php', $arg );
		}

        /**
         * Render calendar event
         * call on rthrm_after_calendar hook in template -> admin -> calendar.php
         */
        function render_calendar() {
			global $rt_calendar, $rt_hrm_module;
            $args = array('post_type' => $rt_hrm_module->post_type,'post_status' => 'pending,approved,rejected');
            if ( ! current_user_can( rt_biz_get_access_role_cap( RT_HRM_TEXT_DOMAIN, 'admin' ) )  ) {
                $args['author'] = get_current_user_id();
            }
            $the_query = new WP_Query( $args );
            $event= array();
            while ( $the_query->have_posts() ) : $the_query->the_post();

                // Leave Status
                $color='';
                if ( get_post_status() == 'approved'){
                    $color=  Rt_HRM_Settings::$settings['approved_leaves_color'];
                }elseif (get_post_status() == 'rejected' ){
                    $color=  Rt_HRM_Settings::$settings['rejected_leaves_color'];
                } elseif (get_post_status() == 'pending' ){
                    $color=  Rt_HRM_Settings::$settings['pending_leaves_color'];
                }

                $leaveStartDate = get_post_meta( get_the_id(), 'leave-start-date', false );
                if ( isset ( $leaveStartDate ) && !empty( $leaveStartDate ) ){
                    $leaveStartDate = DateTime::createFromFormat( 'd/m/Y', $leaveStartDate[0] );
                }
                $leaveStartDate = $leaveStartDate->format('Y-m-d');
                $leaveEndDate = get_post_meta( get_the_id(), 'leave-end-date', false);
                if ( isset ( $leaveEndDate ) && !empty( $leaveEndDate ) ){
                    $leaveEndDate = $leaveEndDate[0];
                }
                if ( isset( $leaveEndDate) && !empty ( $leaveEndDate )  ){
                    $leaveEndDate = DateTime::createFromFormat( 'd/m/Y', $leaveEndDate );
                    $leaveEndDate = $leaveEndDate->format('Y-m-d');
                }else{
                    $leaveEndDate = $leaveStartDate;
                }

                //Leave
                $temp=array(
                    'title'=> get_the_title(),
                    'start'=> $leaveStartDate,
                    'end'=> $leaveEndDate,
                    'color'=> $color,
                    'textColor'=> Rt_HRM_Settings::$settings['leaves_text_color'],
                    'leave_id' => get_the_id(),
                );
                $event[]=$temp;
            endwhile;
            wp_reset_postdata();

			$rt_calendar->setDomElement("#calendar-container");
			$rt_calendar->setPopupElement(".leave-insert-dialog");
			$rt_calendar->setEvent($event);
			$rt_calendar->render_calendar();
		}

	}
}