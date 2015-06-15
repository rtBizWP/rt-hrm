<?php
/**
 * Created by PhpStorm.
 * User: paresh
 * Date: 6/12/14
 * Time: 5:57 PM
 */
function leave_status_update() {

global $rt_hrm_module;

    $allowed_component = array( BP_ACTIVITY_SLUG );


    if( !in_array( bp_current_component(), $allowed_component ) )
        return;

    if ( ! isset( $_POST['rt_hrm_wall_leave_status_update'] ) || ! wp_verify_nonce( $_POST['rt_hrm_wall_leave_status_update'], 'rt_hrm_wall_leave_status_update' ) )
        return;


    if ( !isset( $_POST['post'] ) )
        return;


    $leave = $_POST['post'];



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

    if( isset( $leave['rt_voxxi_blog_id'] ) ){
        restore_current_blog();
    }

    bp_core_add_message('Leave updated successfully');

}
add_action( 'bp_actions', 'leave_status_update' );

function calender_leave_add(){

    global $rt_hrm_module, $rt_hrm_attributes;

    if ( isset( $_REQUEST['form-add-leave'] ) && !empty( $_REQUEST['form-add-leave'] ) && ! is_admin() ) {

        $leave_meta = $_REQUEST['post'];
		$form_type = $_REQUEST['form-add-leave'];

        if( isset( $leave_meta['rt_voxxi_blog_id'] ) )
            switch_to_blog( $leave_meta['rt_voxxi_blog_id'] );


        $author = rt_biz_get_wp_user_for_person( $leave_meta['leave-user-id'] );

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
		
		if( $form_type == "Update Leave" ){
			$newLeaveID = $posts[0]->ID;
		} else {
		
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
			
		}

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

        if( isset( $leave_meta['rt_voxxi_blog_id'] ) ){
            restore_current_blog();
            add_action ( 'wp_head', 'rt_voxxi_js_variables' );
        }
        do_action( 'save_leave', $newLeaveID );

        bp_core_add_message('Leave scheduled successfully');
    }


}
add_action( 'bp_actions', 'calender_leave_add' );

function rt_bp_hrm_ask_for_more(){
    $allowed_component = array( BP_ACTIVITY_SLUG );


    if( !in_array( bp_current_component(), $allowed_component ) )
        return;

    if ( ! isset( $_POST['rt_hrm_wall_leave_ask_more'] ) || ! wp_verify_nonce( $_POST['rt_hrm_wall_leave_ask_more'], 'rt_hrm_wall_leave_ask_more' ) )
        return;


    if ( !isset( $_POST['post'] ) )
        return;

    $leave = $_POST['post'];

    $from_user_name = bp_core_get_user_displayname( get_current_user_id() );


    $subject  = bp_get_email_subject( array( 'text' => sprintf( __( '%1$s: "%2$s"', 'buddypress' ), $leave['subject'],   $from_user_name ) ) );

    $message = sprintf( __(
        '

        %1$s

        ---------------------
        ', RT_HRM_TEXT_DOMAIN ), $leave['mail_content'] );


    /* Send the message */
    $to      = apply_filters( 'rt_hrm_leave_to', $leave['to'] );
    $subject = apply_filters_ref_array( 'rt_hrm_leave_subject', array( $subject, &$from_user_name ) );
    $message = apply_filters_ref_array( 'rt_hrm_leave_message', array(  $leave['mail_content']) );

    wp_mail( $to, $subject, $message );

    bp_core_add_message('Your message was sent successfully.');


}
add_action('bp_actions', 'rt_bp_hrm_ask_for_more' );



