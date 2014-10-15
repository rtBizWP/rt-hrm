<?php 
global $rt_calendar, $rt_hrm_module, $rt_hrm_bp_hrm_calendar, $rt_hrm_bp_hrm_module, $post;
$rt_leave_id = $_GET['rt_leave_id'];
$is_user_change_allowed = 1;

?>
<div class="row list-heading">
    <div class="large-10 columns list-title">
        <h4><?php _e( 'Leave', 'rt_hrm' ) ?></h4>
    </div>
    <div class="large-2 columns">
      <a href="<?php echo esc_url( add_query_arg( array( 'rt_leave_id'=> $get_the_id, 'action'=>'addnew' ), $rt_hrm_bp_hrm->get_component_root_url() ) ); ?>"><input class="pull-right" type="button"  data-reveal-id="add-new-leave-modal" value="Add New" /></a> 
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