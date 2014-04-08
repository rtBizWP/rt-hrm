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

			rtHRMAdmin.LblLeaveStartDate = $('#lblleave-start-date')
			rtHRMAdmin.eleLevaeStartDate = $("#leave-start-date");
			rtHRMAdmin.eleLevaeDayType = $("#leave-day-type");
			rtHRMAdmin.eleLevaeEndDate = $("#leave-end-date");

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
			rtHRMAdmin.LblLeaveStartDate.text( moment(date).format('MMMM DD YYYY') );
			rtHRMAdmin.eleLevaeStartDate.val( moment(date).format('DD/MM/YYYY') );
			rtHRMAdmin.eleLevaeEndDate.val( moment(date).format('DD/MM/YYYY') );

			//remove if any error visible
			removeError(rtHRMAdmin.eleLevaeStartDate);
			removeError(rtHRMAdmin.eleLevaeEndDate);
			return false;
		},
		leaveTimeChange : function(){
			$('#leave-day-type').live('change',function(){
				if ( $('#leave-day-type option:selected').text()=='Other' || $('#leave-day-type option:selected').text()=='other' ) {
					$('#tr-end-date').show();
				}else{
					$('#leave-end-date').val('');
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