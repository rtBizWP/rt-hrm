<?php
/**
 * Don't load this file directly!
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of RT_Calendar
 *
 * @author Dipesh
 */

if ( ! class_exists( 'RT_Calendar' ) ) {
	class RT_Calendar{

		/**
		 * Dom Target element where the calendar is to be displayed
		 */
		var $dom_element;

		/**
		 *
		 */
		public function __construct() {

		}

		/**
		 * Render Calendar
		 */
		function render_calendar(  ) {
			?>
			<script type="text/javascript">
			jQuery(document).ready( function( $ ) {

					$('#calendar-container').fullCalendar({
						header: {
							left: 'prev,next today',
							center: 'title',
							right: 'month,basicWeek,basicDay'
						},
						defaultView: 'month',
						editable: false,
						events: [
							{
								title: 'Dipesh: Leave',
								start: '2014-04-04',
								end: '2014-04-07',
								color: 'green',
								textColor: 'black'
							},
							{
								title: 'Udit: Leave',
								start: '2014-04-05',
								color: 'red',
								textColor: 'black'
							}

							// etc...
						],
						dayClick: function(date, jsEvent, view) {
							$('#calendar-container').trigger('dayClick', [ $(this), date, jsEvent, view ] );
						}
					})

				});
			</script>
			<?php
		}

	}
}