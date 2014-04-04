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
		var $default;
		var $dom_element;
		var $popup_element;
		var $event;

		/**
		 *
		 */
		public function __construct() {
			$this->event= array(
				array(
					'title'=> 'Dipesh: Leave',
					'start'=>'2014-04-04',
					'end'=> '2014-04-07',
					'color'=> 'green',
					'textColor'=> 'black',
				),
				array(
					'title'=> 'udit: Leave',
					'end'=> '2014-04-05',
					'color'=> 'red',
					'textColor'=> 'black',
				),
			);

			$this->default = array(
				'header'=> array(
					'left' => 'prev,next today',
					'center' => 'title',
					'right' => 'month,basicWeek,basicDay'
				),
				'defaultView' => 'month',
				'editable' => false,
				'events' => $this->event,
			);
		}

		/**
		 * @param mixed $dom_element
		 */
		public function setDomElement($dom_element)
		{
			$this->dom_element = $dom_element;
		}

		/**
		 * @param mixed $event
		 */
		public function setEvent($event)
		{
			$this->event = $event;
		}

		/**
		 * @param mixed $popup_element
		 */
		public function setPopupElement($popup_element)
		{
			$this->popup_element = $popup_element;
		}

		/**
		 * Render Calendar
		 */
		function render_calendar(  ) {
			?>
			<script type="text/javascript">
				jQuery(document).ready( function( $ ) {

					function rt_calendar_close_overlays() {
						$('<?php echo $this->dom_element ?>').trigger('rtBeforePopupClose', [ $(this) ] );
						$('<?php echo $this->popup_element ?>').hide();
						$('<?php echo $this->dom_element ?>').trigger('rtBeforePopupClose', [ $(this) ] );
					}

					var rt_calendar={
						init : function(){
							rt_calendar.init_calendar();
							rt_calendar.esc_close();
							<?php if ( isset( $this->popup_element ) && !empty( $this->popup_element ) ){ ?>
								$('<?php echo $this->popup_element ?>').hide();
							<?php } ?>
						},
						init_calendar: function(){
							var json_encode_default=<?php echo json_encode( $this->default ) ?>;
							json_encode_default['dayClick']=function(date, jsEvent, view) {
								<?php if ( isset( $this->popup_element ) && !empty( $this->popup_element ) ){ ?>
									rt_calendar.open_popup( $(this), date, jsEvent, view );
								<?php } ?>
								$('<?php echo $this->dom_element ?>').trigger('rtDayClick', [ $(this), date, jsEvent, view ] );
							}

							$('<?php echo $this->dom_element ?>').fullCalendar(json_encode_default);
						},
						esc_close: function(){
							$(document).keydown(function(event) {
								if (event.keyCode == '27') {
									rt_calendar_close_overlays();
								}
							});
						},
						open_popup: function( self, date, jsEvent, view){
							// Close other overlays
							rt_calendar_close_overlays();

							$('<?php echo $this->dom_element ?>').trigger('rtBeforePopup', [ self, date, jsEvent, view ] );
							$('<?php echo $this->popup_element ?>').show();
							$('<?php echo $this->dom_element ?>').trigger('rtAfterPopup', [ self, date, jsEvent, view ] );
						}
					}
					rt_calendar.init();
				});
			</script>
			<?php
		}

	}
}