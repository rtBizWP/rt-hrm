<?php

/********************************************************************************
 * Screen Functions
 *
 * Screen functions are the controllers of BuddyPress. They will execute when their
 * specific URL is caught. They will first save or manipulate data using business
 * functions, then pass on the user to a template file.
 */
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * If your component uses a top-level directory, this function will catch the requests and load
 * the index page.
 *
 * @package BuddyPress_Template_Pack
 * @since 1.6
 */
function bp_hrm_screen() {

    bp_core_load_template( 'members/single/home'  );	
}
add_action( 'bp_screens', 'bp_hrm_screen' );

function bp_hrm_calender() { 
	add_filter('bp_located_template','load_calender_template');
}

function load_calender_template() {
    return  RT_HRM_BP_HRM_PATH.'/templates/hrm-calender.php';
}

function bp_hrm_leave() {
    add_filter('bp_located_template','load_leave_template');
}

function load_leave_template() {
    return  RT_HRM_BP_HRM_PATH.'/templates/hrm-leave.php';
}

function bp_hrm_requests() {
	add_filter('bp_located_template','load_requests_template');
}

function load_requests_template() {
    return  RT_HRM_BP_HRM_PATH.'/templates/hrm-requests.php';
}

?>