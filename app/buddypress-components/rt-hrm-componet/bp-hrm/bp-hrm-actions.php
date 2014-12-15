<?php
/**
 * Created by PhpStorm.
 * User: paresh
 * Date: 6/12/14
 * Time: 5:57 PM
 */

function pre_get_leave_list( $query ){
    global $rt_person, $wpdb, $bp;


    if( !bp_is_current_component( BP_HRM_SLUG ) )
        return;

     if( !bp_current_action('calender') )
         return;

    $editor_cap = rt_biz_get_access_role_cap( RT_HRM_TEXT_DOMAIN, 'editor' );

    if (  !current_user_can( $editor_cap ) ) {

        $contact_key =  Rt_Person::$meta_key_prefix . $rt_person->user_id_key;

        $post_meta = $wpdb->get_row( "SELECT * from {$wpdb->postmeta} WHERE meta_key = '{$contact_key}' and meta_value = {$bp->displayed_user->id} ");


        $query->set('meta_query',  array(
                'relation' => 'OR',
                array(
                    'key'     => 'leave-user-id',
                    'value'    => '',
                    'compare' => 'NOT EXISTS',
                ),
                array(
                    'key' => 'leave-user-id',
                    'value' => $post_meta->post_id,
                    'compare' => '==',
                ),
            )
        );
    }

    return $query;


}

function calender_pre_get_post_filter(){

    add_action( 'pre_get_posts', 'pre_get_leave_list', 10, 1 );
}

add_action( 'bp_actions', 'calender_pre_get_post_filter' );

function leave_status_update() {

global $rt_hrm_module;

    $allowed_component = array( BP_ACTIVITY_SLUG );


    if( !in_array( bp_current_component(), $allowed_component ) )
        return;


    if ( !isset( $_POST['post'] ) )
        return;


    $leave = $_POST['post'];

    if(  !isset( $leave['post_type'] ) )
        return;



    if( $leave['post_type'] != $rt_hrm_module->post_type )
        return;


    if( isset( $leave['rt_voxxi_blog_id'] ) )
        switch_to_blog( $leave['rt_voxxi_blog_id'] );


    $template = $leave['template'];

    switch( $template ){
        case 'approve_leave':
            $updated_status = 'approved';
            break;

        case 'denny_leave':
            $updated_status = 'rejected';
            break;


    }

    $arg = array(
        'ID' => $leave['post_id'],
        'post_status' => $updated_status,
    );


    wp_update_post( $arg );

    $arg = array(
        'comment_post_ID' => $leave['post_id'],
        'comment_content' => $leave['leave_comment_content'],
    );

    wp_insert_comment( $arg );

    bp_core_add_message('Leave updated successfully');

}
add_action( 'bp_actions', 'leave_status_update' );



