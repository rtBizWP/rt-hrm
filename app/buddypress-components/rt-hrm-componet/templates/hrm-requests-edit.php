<?php 
global $rt_calendar, $rt_hrm_module, $rt_hrm_bp_hrm_calendar, $rt_hrm_bp_hrm_module,$rt_hrm_bp_hrm, $post;
$rt_leave_id = $_GET['rt_leave_id'];
$is_user_change_allowed = 1;

//Trash action
if( isset( $_GET['action'] ) && $_GET['action'] == 'deletepost' && isset( $_GET['rt_leave_id'] ) ) {
    wp_delete_post( $_GET['rt_leave_id'] );
	echo '<script> window.location="' . $rt_hrm_bp_hrm->get_component_root_url() . '/requests"; </script> ';
    die();
}

?>
<div class="row list-heading">
    <div class="large-10 columns list-title">
        <h4><?php _e( 'Leave Approval', 'rt_hrm' ) ?></h4>
    </div>
    <div class="large-2 columns">
       
    </div>
</div>
<?php
/* render data into calendar */

$rt_hrm_bp_hrm_module->save_leave_meta( $rt_leave_id, $post );
$rt_hrm_bp_hrm_module->save_leave( $rt_leave_id, $post );
$rt_hrm_bp_hrm_module->ui_metabox( $rt_leave_id );
if ( isset($_POST['update']) ) {
	//echo '<script> window.location=""; </script> ';
	//die();
}

?>