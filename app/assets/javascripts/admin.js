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

	if($(".datepicker").length > 0) {
		$(".datepicker").datepicker({
			'dateFormat': 'M d,yy',
			onClose: function(newDate,inst) {
				//Day diff from two date with class name leave-start-date & leave-end-date
				
			}
		});
	}
})