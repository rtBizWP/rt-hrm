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
		<h2><?php echo __( 'Schedule a Leave', 'edit-flow' ); ?></h2>
	</div>
	<div class="body">
		<table class="form-table">
			<?php if ( $is_hrm_manager ) { ?>
			<tr>
				<td class="tblkey">
					<label class="label">User</label>
				</td>
				<td class="tblval">
					<input type="text" id="txtleave-user" class="input" name="leave_user" placeholder="<?php echo esc_attr( _x( 'User Name', 'User Name') ); ?>">
				</td>
			</tr>
			<?php } ?>
			<tr>
				<td class="tblkey">
					<label class="label span3">Start Date</label>
				</td>
				<td class="tblval">
					<label class="label" id="lblleave-start-date"></label>
					<input type="hidden" id="txtleave-start-date" class="input" name="leave_start_date" placeholder="<?php echo esc_attr( _x( 'Starting date', 'Leave Start Date') ); ?>">
				</td>
			</tr>
			<tr>
				<td class="tblkey">
					<label class="label">Leave Type</label>
				</td>
				<td class="tblval">
					<select class="input" id="cmbleave-day-type" name="leave-day-type">
						<option value="full_day">Full-day</option>
						<option value="half_day">Half-day</option>
						<option value="other">Other</option>
					</select>
				</td>
			</tr>
			<tr id="tr-end-date" style="display: none" >
				<td class="tblkey">
					<label class="label span3">End Date</label>
				</td>
				<td class="tblval">
					<input type="text" id="txtleave-end-date" class="input datepicker" name="leave_end_date" placeholder="<?php echo esc_attr( _x( 'Ending date', 'Leave End Date') ); ?>">
				</td>
			</tr>
			<tr>
				<td class="tblkey">
					<label class="label">Leave Type</label>
				</td>
				<td class="tblval">
					<select class="input" id="cmbleave-type" name="leave-type">
						<option>Casual leave</option>
						<option>Sick leave</option>
					</select>
				</td>
			</tr>
			<tr>
				<td class="tblkey">
					<label class="label">Description </label>
				</td>
				<td class="tblval">
					<textarea class="input" name="leave_description"></textarea>
				</td>
			</tr>
		</table>
	</div>
	<div class="controls">
		<input type="submit" class="button left" name="form-add-leave" value="<?php echo esc_html( sprintf( _x( 'Create %s', 'post type name', 'edit-flow' ), 'rt_leave' ) ); ?>">
		<a class="button right" href="#"><?php echo esc_html( sprintf( _x( 'Edit %s', 'post type name', 'edit-flow' ), 'rt_leave' ) ); ?>&nbsp;&raquo;</a>
	</div>
	<div class="spinner">&nbsp;</div>
</form>

