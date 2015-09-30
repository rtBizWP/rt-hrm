<?php
/**
 * Don't load this file directly!
 */
if ( ! defined( 'ABSPATH' ) )
	exit;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Rt_HRM_Bp_Hrm_Module
 *
 * @author kishore
 */
if( !class_exists( 'Rt_HRM_Bp_Hrm_Module' ) ) {
	/**
	 * Class Rt_HRM_Bp_Hrm_Module
	 */
	class Rt_HRM_Bp_Hrm_Module extends  Rt_HRM_Module {

        /**
         * slug for leave CPT
         * @var string
         */
        var $post_type = 'rt_leave';

        /**
         * menu position for HRM
         * @var string
         */
        var $menu_position = 33;

        /**
         * Module Name
         * @var string
         */
        var $name = 'HRM';

        /**
         * Array of labels for leave CPT
         * @var array
         */
        var $labels = array();

        /**
         * Array of statuses for leave CPT
         * @var array
         */
        var $statuses = array();

        /**
         * Array of statuses for leave CPT
         * @var array
         */
        var $custom_menu_order = array();

		static $user_leave_quota_key = 'rt_hrm_leaves_quota';

		/**
         * Save save_leave
         * @param $post_id
         * @param $post
         * @return mixed
         */
        function save_leave( $post_id, $post ){
			global $rt_hrm_module,$rt_hrm_attributes;
			if ( empty( $_POST['content'] ) )
				$_POST['content'] = '';
			if ( ! empty( $_POST['post']['post_status'] ) ){
				$save_leave_post = array(
			      'ID'           => $post_id,
			      'post_content' => $_POST['content'],
			      //'post_title' => $_POST['post_title'],
			      'post_status' => $_POST['post']['post_status']
			  	);
			} else {
				$save_leave_post = array(
			      'ID'           => $post_id,
			      'post_content' => $_POST['content'],
			      //'post_title' => $_POST['post_title']
			  	);
			}
		  	
			
			// Update the post into the database
			if ( ! empty( $_POST['content'] ) || ! empty( $_POST['post'] ) )
				wp_update_post( $save_leave_post );
		}

        /**
         * Save meta-box values for leave CPT
         * @param $post_id
         * @param $post
         * @return mixed
         */
        function save_leave_meta( $post_id, $post ){
            global $rt_hrm_module,$rt_hrm_attributes;


            if( $post->post_type != $rt_hrm_module->post_type)
                return;

			if ( $post->post_type == 'revision' ) return;
			if ( ! isset( $_POST['post'] ) ) return;

			$leave_meta = $_POST['post'];			

			if ( isset( $leave_meta[  Rt_HRM_Attributes::$leave_type_tax] ) ) {
				wp_set_post_terms( $post_id, implode( ',', array_map( 'intval', $leave_meta[Rt_HRM_Attributes::$leave_type_tax] ) ), Rt_HRM_Attributes::$leave_type_tax );
			}

			if ( isset( $_POST['leave_quota_use'] ) ) {
				update_post_meta( $post_id, '_rt_hrm_leave_quota_use', $_POST['leave_quota_use'] );
			}

			if ( isset( $leave_meta['leave-user'] ) ) {
				update_post_meta( $post_id, 'leave-user', $leave_meta['leave-user'] );
			}
            if ( isset( $leave_meta['leave-user-id'] ) ) {
            	update_post_meta( $post_id, 'leave-user-id', $leave_meta['leave-user-id'] );
			}
			if ( isset( $leave_meta['leave-duration'] ) ) {
            	update_post_meta( $post_id, 'leave-duration', $leave_meta['leave-duration'] );
			}
			if ( isset( $leave_meta['leave-start-date'] ) ) {
				update_post_meta( $post_id, 'leave-start-date', $leave_meta['leave-start-date'] );
			}

			if ( $leave_meta['leave-duration'] == 'other' && isset( $leave_meta['leave-end-date'] ) ){
				update_post_meta( $post_id, 'leave-end-date', $leave_meta['leave-end-date'] );
			} else {
				delete_post_meta( $post_id, 'leave-end-date' );
			}

            do_action( 'save_leave', $post_id );
                       
		}

		/**
		 * Additional leave details metabox UI
		 * @param $post
		 */
		function ui_metabox( $rt_leave_id ){
			global $current_user, $rt_hrm_attributes;
			wp_nonce_field( 'rthrm_leave_additional_details_meta', 'rthrm_leave_additional_details_meta_nonce' );

			$post = get_post( $rt_leave_id );
			// print_r($post);

			if (  !current_user_can( 'hrm_edit_leaves' ) && get_current_user_id() != intval( $post->post_author )) {

				return;
			}

			$leave_user = get_post_meta( $rt_leave_id, 'leave-user', false);
			$leave_user_id = get_post_meta( $rt_leave_id, 'leave-user-id', false);
			$leave_duration = get_post_meta( $rt_leave_id, 'leave-duration', false);
			$leave_start_date = get_post_meta( $rt_leave_id, 'leave-start-date', false);
			$leave_end_date = get_post_meta( $rt_leave_id, 'leave-end-date', false);
			$leave_quota_use = get_post_meta( $rt_leave_id, '_rt_hrm_leave_quota_use', true );

			?>
			<form action="<?php //echo esc_url( add_query_arg( array( 'rt_leave_id'=> $rt_leave_id, 'action'=>'update' ) )); ?>" class="" method="POST" id="form-add-leave" style="display: block;">
				<!--<div id="titlewrap">
				<label for="title" id="title-prompt-text" class="screen-reader-text">Enter title here</label>
				<input type="text" autocomplete="off" id="title" value="" size="30" name="post_title" readonly="readonly">
				&nbsp;&nbsp;
	                <?php
				if (isset($post->ID))
					$pstatus = $post->post_status;
				else
					$pstatus = "";
				$post_status = $this->get_custom_statuses();
				$custom_status_flag = true;
				$user_edit = current_user_can( 'hrm_edit_leaves' );
				?>
	                <?php if( $user_edit ) { ?>
	                    <select id="rtpm_post_status" class="right" name="post[post_status]">
	                        <?php foreach ($post_status as $status) {
					if ($status['slug'] == $pstatus) {
						$selected = 'selected="selected"';
						$custom_status_flag = false;
					} else {
						$selected = '';
					}
					printf('<option value="%s" %s >%s</option>', $status['slug'], $selected, $status['name']);
				} ?>
	                        <?php if ( $custom_status_flag && isset( $post->ID ) ) { echo '<option selected="selected" value="'.$pstatus.'">'.$pstatus.'</option>'; } ?>
	                    </select>
	                <?php } else {
					foreach ( $post_status as $status ) {
						if($status['slug'] == $pstatus) {
							echo '<span class="rtpm_view_mode">'.$status['name'].'</span>';
							break;
						}
					}
				} ?>
			</div>
			<br /><br /> -->
				<input type="hidden" autocomplete="off" id="title" value="rt_leave" size="30" name="post_type" readonly="readonly">
				<div class="row <?php if ( ! current_user_can( 'hrm_edit_leaves' ) ) { ?>  hide<?php } ?>">
					<div class="large-6 columns">
						<label>Employee Name <small class="required"> *</small></label>
						<input type="text" id="leave-user" size="" name="post[leave-user]" required="required" placeholder="<?php echo esc_attr( _x( 'Employee Name', 'User Name') ); ?>" autocomplete="off" class="rt-form-text user-autocomplete" value="<?php if ( isset( $leave_user ) && !empty( $leave_user ) ) { echo $leave_user[0]; } elseif ( ! current_user_can( 'hrm_edit_leaves' ) ) { echo $current_employee->post_title; }  ?>">
						<input type="hidden" id="leave-user-id" name="post[leave-user-id]" placeholder="<?php echo esc_attr( _x( 'Employee Name', 'User Name') ); ?>" class="rt-form-text" value="<?php if ( isset( $leave_user_id ) && !empty( $leave_user_id ) ) { echo $leave_user_id[0]; } elseif ( ! current_user_can( 'hrm_edit_leaves' ) ) { echo $current_employee->ID; }  ?>">

					</div>
					<div class="large-6 columns">
						<label>Status</label>
						<?php
						if (isset($post->ID))
							$pstatus = $post->post_status;
						else
							$pstatus = "";
						$post_status = $this->get_custom_statuses();
						$custom_status_flag = true;
						$user_edit = current_user_can( 'hrm_edit_leaves' );
						?>
						<?php if( $user_edit ) { ?>
	                    <select id="rtpm_post_status  class="right" name="post[post_status]">
	                        <?php foreach ($post_status as $status) {
							if ($status['slug'] == $pstatus) {
								$selected = 'selected="selected"';
								$custom_status_flag = false;
							} else {
								$selected = '';
							}
							printf('<option value="%s" %s >%s</option>', $status['slug'], $selected, $status['name']);
						} ?>
							<?php if ( $custom_status_flag && isset( $post->ID ) ) { echo '<option selected="selected" value="'.$pstatus.'">'.$pstatus.'</option>'; } ?>
	                    </select>
	                <?php } else {
							foreach ( $post_status as $status ) {
								if($status['slug'] == $pstatus) {
									echo '<span class="rtpm_view_mode">'.$status['name'].'</span>';
									break;
								}
							}
						} ?>
					</div>
				</div>
				<div class="row">
					<div class="large-6 columns">
						<label for="<?php echo $rt_hrm_attributes->leave_type_tax_label; ?>">
							<?php echo $rt_hrm_attributes->leave_type_tax_label; ?>
						</label>
						<?php
						$options = array();
						$terms = get_terms( Rt_HRM_Attributes::$leave_type_tax, array( 'hide_empty' => false, 'order' => 'asc' ) );
						$post_term = wp_get_post_terms( ( isset( $rt_leave_id ) ) ? $rt_leave_id : '', Rt_HRM_Attributes::$leave_type_tax, array( 'fields' => 'ids' ) );
						// Default Selected Term for the attribute. can beset via settings -- later on
						$selected_term = '-11111';
						if( !empty( $post_term ) ) {
							$selected_term = $post_term[0];
						}
						foreach ($terms as $term) {
							$options[] = array(
								$term->name => $term->term_id,
								'title' => $term->name,
								'checked' => ($term->term_id == $selected_term) ? true : false,
							);
						}
						global $rt_form;
						$args = array(
							'id' => Rt_HRM_Attributes::$leave_type_tax,
							'name' => 'post['.Rt_HRM_Attributes::$leave_type_tax.'][]',
							'rtForm_options' => $options,
						);
						echo $rt_form->get_radio( $args );
						?>
					</div>
					<div class="large-6 columns">
						<label for="leave-duration">Duration</label>
						<select id="leave-duration" name="post[leave-duration]" class="rt-form-select">
							<option value="full-day" <?php if ( isset( $leave_duration ) && !empty( $leave_duration ) &&  $leave_duration[0] == 'full-day' ) { echo 'selected'; } ?> >Full Day</option>
							<option value="half-day" <?php if ( isset( $leave_duration ) && !empty( $leave_duration ) &&  $leave_duration[0] == 'half-day' ) { echo 'selected'; } ?>>Half Day</option>
							<option value="other" <?php if ( isset( $leave_duration ) && !empty( $leave_duration ) &&  $leave_duration[0] == 'other' ) { echo 'selected'; } ?>>Other</option>
						</select>
					</div>

				</div>
				<div class="row">
					<div class="large-6 columns">
						<label for="leave-start-date">Start Date <small class="required"> *</small></label>
						<input id="leave-start-date" name="post[leave-start-date]"  class="rt-form-text datepicker" placeholder="Select Start Date" readonly="readonly" value="<?php if ( isset( $leave_start_date ) && !empty( $leave_start_date ) ) { echo $leave_start_date[0]; }  ?>" type="text">
					</div>
					<div class="large-6 columns">
	            	  <span>
					  <span>
					  <label for="leave-end-date">End Date</label>
	            	  <input id="leave-end-date" name="post[leave-end-date]" class="rt-form-text datepicker" placeholder="Select End Date" readonly="readonly" value="<?php if ( isset( $leave_end_date ) && !empty( $leave_end_date ) ) { echo $leave_end_date[0]; }  ?>" type="text">
					  </span>
					  </span>

					</div>
				</div>
				<div class="row">
					<div class="large-8 columns">
						<label class="">Description </label>
						<textarea id="content" class="rt-form-text" name="content" aria-hidden="true"><?php echo $post->post_content ?></textarea>
					</div>
				</div>
				<?php
				$display_checkbox = false;
				if ( current_user_can( 'hrm_edit_leaves' ) ) {
					$display_checkbox = true;
				} else {
					$leave_quota = $this->get_user_remaining_leaves( get_current_user_id() );
					if ( intval( $leave_quota ) > 0 ) {
						$display_checkbox = true;
					}
				}
				?>
				<div class="row" <?php echo ( ! $display_checkbox ) ? 'class="hide"' : ''; ?>>
					<div class="large-6 columns">
						<label><input type="checkbox" id="leave_quota_use" name="leave_quota_use" value="1" <?php checked( '1', $leave_quota_use ); ?> /> <?php _e( 'Use Paid Leaves that are left ?' ); ?></label>
					</div>
					<div class="large-6 columns">
						<label id="remaining-leave-quota">Remaining leave: &nbsp;&nbsp;<?php if ( isset( $leave_user_id ) && !empty( $leave_user_id ) ) { echo $this->get_user_remaining_leaves( $leave_user_id[0] ) ; } elseif ( ! current_user_can( 'hrm_edit_leaves' ) ) { echo $this->get_user_remaining_leaves( $current_employee->ID ); }  ?></label>
					</div>

				</div>
				<?php if( isset( $_REQUEST['action'] ) && $_REQUEST['action'] != 'view' ) {?>
					<div class="row">
						<div class="large-10 columns">
							&nbsp;&nbsp;
						</div>
						<div class="large-2 columns controls">
							<input type="submit" value="Update Leave" name="form-add-leave" class="button">
						</div>
						<div class="spinner">&nbsp;</div>

					</div>
				<?php } ?>
			</form>

		<?php
		}


	}
}
