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
    
    echo 'calender Screen';
}

function bp_hrm_leave() {
    
    echo 'leave Screen';
}

function bp_hrm_requests() {
    
    echo 'requests Screen';
}

?>