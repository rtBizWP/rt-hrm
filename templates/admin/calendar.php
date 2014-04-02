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
<form method="POST" class="leave-insert-dialog leave-insert-dialog item-overlay leave-insert-overlay">
	<?php /* translators: %1$s = post type name, %2$s = date */ ?>
	<div class="leave-insert-dialog-title">
		<h2><?php echo sprintf( __( 'Schedule a %1$s for %2$s', 'edit-flow' ), 'rt_leave', 'date_formatted' ); ?></h2>
	</div>
	<div class="leave-insert-dialog-body">
		<input type="text" id="leave-user" class="leave-insert-dialog-input" name="leave-insert-dialog-leave-user" placeholder="<?php echo esc_attr( _x( 'User Name', 'User Name') ); ?>">
		<input type="text" id="leave-start-date" class="leave-insert-dialog-input leave-start-date datepicker" name="leave-insert-dialog-leave-end-title" placeholder="<?php echo esc_attr( _x( 'Startiing date', 'Leave Start Date') ); ?>">
		<input type="text" id="leave-end-date" class="leave-insert-dialog-input leave-end-date datepicker" name="leave-insert-dialog-leave-end-title" placeholder="<?php echo esc_attr( _x( 'Ending date', 'Leave End Date') ); ?>">
		<label class="leave-insert-dialog-label" id="leave-days">2 days</label>
		<select class="leave-insert-dialog-input" id="leave-type">
			<option>Casual leave</option>
			<option>Sick leave</option>
		</select>
	</div>
	<div class="leave-insert-dialog-controls">
		<input type="submit" class="button left" value="<?php echo esc_html( sprintf( _x( 'Create %s', 'post type name', 'edit-flow' ), 'rt_leave' ) ); ?>">
		<a class="button right" href="#"><?php echo esc_html( sprintf( _x( 'Edit %s', 'post type name', 'edit-flow' ), 'rt_leave' ) ); ?>&nbsp;&raquo;</a>
	</div>
	<div class="spinner">&nbsp;</div>
</form>
