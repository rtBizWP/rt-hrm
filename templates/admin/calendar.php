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
		<table class="form-table rthrm-container">
			<tbody>
			<?php if ( $is_hrm_manager ) { ?>
				<tr>
					<td class="tblkey">
						<label class="label">User</label>
					</td>
					<td class="tblval">
						<input type="text" id="leave-user" class="rt-form-text" name="post[leave_user]" placeholder="<?php echo esc_attr( _x( 'User Name', 'User Name') ); ?>">
					</td>
				</tr>
			<?php } ?>
			<?php
			global $rt_hrm_module,$rt_hrm_attributes;
			$attributes = rthrm_get_attributes( $rt_hrm_module->post_type );
			foreach ( $attributes as $attr ){
				?>
				<tr>
					<td>
						<label for="<?php echo $attr->attribute_name ?>">
							<?php echo $attr->attribute_label; ?>
						</label>
					</td>
					<td>
						<?php $rt_hrm_attributes->render_attribute( $attr,'', true ); ?>
					</td>
				</tr>
			<?php
			}
			?>
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
		<input type="submit" class="button left" name="form-add-leave" value="<?php echo esc_html( sprintf( _x( 'Create %s', 'post type name', 'edit-flow' ), 'rt_leave' ) ); ?>">
		<a class="button right" href="#"><?php echo esc_html( sprintf( _x( 'Edit %s', 'post type name', 'edit-flow' ), 'rt_leave' ) ); ?>&nbsp;&raquo;</a>
	</div>
	<div class="spinner">&nbsp;</div>
</form>

