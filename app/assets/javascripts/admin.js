rthrm_user_edit = rthrm_user_edit[0];

jQuery(document).ready(function($) {
	var rtHRMAdmin = {
		/**
		 *
		 */
		init : function(){
			rtHRMAdmin.initDatePicker();
			rtHRMAdmin.initRtCalenderMethod();
			rtHRMAdmin.leaveTimeChange();
		},
		initDatePicker : function(){
			//call datepicker
			$(".datepicker").live('focus', function(){
				$(this).datepicker({
					'dateFormat': 'dd/mm/yy'
				});
			})
		},
		initRtCalenderMethod : function(){
			$('#calendar-container').on('rtBeforePopup', rtHRMAdmin.rtHrmBeforePopup );
		},
		rtHrmBeforePopup : function( e, self, date, jsEvent, view ){
			rtHRMAdmin.$lbl_leave_start_day = $('#lblleave-start-date')
			rtHRMAdmin.$leave_start_day = $('#txtleave-start-date');
			rtHRMAdmin.$leave_end_day = $('#txtleave-end-date');

			rtHRMAdmin.$lbl_leave_start_day.text( moment(date).format('MMMM DD YYYY') );
			rtHRMAdmin.$leave_start_day.val( moment(date).format('DD/MM/YYYY') );
			rtHRMAdmin.$leave_end_day.val( moment(date).format('DD/MM/YYYY') );

			return false;
		},
		leaveTimeChange : function(){
			$('#tr-end-date').hide();
			$('#cmbleave-day-type').live('change',function(){
				if ( $(this).val()=='other' ) {
					$('#tr-end-date').show();
				}else{
					$('#tr-end-date').hide();
				}
			})
		}
	}
	rtHRMAdmin.init();
});