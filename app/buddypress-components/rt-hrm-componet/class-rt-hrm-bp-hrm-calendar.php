<?php
/**
 * Don't load this file directly!
 */
if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * Description of Rt_Hrm_Bp_Hrm_Calendar
 *
 * @author kishore
 */
if ( ! class_exists( 'Rt_Hrm_Bp_Hrm_Calendar' ) ) {
    
    class Rt_Hrm_Bp_Hrm_Calendar extends Rt_HRM_Calendar {
        
		var $post_type = 'rt_leave';
		
        function __construct() {
            parent::__construct();
			
        }
		
		/**
         * Actions to be taken prior to page loading. This is after headers have been set.
         * Handle calendar page event
         * call on load-$hook
         */
        function page_actions() {
			global $rt_hrm_module, $rt_hrm_attributes;
			
			if ( isset( $_REQUEST['form-add-leave'] ) && !empty( $_REQUEST['form-add-leave'] ) && ! is_admin() ) {

				$leave_meta = $_REQUEST['post'];
				$author = $leave_meta['leave-user-id'];

				$args = array(
					'meta_query' => array(
						array(
							'key' => 'leave-user-id',
							'value' => $leave_meta['leave-user-id']
						),
						array(
							'key' => 'leave-start-date',
							'value' => $leave_meta['leave-start-date']
						)
					),
					'post_type' => $rt_hrm_module->post_type,
					'post_status' => 'any',
					'nopaging' => true
				);

				$posts = get_posts($args);

				if ( count($posts) > 0 ) {
					// echo '<div class="error"><p>'.__( 'You can not apply for leave twice on the same day.' ).'</p></div>';
					// todo: write in php instead of js
				?>
					<script> alert('You can not apply for leave twice on the same day.') </script>
				<?php return false;
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
			}
		}
		
		/**
         *  Add hooks for calendar page like page action or render calendar
         */
        function setup_calendar() {
			/* Add callbacks for this screen only */
			add_action( 'rthrm_after_calendar', array( $this, 'page_actions' ), 9 );
			add_action( 'rthrm_after_calendar', array( $this, 'footer_scripts' ) );

			/* render data into calendar */
			add_action( 'rthrm_after_calendar', array( $this, 'render_calendar' ) );
		}
		
    
    }
    
}