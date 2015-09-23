<?php

/*
 * rtHRM Studio Calendar Template
 *
 * @author Dipesh
 */
?>
<div class="wrap">

	<?php 

	$employee_id = get_current_user_id();
	$employee_name = rtbiz_get_user_displayname( $employee_id );


	?>

	<?php if( ! is_admin() ): ?>
	<div class="list-heading">
        <div class="large-10 columns list-title">
            <h4><?php $menu_label = Rt_HRM_Settings::$settings['menu_label']; echo $menu_label . __( ' Calendar' ); ?></h4>
        </div>
        <div class="large-2 columns">
            <?php if( isset( $_GET['rt_voxxi_blog_id']) ){ ?>
                <a title="Close" class="right close-sidepanel"><i class="fa fa-caret-square-o-right fa-2x"></i></a>
            <?php } ?>
        </div>
    </div>
	<?php else: ?>
		<h2><?php  _e( ' Calendar', RT_HRM_TEXT_DOMAIN ) ?></h2>
	<?php endif; ?>

		<div id="calendar-widgets">
			<div id="calendar-container"></div>
		</div>

		<?php wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false ); ?>
		<?php do_action( 'rthrm_after_calendar' ); ?>

</div><!-- .wrap -->
<form id="form-add-leave" method="POST" class="leave-insert-dialog" action="">
	<?php wp_nonce_field( 'rthrm_save_leave', 'rthrm_save_leave_nonce' ); ?>
	<div class="title">
		<h2 class="title-form-add-leave <?php if ( is_admin() ){ echo "left"; }?>"><?php echo __( 'Schedule a Leave', 'edit-flow' ); ?>
            <a class="button right" id="close_popup"><?php _e( 'X' ) ?></a></h2>
	</div>
    <?php if( isset( $_GET['rt_voxxi_blog_id']) ){
        $post_id = $_GET['id'];
        global $rt_hrm_module;
        ?>
        <input type="hidden" name="post[rt_voxxi_blog_id]" value="<?php echo $_GET['rt_voxxi_blog_id'] ?>" />
        <input type="hidden" name="post[template]" value="<?php echo $_GET['template'] ?>" />
        <input type="hidden" name="post[actvity_element_id]" value="<?php echo $_GET['actvity_element_id'] ?>" />
        <input type="hidden" name="post[post_id]" value="<?php echo $post_id ?>" />
    <?php } ?>
	<div class="body">
		<table class="form-table rthrm-container">
			<tbody>
				<tr  <?php if ( ! $is_hrm_manager ) { ?>  class="hide" <?php } ?> >
					<td class="tblkey">
						<label class="">Employee Name</label>
					</td>
					<td class="tblval">
                        <input type="text" id="leave-user" name="post[leave-user]" placeholder="<?php echo esc_attr( _x( 'Employee Name', 'User Name') ); ?>" autocomplete="off" class="rt-form-text user-autocomplete" value="<?php echo ( $employee_name ) ? $employee_name : ''; ?>">
                        <input type="hidden" id="leave-user-id" name="post[leave-user-id]" placeholder="<?php echo esc_attr( _x( 'Employee Name', 'User Name') ); ?>" class="rt-form-text" value="<?php  echo isset( $employee_id) ? $employee_id : '';  ?>">
					</td>
				</tr>
				<tr>
					<?php global $rt_hrm_attributes; ?>
					<td>
						<label for="<?php echo $rt_hrm_attributes->leave_type_tax_label; ?>">
							<?php echo $rt_hrm_attributes->leave_type_tax_label; ?>
						</label>
					</td>
					<td>
					<?php
						$options = array();
						$terms = get_terms( Rt_HRM_Attributes::$leave_type_tax, array( 'hide_empty' => false, 'order' => 'asc' ) );
						$post_term = wp_get_post_terms( ( isset( $post->ID ) ) ? $post->ID : '', Rt_HRM_Attributes::$leave_type_tax, array( 'fields' => 'ids' ) );
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
					</td>
				</tr>
				<tr>
					<td>
						<label for="leave-duration">Duration</label>
					</td>
					<td>
						<select id="leave-duration" name="post[leave-duration]" class="rt-form-select">
							<option value="full-day">Full Day</option>
							<option value="half-day">Half Day</option>
							<option value="other">Other</option>
						</select>
					</td>
				</tr>
				<tr>
					<td>
						<label for="leave-start-date">Start Date</label>
					</td>
					<td>
						<?php if ( is_admin() ) {?>
							<label id="lblleave-start-date"></label>
						<?php }?>
						<div >
							<?php if ( is_admin() ) {?>
								<input id="leave-start-date" name="post[leave-start-date]"  class="rt-form-text" placeholder="Select Start Date" readonly="readonly" value="" type="hidden">
							<?php } else {?>
								<input id="leave-start-date" name="post[leave-start-date]"  class="rt-form-text datepicker" placeholder="Select Start Date" readonly="readonly" value="" type="text">
							<?php }?>
						</div>
					</td>
				</tr>
				<tr style="display: none;">
					<td>
						<label for="leave-end-date">End Date</label>
					</td>
					<td>
						<div>
							<input id="leave-end-date" name="post[leave-end-date]" class="rt-form-text datepicker" placeholder="Select End Date" readonly="readonly" value="" type="text">
						</div>
					</td>
				</tr>
				<tr>
					<td class="tblkey">
						<label class="">Description </label>
					</td>
					<td class="tblval">
						<textarea id="leave_description"  class="rt-form-text" name="post[leave_description]"></textarea>
					</td>
				</tr>
				<?php
				global $rt_hrm_module;
				$display_checkbox = false;

				if ( ! $is_hrm_manager ) {
					$leave_quota = $rt_hrm_module->get_user_leave_quota( get_current_user_id() );
					if ( intval( $leave_quota ) > 0 ) {
						$display_checkbox = true;
					}
				} ?>
				<tr <?php echo ( ! $display_checkbox ) ? 'class="hide"' : ''; ?>>
					<td></td>
					<td>
						<label><input type="checkbox" id="leave_quota_use" name="leave_quota_use" value="1" /> <?php _e( 'Use Paid Leaves that are left ?' ); ?></label>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="controls">
		<input type="submit" class="button left" name="form-add-leave" value="<?php echo esc_html( sprintf( _x( 'Create %s', 'post type name', 'edit-flow' ), rthrm_leave_label() ) ); ?>">
	</div>
	<div class="spinner">&nbsp;</div>
</form>
