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
			echo 'Calendar View';
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
			<ul class="ef-calendar-navigation">
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
			$url = add_query_arg( $filters, menu_page_url( 'calendar_view', false ) );

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