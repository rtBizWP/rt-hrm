<?php 
global $rt_calendar, $rt_hrm_module, $rt_hrm_bp_hrm_calendar, $rt_hrm_bp_hrm_module, $post;
$rt_leave_id = $_GET['rt_leave_id'];
$is_user_change_allowed = 1;

?>
<div class="row list-heading">
    <div class="large-10 columns list-title">
        <h4><?php _e( 'Leave', RT_HRM_TEXT_DOMAIN ) ?></h4>
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