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

	var rtHRMFrontend = {
		/**
		 *
		 */
		init : function(){
			rtHRMFrontend.sortOrder();
            rtHRMFrontend.leaveListing();
		},
		sortOrder : function(){
			$( ".order" ).change(function() {
				$( ".lists tr.lists-data" ).remove();
				//rtHRMFrontend.leaveListing();
			});
		},
        leaveListing : function(){
			$( ".order" ).change(function() {
				$.ajax({
					url: ajaxurl,
					dataType: "json",
					type: 'POST',
					data: {
						action: "leave_listing_info",
						order:  $(this).val(),
						attr:  $(this).attr('name')
					},
					success: function( data ) {
						console.log( data );
						$.each( data, function( i, val ) {
							$( ".lists tr.lists-header" ).after( '<tr class="lists-data"><td class="leavetype">' + data[i].leavetype + '<br /><span><a href="' + data[i].editpostlink + '">Edit</a></span>&nbsp;&#124;<a href="' + data[i].permalink + '">View</a></td><td>' + data[i].leavestartdate + '</td><td>' + data[i].leaveenddate + '</td><td>' + data[i].poststatus + '</td></tr>' );
						});
						
						// $( "table.lists" ).after('<ul><li><a class="page-link" data-page="2">Previous</a></li><li><a class="page-link" data-page="2">Next</a></li></ul>');
						
						
					}
				});
			});
        }
	}
	rtHRMFrontend.init();
});