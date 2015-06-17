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
        private function __construct() {
			$this->screen_id = '';

	        $this->setup();
		}

	    /**
	     * Get a singleton instance of the class
	     *
	     * @return Rt_HRM_Calendar
	     */
	    public static function factory() {
		    static $instance = false;
		    if ( ! $instance ) {
			    $instance = new self();
		    }
		    return $instance;
	    }


	    /**
         *  Add hooks for calendar page like page action or render calendar
         */
        function setup() {

			add_action( 'init', array( $this, 'save_leave_data' ) );
			add_action( 'rthrm_after_calendar', array( $this, 'render_calendar' ) );
		}

	    /**
	     *  Save leave data
	     */
        function save_leave_data() {
			global $rt_hrm_module, $rt_hrm_leave;

	        if( ! isset( $_REQUEST['rthrm_save_leave_nonce'] ) || ! wp_verify_nonce( $_REQUEST['rthrm_save_leave_nonce'], 'rthrm_save_leave' ) )
		        return;
				$leave_meta = $_REQUEST['post'];
				$author =  $leave_meta['leave-user-id'];

	        /**
	         * Check user is not on leave
	         */
	        $leave_start_date = date_create_from_format( 'd/m/Y', $leave_meta['leave-start-date'] );
            $leave_ids = $rt_hrm_leave->rthrm_check_user_on_leave( $leave_meta['leave-user-id'], $leave_start_date->format('Y-m-d') );
            if( empty( $leave_ids ) && isset( $leave_meta['leave-end-date'] ) ) {
	            $leave_end_date = date_create_from_format( 'd/m/Y', $leave_meta['leave-end-date'] );
	            $leave_ids = $rt_hrm_leave->rthrm_check_user_on_leave( $leave_meta['leave-user-id'], $leave_end_date->format('Y-m-d') );
            }

            if( ! empty( $leave_ids ) ) {

	            $message = 'Leave for the day(Period) has been already applied';

	            if( is_admin() ) {
		            echo '<div id="message" class="error notice is-dismissible"><p>' .  $message  . '</p></div>';
	            } else {
					bp_core_add_message( $message, 'error' );
	            }
	            return;
            }

			$newLeave = array(
				'comment_status' =>  'closed',
				'post_author' => $author,
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

			if ( isset( $_POST['leave_quota_use'] ) ) {
				update_post_meta( $newLeaveID, '_rt_hrm_leave_quota_use', $_POST['leave_quota_use'] );
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

            do_action( 'save_leave', $newLeaveID );

	        $message = __( 'Leave has been scheduled', RT_HRM_TEXT_DOMAIN );
	        if( !is_admin() ) {
		        bp_core_add_message( $message );
	        } else {
		        echo '<div id="message" class="updated notice is-dismissible"><p>' .  $message  . '</p></div>';
	        }

		}


        /**
         * render calendar template
         * @param $post_type
         */
        function ui( $post_type ) {

			$arg = array(
				'post_type' => $post_type,
				'is_hrm_manager' => current_user_can( rt_biz_get_access_role_cap( RT_HRM_TEXT_DOMAIN, 'editor' ) ),
			);
			rthrm_get_template( 'admin/calendar.php', $arg );
		}

        /**
         * Render calendar event
         * call on rthrm_after_calendar hook in template -> admin -> calendar.php
         */
        function render_calendar() {
			global $rt_calendar, $rt_hrm_module, $rt_hrm_leave;
            $args = array(
                'no_found_rows' => true,
                'post_type' => $rt_hrm_module->post_type,
                'post_status' => array( 'pending', 'approved', 'rejected' ),
                'nopaging' => true,
            );

			$meta_query = $rt_hrm_leave->rthrm_get_leave_for_author();

	        if( ! empty( $meta_query ) )
		        $args['meta_query'] = $meta_query;

            $the_query = new WP_Query( $args );
            $event= array();
            while ( $the_query->have_posts() ) {
	            $the_query->the_post();

	            // Leave Status
	            $color = '';
	            if ( get_post_status() == 'approved' ) {
		            $color = Rt_HRM_Settings::$settings['approved_leaves_color'];
	            } elseif ( get_post_status() == 'rejected' ) {
		            $color = Rt_HRM_Settings::$settings['rejected_leaves_color'];
	            } elseif ( get_post_status() == 'pending' ) {
		            $color = Rt_HRM_Settings::$settings['pending_leaves_color'];
	            }

	            $leaveStartDate = get_post_meta( get_the_id(), 'leave-start-date', true );
	            if ( isset ( $leaveStartDate ) && ! empty( $leaveStartDate ) ) {
		            $leaveStartDate = DateTime::createFromFormat( 'd/m/Y', $leaveStartDate );
		            $leaveStartDate = $leaveStartDate->format( 'Y-m-d' );
	            }

	            $leaveEndDate = get_post_meta( get_the_id(), 'leave-end-date', true );

	            if ( isset( $leaveEndDate ) && ! empty ( $leaveEndDate ) ) {
		            $leaveEndDate = DateTime::createFromFormat( 'd/m/Y', $leaveEndDate );

		            //Add P1D( period of 1 day ) date to make it inclusive. date in fullcalender.js@v2 is exclusive
		            $leaveEndDate->add(new DateInterval('P1D'));

		            $leaveEndDate = $leaveEndDate->format( 'Y-m-d' );
	            } else {
		            $leaveEndDate = $leaveStartDate;
	            }

	            //Leave
	            $leave_data = array(
		            'title'     => get_the_title(),
		            'start'     => $leaveStartDate,
		            'end'       => $leaveEndDate,
		            'color'     => $color,
		            'textColor' => Rt_HRM_Settings::$settings['leaves_text_color'],
		            'leave_id'  => get_the_id(),
	            );
	            $event[]    = $leave_data;
            }
            wp_reset_postdata();

			$rt_calendar->setDomElement("#calendar-container");
			$rt_calendar->setPopupElement(".leave-insert-dialog");
			$rt_calendar->setEvent($event);
			$is_editor = current_user_can( rt_biz_get_access_role_cap( RT_HRM_TEXT_DOMAIN, 'editor' ) );
			$rt_calendar->render_calendar( $is_editor );
		}

	}
}