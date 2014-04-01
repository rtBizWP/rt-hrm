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
 * Description of Rt_HRM_Calendar
 *
 * @author Dipesh
 */
if ( !class_exists( 'Rt_HRM_Calendar' ) ) {
	class Rt_HRM_Calendar {

		var $screen_id;
		var $charts = array();
		var $start_date = '';
		var $current_week = 1;
		var $total_weeks = 6;
		var $hidden = 0;
		var $max_visible_posts_per_date = 4;
		var $post_type;

		public function __construct() {
			$this->screen_id = '';
		}

		function setup_calendar() {
			/* Add callbacks for this screen only */
			add_action( 'load-'.$this->screen_id, array( $this, 'page_actions' ), 9 );
			add_action( 'admin_footer-'.$this->screen_id, array( $this, 'footer_scripts' ) );

			/* render data into calendar */
			add_action( 'rthrm_after_calendar', array( $this, 'render_calendar' ) );
		}

		/**
		 *
		 */
		function add_screen_id( $screen_id ) {
			$this->screen_id = $screen_id;
		}

		/**
		 *
		 */
		function add_post_type( $post_type ) {
			$this->post_type = $post_type;
		}

		/**
		 * Prints the jQuery script to initiliase the metaboxes
		 * Called on admin_footer-*
		 */
		function footer_scripts() { ?>

		<?php }

		/*
		* Actions to be taken prior to page loading. This is after headers have been set.
		* call on load-$hook
		* This calls the add_meta_boxes hooks, adds screen options and enqueues the postbox.js script.
		*/
		function page_actions() {
			global $rt_hrm_module;

			if ( isset( $_REQUEST['page'] ) && $_REQUEST['page'] === 'rthrm-'.$rt_hrm_module->post_type.'-calendar_view' ) {

			}
		}

		function ui( $post_type ) {
			//rthrm_get_template( 'admin/dashboard.php', array( 'post_type' => $post_type ) );
			$dotw = array(
				'Sat',
				'Sun',
			);
			$filters['start_date'] = date( 'Y-m-d', current_time( 'timestamp' ) );
			if( isset( $_REQUEST['start_date'] ) && !empty( $_REQUEST['start_date'] ) ){
				$filters['start_date'] = date('Y-m-d', strtotime( $_REQUEST['start_date'] ) );
			}

			$filters['start_date'] = $this->get_beginning_of_week( $filters['start_date'], 'Y-m-d' , 1 );

			$this->start_date = $filters['start_date'];

			$dates = array();
			$heading_date = $filters['start_date'];
			for ( $i=0; $i<7; $i++ ) {
				$dates[$i] = $heading_date;
				$heading_date = date( 'Y-m-d', strtotime( "+1 day", strtotime( $heading_date ) ) );
			}

			?>

			<div class="wrap">
				<div id="rt-hrm-calendar-title"><!-- Calendar Title -->
					<h2><?php _e( 'Calendar', 'edit-flow' ); ?>&nbsp;<span class="time-range"><?php $this->calendar_time_range(); ?></span></h2>
				</div><!-- /Calendar Title -->

				<?php
				// Handle posts that have been trashed or untrashed
				?>

				<div id="rt-hrm-calendar-wrap"><!-- Calendar Wrapper -->

					<?php $this->print_top_navigation( $filters ); ?>

					<?php
					$table_classes = array();
					// CSS don't like our classes to start with numbers
					if ( $this->total_weeks == 1 )
						$table_classes[] = 'one-week-showing';
					elseif ( $this->total_weeks == 2 )
						$table_classes[] = 'two-weeks-showing';
					elseif ( $this->total_weeks == 3 )
						$table_classes[] = 'three-weeks-showing';

					$table_classes = apply_filters( 'rt-hrm_calendar_table_classes', $table_classes );
					?>
					<table id="rt-hrm-calendar-view" class="<?php echo esc_attr( implode( ' ', $table_classes ) ); ?>">
						<thead>
						<tr class="calendar-heading">
							<?php echo $this->get_time_period_header( $dates ); ?>
						</tr>
						</thead>
						<tbody>

						<?php
						$current_month = date( 'F', strtotime( $filters['start_date'] ) );
						for( $current_week = 1; $current_week <= $this->total_weeks; $current_week++ ):
							// We need to set the object variable for our posts_where filter
							$this->current_week = $current_week;
							//$week_posts = $this->get_calendar_posts_for_week( $post_query_args );
							$date_format = 'Y-m-d';
							$week_single_date = $this->get_beginning_of_week( $filters['start_date'], $date_format, $current_week );
							$week_dates = array();
							$split_month = false;
							for ( $i = 0 ; $i < 7; $i++ ) {
								$week_dates[$i] = $week_single_date;
								$single_date_month = date( 'F', strtotime( $week_single_date ) );
								if ( $single_date_month != $current_month ) {
									$split_month = $single_date_month;
									$current_month = $single_date_month;
								}
								$week_single_date = date( 'Y-m-d', strtotime( "+1 day", strtotime( $week_single_date ) ) );
							}
							?>
							<?php if ( $split_month ): ?>
							<tr class="month-marker">
								<?php foreach( $week_dates as $key => $week_single_date ) {
									if ( date( 'F', strtotime( $week_single_date ) ) != $split_month && date( 'F', strtotime( "+1 day", strtotime( $week_single_date ) ) ) == $split_month ) {
										$previous_month = date( 'F', strtotime( $week_single_date ) );
										echo '<td class="month-marker-previous">' . esc_html( $previous_month ) . '</td>';
									} else if ( date( 'F', strtotime( $week_single_date ) ) == $split_month && date( 'F', strtotime( "-1 day", strtotime( $week_single_date ) ) ) != $split_month ) {
										echo '<td class="month-marker-current">' . esc_html( $split_month ) . '</td>';
									} else {
										echo '<td class="month-marker-empty"></td>';
									}
								} ?>
							</tr>
						<?php endif; ?>

							<tr class="week-unit">
								<?php foreach( $week_dates as $day_num => $week_single_date ): ?>
									<?php
									// Somewhat ghetto way of sorting all of the day's posts by post status order
									$td_classes = array(
										'day-unit',
									);
									$day_name = date( 'D', strtotime( $week_single_date ) );

									if ( in_array( $day_name, $dotw ) )
										$td_classes[] = 'weekend-day';

									if ( $week_single_date == date( 'Y-m-d', current_time( 'timestamp' ) ) )
										$td_classes[] = 'today';

									// Last day of the week
									if ( $day_num == 6 )
										$td_classes[] = 'last-day';

									$td_classes = apply_filters( 'rt-hrm_calendar_table_td_classes', $td_classes, $week_single_date );
									?>
									<td class="<?php echo esc_attr( implode( ' ', $td_classes ) ); ?>" id="<?php echo esc_attr( $week_single_date ); ?>">
										<button class='schedule-new-leave-button'>+</button>
										<?php if ( $week_single_date == date( 'Y-m-d', current_time( 'timestamp' ) ) ): ?>
											<div class="day-unit-today"><?php _e( 'Today', 'edit-flow' ); ?></div>
										<?php endif; ?>
										<div class="day-unit-label"><?php echo esc_html( date( 'j', strtotime( $week_single_date ) ) ); ?></div>
										<ul class="leave-list">

										</ul>
										<?php $date_formatted = date( 'D, M jS, Y', strtotime( $week_single_date ) ); ?>
										<form method="POST" class="leave-insert-dialog leave-insert-dialog item-overlay leave-insert-overlay">
											<?php /* translators: %1$s = post type name, %2$s = date */ ?>
											<div class="leave-insert-dialog-title">
												<h2><?php echo sprintf( __( 'Schedule a %1$s for %2$s', 'edit-flow' ), $this->post_type, $date_formatted ); ?></h2>
											</div>
											<div class="leave-insert-dialog-body">
												<input type="text" class="leave-insert-dialog-leave-user" name="leave-insert-dialog-leave-user" placeholder="<?php echo esc_attr( _x( 'User Name', 'User Name') ); ?>">
												<input type="text" class="leave-insert-dialog-leave-title" name="leave-insert-dialog-leave-end-title" placeholder="<?php echo esc_attr( _x( 'Select End date', 'post type name') ); ?>">
												<input type="text" class="leave-insert-dialog-leave-title" name="leave-insert-dialog-leave-end-title" placeholder="<?php echo esc_attr( _x( 'Select date', 'post type name') ); ?>">
												<input type="hidden" class="leave-insert-dialog-leave-date" name="leave-insert-dialog-leave-start-title" value="<?php echo esc_attr( $week_single_date ); ?>">
											</div>
											<div class="leave-insert-dialog-controls">
												<input type="submit" class="button left" value="<?php echo esc_html( sprintf( _x( 'Create %s', 'post type name', 'edit-flow' ), $this->post_type ) ); ?>">
												<a class="leave-insert-dialog-edit-leave-link" href="#"><?php echo esc_html( sprintf( _x( 'Edit %s', 'post type name', 'edit-flow' ), $this->post_type ) ); ?>&nbsp;&raquo;</a>
											</div>
											<div class="spinner">&nbsp;</div>
										</form>

									</td>
								<?php endforeach; ?>
							</tr>

						<?php endfor; ?>

						</tbody>
					</table><!-- /Week Wrapper -->
					<?php
					// Nonce field for AJAX actions
					wp_nonce_field( 'rt-hrm-calendar-modify', 'rt-hrm-calendar-modify' ); ?>

					<div class="clear"></div>
				</div><!-- /Calendar Wrapper -->

			</div> <?php
		}

		function render_calendar() {

		}


		// Helper function for Calendar
		function calendar_time_range() {
			$first_datetime = strtotime( $this->start_date );
			if ( date( 'Y', current_time( 'timestamp' ) ) != date( 'Y', $first_datetime ) )
				$first_date = date( 'F jS, Y', $first_datetime );
			else
				$first_date = date( 'F jS', $first_datetime );
			$total_days = ( $this->total_weeks * 7 ) - 1;
			$last_datetime = strtotime( "+" . $total_days . " days", date( 'U', strtotime( $this->start_date ) ) );
			if ( date( 'Y', current_time( 'timestamp' ) ) != date( 'Y', $last_datetime ) )
				$last_date = date( 'F jS, Y', $last_datetime );
			else
				$last_date = date( 'F jS', $last_datetime );
			echo sprintf( __( 'for %1$s through %2$s'), $first_date, $last_date );
		}


		function print_top_navigation( $filters ) {
			?>
			<ul class="rt-hrm-calendar-navigation">
				<?php /** Previous and next navigation items (translatable so they can be increased if needed )**/ ?>
				<li class="date-change next-week">
					<a title="<?php printf( __( 'Forward 1 week', 'edit-flow' ) ); ?>" href="<?php echo esc_url( $this->get_pagination_link( 'next', $filters, 1 ) ); ?>"><?php _e( '&rsaquo;', 'edit-flow' ); ?></a>
					<?php if ( $this->total_weeks > 1): ?>
						<a title="<?php printf( __( 'Forward %d weeks', 'edit-flow' ), $this->total_weeks ); ?>" href="<?php echo esc_url( $this->get_pagination_link( 'next', $filters ) ); ?>"><?php _e( '&raquo;', 'edit-flow' ); ?></a>
					<?php endif; ?>
				</li>
				<li class="date-change today">
					<a title="<?php printf( __( 'Today is %s', 'edit-flow' ), date( get_option( 'date_format' ), current_time( 'timestamp' ) ) ); ?>" href="<?php echo esc_url( $this->get_pagination_link( 'next', $filters, 0 ) ); ?>"><?php _e( 'Today', 'edit-flow' ); ?></a>
				</li>
				<li class="date-change previous-week">
					<?php if ( $this->total_weeks > 1): ?>
						<a title="<?php printf( __( 'Back %d weeks', 'edit-flow' ), $this->total_weeks ); ?>"  href="<?php echo esc_url( $this->get_pagination_link( 'previous', $filters ) ); ?>"><?php _e( '&laquo;', 'edit-flow' ); ?></a>
					<?php endif; ?>
					<a title="<?php printf( __( 'Back 1 week', 'edit-flow' ) ); ?>" href="<?php echo esc_url( $this->get_pagination_link( 'previous', $filters, 1 ) ); ?>"><?php _e( '&lsaquo;', 'edit-flow' ); ?></a>
				</li>
				<li class="ajax-actions">
					<img class="waiting" style="display:none;" src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>" alt="" />
				</li>
			</ul>
			<?php
		}

		function get_pagination_link( $direction = 'next', $filters = array(), $weeks_offset = null ) {
			if ( !isset( $weeks_offset ) )
				$weeks_offset = $this->total_weeks;
			else if ( $weeks_offset == 0 )
				$filters['start_date'] = $this->get_beginning_of_week( date( 'Y-m-d', current_time( 'timestamp' ) ) );

			if ( $direction == 'previous' )
				$weeks_offset = '-' . $weeks_offset;

			$filters['start_date'] = date( 'Y-m-d', strtotime( $weeks_offset . " weeks", strtotime( $filters['start_date'] ) ) );
			$arg=array(
				'post_type'=>$this->post_type,
				'page'=>'rthrm-' . $this->post_type . '-calendar',
			);
			$arg = array_merge( $arg, $filters );
			$url = add_query_arg( $arg, menu_page_url( $this->post_type, false ) );

			return $url;
		}

		function get_time_period_header( $dates ) {
			$html = '';
			foreach( $dates as $date ) {
				$html .= '<th class="column-heading" >';
				$html .= esc_html( date('l', strtotime( $date ) ) );
				$html .= '</th>';
			}

			return $html;
		}

		function get_beginning_of_week( $date, $format = 'Y-m-d', $week = 1 ) {
			$date = strtotime( $date );
			$start_of_week = 1;
			$day_of_week = date( 'w', $date );
			$date += (( $start_of_week - $day_of_week - 7 ) % 7) * 60 * 60 * 24 * $week;
			$additional = 3600 * 24 * 7 * ( $week - 1 );
			$formatted_start_of_week = date( $format, $date + $additional );
			return $formatted_start_of_week;
		}

	}
}