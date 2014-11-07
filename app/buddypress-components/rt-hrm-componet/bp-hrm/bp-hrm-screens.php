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
	add_action('bp_template_content','load_calender_template');
}

function load_calender_template() {
	$cap = rt_biz_get_access_role_cap( RT_HRM_TEXT_DOMAIN, 'editor' );
     if ( ! current_user_can( $cap ) ) {
      echo 'You do not have sufficient permissions to access this page';
            return false;
    }
    include  RT_HRM_BP_HRM_PATH.'/templates/hrm-calender.php';
}

function bp_hrm_leave() {
    add_action('bp_template_content','load_leave_template');
}

function load_leave_template() {
	$cap = rt_biz_get_access_role_cap( RT_HRM_TEXT_DOMAIN, 'editor' );
     if ( ! current_user_can( $cap ) ) {
      echo 'You do not have sufficient permissions to access this page';
            return false;
    }
	 
	if ( isset( $_GET['action'] ) && ($_GET['action'] == 'view' || $_GET['action'] == 'edit' || $_GET['action'] == 'update') ) {
          include  RT_HRM_BP_HRM_PATH.'/templates/hrm-leave-edit.php';
    } else if (isset( $_GET['action'] ) && ($_GET['action'] == 'addnew') ) {
    	include  RT_HRM_BP_HRM_PATH.'/templates/hrm-leave-new.php';
    }
    else {
          include  RT_HRM_BP_HRM_PATH.'/templates/hrm-leave.php';
    }
}

function bp_hrm_requests() {
	if ( current_user_can( rt_biz_get_access_role_cap( RT_HRM_TEXT_DOMAIN, 'editor' ) ) ) {
		add_action('bp_template_content','load_requests_template');
	}
}

function load_requests_template() {
	$cap = rt_biz_get_access_role_cap( RT_HRM_TEXT_DOMAIN, 'editor' );
     if ( ! current_user_can( $cap ) ) {
      echo 'You do not have sufficient permissions to access this page';
            return false;
    }
	 
	if ( isset( $_GET['action'] ) && ( $_GET['action'] == 'view' || $_GET['action'] == 'edit' || $_GET['action'] == 'update' || $_GET['action'] == 'deletepost' ) ) {
          include  RT_HRM_BP_HRM_PATH.'/templates/hrm-requests-edit.php';
    }else{
          include  RT_HRM_BP_HRM_PATH.'/templates/hrm-requests.php';
    }
}

?>