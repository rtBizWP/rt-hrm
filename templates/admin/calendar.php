<?php

/*
 * rtHRM Studio Calendar Template
 *
 * @author Dipesh
 */
?>
<div class="wrap">

	<?php screen_icon(); ?>

	<h2><?php _e( 'HRM Calendar' ); ?></h2>

	<div id="poststuff">

		<div id="calendar-widgets" class="metabox-holder">


			<div id="calendar-container" class="postbox-container">

			</div>

		</div> <!-- #post-body -->
		<?php wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false ); ?>
		<?php do_action( 'rthrm_after_calendar' ); ?>

	</div> <!-- #poststuff -->

</div><!-- .wrap -->
<form id="form-add-leave" method="POST" class="leave-insert-dialog" action="">
	<?php /* translators: %1$s = post type name, %2$s = date */ ?>
	<div class="title">
		<h2 class="title-form-add-leave left"><?php echo __( 'Schedule a Leave', 'edit-flow' ); ?>
            <a class="button right" id="close_popup"><?php _e( 'X' ) ?></a></h2>
	</div>
	<div class="body">
		<table class="form-table rthrm-container">
			<tbody>
				<tr <tr  <?php if ( ! $is_hrm_manager ) { ?>  class="hide" <?php } ?> >
					<td class="tblkey">
						<label class="label">Employee Name</label>
					</td>
					<td class="tblval">
                        <input type="text" id="leave-user" name="post[leave-user]" placeholder="<?php echo esc_attr( _x( 'Employee Name', 'User Name') ); ?>" autocomplete="off" class="rt-form-text user-autocomplete" value="<?php  if ( ! current_user_can( rt_biz_get_access_role_cap( RT_HRM_TEXT_DOMAIN, 'admin' ) ) ) { echo $current_employee->post_title; } ?>">
                        <input type="hidden" id="leave-user-id" name="post[leave-user-id]" placeholder="<?php echo esc_attr( _x( 'Employee Name', 'User Name') ); ?>" class="rt-form-text" value="<?php if ( ! current_user_can( rt_biz_get_access_role_cap( RT_HRM_TEXT_DOMAIN, 'admin' ) ) ) { echo $current_employee->ID; } ?>">
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
						<label id="lblleave-start-date"></label>
						<div >
							<input id="leave-start-date" name="post[leave-start-date]"  class="rt-form-text" placeholder="Select Start Date" readonly="readonly" value="" type="hidden">
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
						<label class="label">Description </label>
					</td>
					<td class="tblval">
						<textarea id="leave_description"  class="rt-form-text" name="post[leave_description]"></textarea>
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

