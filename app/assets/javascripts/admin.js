rthrm_user_edit = rthrm_user_edit[0];

jQuery(document).ready(function($) {


	function addError(element, message) {
		$(element).addClass("error");
		if ($(element).next().length > 0) {
			if ($(element).next().hasClass("error")) {
				$(element).next().html(message);
			} else {
				$(element).after("<small class='error'>" + message + "</small>");
			}
		} else {
			$(element).after("<small class='error'>" + message + "</small>");
		}
	}
	function removeError(element) {
		$(element).removeClass("error");
		if ($(element).next().length > 0) {
			if ($(element).next().hasClass("error")) {
				$(element).next().remove();
			}
		}
	}

	var rtHRMAdmin = {
		/**
		 *
		 */
		init : function(){
			rtHRMAdmin.initDatePicker();
			rtHRMAdmin.initRtCalenderMethod();
			rtHRMAdmin.leaveTimeChange();
			rtHRMAdmin.add_leave_validate();

			rtHRMAdmin.eleLevaeStartDate = $("#txtleave-start-date");
			rtHRMAdmin.eleLevaeDayType = $("#cmbleave-day-type");
			rtHRMAdmin.eleLevaeEndDate = $("#txtleave-end-date");

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

			//remove if any error visible
			removeError(rtHRMAdmin.eleLevaeStartDate);
			removeError(rtHRMAdmin.eleLevaeEndDate);
			return false;
		},
		leaveTimeChange : function(){
			$('#cmbleave-day-type').live('change',function(){
				if ( $(this).val()=='other' ) {
					$('#tr-end-date').show();
				}else{
					$('#txtleave-end-date').val('');
					$('#tr-end-date').hide();
				}
			})
		},
		add_leave_validate : function(){
			$("#post, #form-add-leave").submit(function(e) {
				try {
					if ($(rtHRMAdmin.eleLevaeStartDate).val().trim() == "") {
						addError(rtHRMAdmin.eleLevaeStartDate, "Please Enter the Leave Start Date");
						return false;
					}
					removeError(rtHRMAdmin.eleLevaeStartDate);
					if( $(rtHRMAdmin.eleLevaeDayType).val().trim() == "other" ){
						if ($(rtHRMAdmin.eleLevaeEndDate).val().trim() == "") {
							addError(rtHRMAdmin.eleLevaeEndDate, "Please Enter the Leave End Date");
							return false;
						}
					}
					removeError(rtHRMAdmin.eleLevaeEndDate);
				} catch (e) {
					console.log(e);
					return false;
				}
			});
		}
	}
	rtHRMAdmin.init();
});