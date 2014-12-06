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



