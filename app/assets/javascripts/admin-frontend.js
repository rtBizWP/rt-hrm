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
			rtHRMAdmin.eleLeaveUserID = $('#leave-user-id');
			rtHRMAdmin.eleLeaveUser = $('.user-autocomplete');
            rtHRMAdmin.eleLeaveType = $('input[name="post[leave-type][]"]:checked');
			rtHRMAdmin.lblLeaveStartDate = $('#lblleave-start-date');
			rtHRMAdmin.eleLevaeStartDate = $("#leave-start-date");
			rtHRMAdmin.eleLevaeDayType = $("#leave-duration");
			rtHRMAdmin.eleLevaeEndDate = $("#leave-end-date");
            rtHRMAdmin.eleLevaeDesc = $("#leave_description");
			rtHRMAdmin.eleLeaveQuotaUse = $('#leave_quota_use');

			rtHRMAdmin.initDatePicker();
			rtHRMAdmin.initRtCalenderMethod();
			rtHRMAdmin.leaveDayChange();
			rtHRMAdmin.leaveDayCheck();
			rtHRMAdmin.add_leave_validate();
            rtHRMAdmin.autocompleteUser();
			rtHRMAdmin.paidLeaveChange();
			rtHRMAdmin.initDashboardWPMenuHack();
		},
		initDatePicker : function(){
			//call datepicker
			$(document).on('focus', ".datepicker",function(){
				$(this).datepicker({
					'dateFormat': 'dd/mm/yy'
				});
			})
		},
		initRtCalenderMethod : function(){
            rtHRMAdmin.employeeId= rtHRMAdmin.eleLeaveUserID.val()
            rtHRMAdmin.employeeName= rtHRMAdmin.eleLeaveUser.val()
			$('#calendar-container').on('rtBeforePopup', rtHRMAdmin.rtHrmBeforePopup );
            $('#calendar-container').on('rtEventClick', rtHRMAdmin.rtHrmEventClick );
		},
		rtHrmBeforePopup : function( e, self, date, jsEvent, view ){
			rtHRMAdmin.lblLeaveStartDate.text( moment(date).format('MMMM DD YYYY, dddd') );
			rtHRMAdmin.eleLevaeStartDate.val( moment(date).format('DD/MM/YYYY') );
			rtHRMAdmin.eleLevaeEndDate.val( moment(date).format('DD/MM/YYYY') );
            rtHRMAdmin.eleLeaveUserID.val( rtHRMAdmin.employeeId );
            rtHRMAdmin.eleLeaveUser.val( rtHRMAdmin.employeeName );
            $('input[name="post[leave-type][]"]:checked').attr('checked', false);
            rtHRMAdmin.eleLevaeDesc.val('');
			//remove if any error visible
			removeError(rtHRMAdmin.eleLevaeStartDate);
			removeError(rtHRMAdmin.eleLevaeEndDate);
			return false;
		},
        rtHrmEventClick : function ( e, self, event, element ){
            window.location = frontendhrmurl + "/leave/?rt_leave_id=" + event.leave_id + "&action=edit";
        },
		leaveDayChange : function(){
			if ( rtHRMAdmin.eleLevaeDayType.val()=='Other' || rtHRMAdmin.eleLevaeDayType.val()=='other' ) {
				rtHRMAdmin.eleLevaeEndDate.parent().show();
			} else {
				rtHRMAdmin.eleLevaeEndDate.val('');
				rtHRMAdmin.eleLevaeEndDate.parent().hide();
			}
			$(document).on('change','#'+rtHRMAdmin.eleLevaeDayType.attr('id'),function(){
				if ( $(this).val()=='Other' || $(this).val()=='other' ) {
					rtHRMAdmin.eleLevaeEndDate.parent().show();
				} else {
					rtHRMAdmin.eleLevaeEndDate.val('');
					rtHRMAdmin.eleLevaeEndDate.parent().hide();
				}
			})
		},
		paidLeaveChange: function() {
			rtHRMAdmin.eleLeaveUserID.bind('change');
			rtHRMAdmin.eleLeaveUserID.on('change', function(e) {
				e.preventDefault();
				rtHRMAdmin.eleLeaveQuotaUse.closest('tr').addClass('hide');
				rtHRMAdmin.eleLeaveQuotaUse.prop('checked', false).change();
				if ( $(this).val().trim() == '' ) {
					return;
				}
				employee_id = $(this).val();
				$.post(ajaxurl, {
					action: 'rt_hrm_check_user_leave_quota',
					employee_id: employee_id
				}, function(data) {
					data = $.parseJSON(data);
					if ( data.status == 'success' ) {
                                                $('#remaining-leave-quota').text(data.leave_quota);
						rtHRMAdmin.eleLeaveQuotaUse.closest('tr').removeClass('hide');
					}
				});
			});
		},
		add_leave_validate : function(){
			$("#post, #form-add-leave").submit(function(e) {
				try {
                    if ( typeof(rtHRMAdmin.eleLeaveUserID.val()) == "undefined"
						|| rtHRMAdmin.eleLeaveUserID.val().trim() == ""
						|| rtHRMAdmin.eleLeaveUser.val().trim() == '' ) {
                        addError(rtHRMAdmin.eleLeaveUserID, "Please Enter valid Employee Name");
                        rtHRMAdmin.removeLodingOnNewPost();
                        return false;
                    }
                    if ( typeof(rtHRMAdmin.eleLevaeStartDate.val()) == "undefined"
						|| rtHRMAdmin.eleLevaeStartDate.val().trim() == "") {
						addError(rtHRMAdmin.eleLevaeStartDate, "Please Enter the Leave Start Date");
                        rtHRMAdmin.removeLodingOnNewPost();
                        return false;
					}
                                        
                                      
                                        
					removeError(rtHRMAdmin.eleLevaeStartDate);
					if( rtHRMAdmin.eleLevaeDayType.val().trim() == "other" ){
						if (rtHRMAdmin.eleLevaeEndDate.val().trim() == "") {
							addError(rtHRMAdmin.eleLevaeEndDate, "Please Enter the Leave End Date");
                            rtHRMAdmin.removeLodingOnNewPost();
                            return false;
						}
					}
					removeError(rtHRMAdmin.eleLevaeEndDate);
				} catch (e) {
					console.log(e);
					return false;
				}
			});
		},
        autocompleteUser : function(){
            if(rtHRMAdmin.eleLeaveUser.length > 0){
                rtHRMAdmin.eleLeaveUser.autocomplete({
                    source: function( request, response ) {
                        rtHRMAdmin.eleLeaveUserID.val("").trigger('change');
                        $.ajax({
                            url: ajaxurl,
                            dataType: "json",
                            type:'post',
                            data: {
                                action: "seach_employees_name",
                                maxRows: 10,
                                query: request.term
                            },
                            success: function( data ) {
                                response( $.map( data, function( item ) {
                                    return {
                                        id: item.id ,
                                        label:item.label
                                    }
                                }));
                            }
                        });
                    },minLength: 2,
                    select: function(event, ui) {
                        rtHRMAdmin.eleLeaveUser.val(ui.item.label);
                        rtHRMAdmin.eleLeaveUserID.val(ui.item.id).trigger('change');
                        return false;
                    }
                }).data("ui-autocomplete")._renderItem = function(ul, item) {
                    return $("<li></li>").data("ui-autocomplete-item", item).append("<a class='ac-subscribe-selected'>" +  item.label + "</a>").appendTo(ul);
                };
            }
        },
        removeLodingOnNewPost : function(){
            $('#save-action #save-post').removeClass('button-disabled');
            $('#save-action #save-post').val('Save Draft');
            $('#save-action span').attr('style','display: none;');
            $('#publishing-action #save-publish').removeClass('button-primary-disabled');
        },

		initDashboardWPMenuHack : function() {
			/**
			* WordPress Menu Hack for Dashboard
			*/
		   if ( typeof rt_hrm_top_menu != 'undefined' && typeof rt_hrm_dashboard_url != 'undefined' ) {
			   $('#'+rt_hrm_top_menu+' ul li').removeClass('current');
			   $('#'+rt_hrm_top_menu+' ul li a').removeClass('current');
			   $('#'+rt_hrm_top_menu+' ul li a').each(function(e) {
				   if ( this.href == rt_hrm_dashboard_url ) {
					   $(this).parent().addClass("current");
					   $(this).addClass('current');
				   }
			   });
		   }
		},
                
                leaveDayCheck:function(){
                    $("#leave-start-date").change(function(){
                      if ( !typeof(rtHRMAdmin.eleLeaveUserID.val()) == "undefined"
						|| !rtHRMAdmin.eleLeaveUserID.val().trim() == ""
						|| !rtHRMAdmin.eleLeaveUser.val().trim() == '' ) {
                         $.post(ajaxurl, {
                                        action: 'rt_hrm_check_leave_day',
					leave_user_id: rtHRMAdmin.eleLeaveUserID.val().trim(),
					leave_start_date: rtHRMAdmin.eleLevaeStartDate.val().trim()
				}, function(data) {
					data = $.parseJSON(data);
					if ( data.status == 'error' ) {
                                            rtHRMAdmin.eleLevaeStartDate.val('');
						addError(rtHRMAdmin.eleLevaeStartDate, data.message);   
					}else{
                                            removeError(rtHRMAdmin.eleLevaeStartDate);
                                        }   
				});  
                        }
                    });
                 
                }

	}
	rtHRMAdmin.init();
});