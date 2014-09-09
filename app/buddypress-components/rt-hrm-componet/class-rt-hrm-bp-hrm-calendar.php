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