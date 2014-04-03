rthrm_user_edit = rthrm_user_edit[0];

jQuery(document).ready(function($) {
    var LOADER_OVERLAY = $("<div class='loading-overlay'><i class='loader-icon'></i></div>");
	/*$.ajaxSetup({
		beforeSend : function(jqXHR, settings) {
			if(settings.data.indexOf('heartbeat') === -1 && settings.data.indexOf('closed-postboxes') === -1 && settings.data.indexOf('meta-box-order') === -1) {
				$("body").append(LOADER_OVERLAY);
			}
		},
		complete : function(jqXHR, settings) {
			$("body").find(".loading-overlay").remove();
		}
	});*/

	//Close overlays
	function rthrm_calendar_close_overlays() {

		$('.leave-insert-dialog').removeClass('item-overlay').addClass('item-static');

	}

	// Enables quick creation/edit of leave on a particular date from the calendar
	var rtHRMQuickPublish = {
		/**
		 *
		 */
		init : function(){

			//call datepicker
			$(".datepicker").live('focus', function(){
				$(this).datepicker({
					'dateFormat': 'DD/MM/YYYY'
				});
			})

			//Close other overlays on ESC key
			$(document).keydown(function(event) {
				if (event.keyCode == '27') {
					rthrm_calendar_close_overlays();
				}
			});

			$('#calendar-container').on('dayClick', rtHRMQuickPublish.open_quickleave_dialogue );

			//End date & leave-day-type
			$('#tr-end-date').hide();
			$('#cmbleave-day-type').live('change',function(){
				if ( $(this).val()=='other' ) {
					$('#tr-end-date').show();
				}else{
					$('#tr-end-date').hide();
				}
			})

		}, // init
		open_quickleave_dialogue : function(e, parent, date, jsEvent, view){

			e.preventDefault();

			// Close other overlays
			rthrm_calendar_close_overlays();

			rtHRMQuickPublish.$lbl_leave_start_day = $('#lblleave-start-date')
			rtHRMQuickPublish.$leave_start_day = $('#txtleave-start-date');
			rtHRMQuickPublish.$leave_end_day = $('#txtleave-end-date');
			var $new_leave_form_content = $('.leave-insert-dialog');

			rtHRMQuickPublish.$lbl_leave_start_day.text( moment(date).format('MMMM DD YYYY') );
			rtHRMQuickPublish.$leave_start_day.val( moment(date).format('DD/MM/YYYY') );
			rtHRMQuickPublish.$leave_end_day.val( moment(date).format('DD/MM/YYYY') );
			rtHRMQuickPublish.$new_leave_form = $new_leave_form_content.addClass('item-overlay leave-insert-overlay')
		    return false;
		}
	}

	rtHRMQuickPublish.init();

})