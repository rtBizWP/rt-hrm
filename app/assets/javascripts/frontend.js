jQuery(document).ready(function($) {

	var rtHRMFrontend = {
		/**
		 *
		 */
		init : function(){
            rtHRMFrontend.leaveListing();
			rtHRMFrontend.requestsListing();
		},
        leaveListing : function(){
			var paged = 1;
			var order = "";
			var attr = "";
			var max_num_pages = 999999;
			if ( 1 == paged ){
				$( "li#prev" ).hide();
			} else {
				$( "li#prev" ).show();
			}
			if ( max_num_pages == paged ){
				$( "li#next" ).hide();
			} else {
				$( "li#next" ).show();
			}
			$( ".lists .order" ).change(function() {
				order = $(this).val();
				attr =  $(this).attr('name');
				$( ".lists tr.lists-data" ).remove();
				$.ajax({
					url: ajaxurl,
					dataType: "json",
					type: 'POST',
					data: {
						action: "leave_listing_info",
						order:  order,
						attr:  attr,
						paged: paged
					},
					beforeSend : function(){
						$( ".lists tr.lists-header" ).append('<tr id="loading" style="text-align:center"><td>' +
                            '<img src="' + rthrmurl +'app/assets/img/loading.gif"/>' +
                            '</td></tr>'
						);
					},
					success: function( data ) {
						$.each( data, function( i, val ) {
							$( ".lists tr.lists-header" ).after( '<tr class="lists-data"><td class="leavetype">' + data[i].leavetype + '<br /><span><a href="' + data[i].editpostlink + '">Edit</a></span>&nbsp;&#124;<a href="' + data[i].permalink + '">View</a></td><td>' + data[i].leavestartdate + '</td><td>' + data[i].leaveenddate + '</td><td>' + data[i].poststatus + '</td></tr>' );
						});
						if ( data.length === 0 ){
							$( ".lists tr.lists-data" ).remove();
							$( "ul#pagination" ).remove();
							$( ".lists tr.lists-header" ).after( '<tr class="lists-data"><td colspan="7" align="center" scope="row">No Leave Listing</td></tr>' );
							$( ".lists #loading" ).remove();
							
						} else {
							$( ".lists #loading" ).remove();
						}
					},
					error: function(jqXHR, textStatus, errorThrown) {
					    $( ".lists #loading" ).remove();
						alert(jqXHR + " :: " + textStatus + " :: " + errorThrown);
					}
				});
				
			});
			$( "#next" ).click(function() {
				paged++;
				$( ".lists tr.lists-data" ).remove();
				$.ajax({
					url: ajaxurl,
					dataType: "json",
					type: 'POST',
					data: {
						action: "leave_listing_info",
						order:  order,
						attr:  attr,
						paged: paged
					},
					beforeSend : function(){
						$( ".lists tr.lists-header" ).append('<tr id="loading" style="text-align:center"><td>' +
                            '<img src="' + rthrmurl +'app/assets/img/loading.gif"/>' +
                            '</td></tr>'
						);
					},
					success: function( data ) {
						if ( data.length != 0 ){
							max_num_pages = data[0].max_num_pages;
						}
						if ( max_num_pages == paged ){
							$( "li#next" ).hide();
						} else {
							$( "li#next" ).show();
						}
						if ( 1 == paged ){
							$( "li#prev" ).hide();
						} else {
							$( "li#prev" ).show();
						}
						$.each( data, function( i, val ) {
							$( ".lists tr.lists-header" ).after( '<tr class="lists-data"><td class="leavetype">' + data[i].leavetype + '<br /><span><a href="' + data[i].editpostlink + '">Edit</a></span>&nbsp;&#124;<a href="' + data[i].permalink + '">View</a></td><td>' + data[i].leavestartdate + '</td><td>' + data[i].leaveenddate + '</td><td>' + data[i].poststatus + '</td></tr>' );
						});
						if ( data.length === 0 ){
							$( ".lists tr.lists-data" ).remove();
							$( "ul#pagination" ).remove();
							$( ".lists tr.lists-header" ).after( '<tr class="lists-data"><td colspan="7" align="center" scope="row">No Leave Listing</td></tr>' );
							$( ".lists #loading" ).remove();
							
						} else {
							$( ".lists #loading" ).remove();
						}
					},
					error: function(jqXHR, textStatus, errorThrown) {
					    $( ".lists #loading" ).remove();
						alert(jqXHR + " :: " + textStatus + " :: " + errorThrown);
					}
				});
			});
			$( "#prev" ).click(function() {
				paged--;
				$( ".lists tr.lists-data" ).remove();
				$.ajax({
					url: ajaxurl,
					dataType: "json",
					type: 'POST',
					data: {
						action: "leave_listing_info",
						order:  order,
						attr:  attr,
						paged: paged
					},
					beforeSend : function(){
						$( ".lists tr.lists-header" ).append('<tr id="loading" style="text-align:center"><td>' +
                            '<img src="' + rthrmurl +'app/assets/img/loading.gif"/>' +
                            '</td></tr>'
						);
					},
					success: function( data ) {
						if ( data.length != 0 ){
							max_num_pages = data[0].max_num_pages;
						}
						if ( max_num_pages == paged ){
							$( "li#next" ).hide();
						} else {
							$( "li#next" ).show();
						}
						if ( 1 == paged ){
							$( "li#prev" ).hide();
						} else {
							$( "li#prev" ).show();
						}
						$.each( data, function( i, val ) {
							$( ".lists tr.lists-header" ).after( '<tr class="lists-data"><td class="leavetype">' + data[i].leavetype + '<br /><span><a href="' + data[i].editpostlink + '">Edit</a></span>&nbsp;&#124;<a href="' + data[i].permalink + '">View</a></td><td>' + data[i].leavestartdate + '</td><td>' + data[i].leaveenddate + '</td><td>' + data[i].poststatus + '</td></tr>' );
						});
						if ( data.length === 0 ){
							$( ".lists tr.lists-data" ).remove();
							$( "ul#pagination" ).remove();
							$( ".lists tr.lists-header" ).after( '<tr class="lists-data"><td colspan="7" align="center" scope="row">No Leave Listing</td></tr>' );
							$( ".lists #loading" ).remove();
							
						} else {
							$( ".lists #loading" ).remove();
						}
					},
					error: function(jqXHR, textStatus, errorThrown) {
					    $( ".lists #loading" ).remove();
						alert(jqXHR + " :: " + textStatus + " :: " + errorThrown);
					}
				});
			});
        },
		requestsListing : function(){
			var paged = 1;
			var order = "";
			var attr = "";
			var max_num_pages = 999999;
			if ( 1 == paged ){
				$( "li#prev" ).hide();
			} else {
				$( "li#prev" ).show();
			}
			if ( max_num_pages == paged ){
				$( "li#next" ).hide();
			} else {
				$( "li#next" ).show();
			}
			$( ".requests-lists .order" ).change(function() {
				order = $(this).val();
				attr =  $(this).attr('name');
				$( ".requests-lists tr.lists-data" ).remove();
				$.ajax({
					url: ajaxurl,
					dataType: "json",
					type: 'POST',
					data: {
						action: "requests_listing_info",
						order:  order,
						attr:  attr,
						paged: paged
					},
					beforeSend : function(){
						$( ".requests-lists tr.lists-header" ).append('<tr id="loading" style="text-align:center"><td>' +
                            '<img src="' + rthrmurl +'app/assets/img/loading.gif"/>' +
                            '</td></tr>'
						);
					},
					success: function( data ) {
						$.each( data, function( i, val ) {
							$( ".requests-lists tr.lists-header" ).after( '<tr class="lists-data"><td align="center" scope="row">' + data[i].avatar + '</td><td class="leaveuservalue">' + data[i].leaveuservalue + '<br /><span><a href="' + data[i].editpostlink + '">Edit</a></span>&nbsp;&#124;<a href="' + data[i].permalink + '">View</a>&#124;&nbsp;<a href="' + data[i].deletepostlink + '">Delete</a></td><td>' + data[i].leavetype + '</td><td>' + data[i].leavestartdate + '</td><td>' + data[i].leaveenddate + '</td><td>' + data[i].poststatus + '</td><td>' + data[i].approver + '</td></tr>' );
						});
						if ( data.length === 0 ){
							$( ".requests-lists tr.lists-data" ).remove();
							$( "ul#pagination" ).remove();
							$( ".requests-lists tr.lists-header" ).after( '<tr class="lists-data"><td colspan="7" align="center" scope="row">No Leave Listing</td></tr>' );
							$( ".requests-lists #loading" ).remove();
							
						} else {
							$( ".requests-lists #loading" ).remove();
						}
					},
					error: function(jqXHR, textStatus, errorThrown) {
					    $( ".requests-lists #loading" ).remove();
						alert(jqXHR + " :: " + textStatus + " :: " + errorThrown);
					}
				});
				
			});
			$( "#next" ).click(function() {
				paged++;
				$( ".requests-lists tr.lists-data" ).remove();
				$.ajax({
					url: ajaxurl,
					dataType: "json",
					type: 'POST',
					data: {
						action: "requests_listing_info",
						order:  order,
						attr:  attr,
						paged: paged
					},
					beforeSend : function(){
						$( ".requests-lists tr.lists-header" ).append('<tr id="loading" style="text-align:center"><td>' +
                            '<img src="' + rthrmurl +'app/assets/img/loading.gif"/>' +
                            '</td></tr>'
						);
					},
					success: function( data ) {
						if ( data.length != 0 ){
							max_num_pages = data[0].max_num_pages;
						}
						if ( max_num_pages == paged ){
							$( "li#next" ).hide();
						} else {
							$( "li#next" ).show();
						}
						if ( 1 == paged ){
							$( "li#prev" ).hide();
						} else {
							$( "li#prev" ).show();
						}
						$.each( data, function( i, val ) {
							$( ".requests-lists tr.lists-header" ).after( '<tr class="lists-data"><td align="center" scope="row">' + data[i].avatar + '</td><td class="leaveuservalue">' + data[i].leaveuservalue + '<br /><span><a href="' + data[i].editpostlink + '">Edit</a></span>&nbsp;&#124;<a href="' + data[i].permalink + '">View</a>&#124;&nbsp;<a href="' + data[i].deletepostlink + '">Delete</a></td><td>' + data[i].leavetype + '</td><td>' + data[i].leavestartdate + '</td><td>' + data[i].leaveenddate + '</td><td>' + data[i].poststatus + '</td><td>' + data[i].approver + '</td></tr>' );
						});
						if ( data.length === 0 ){
							$( ".requests-lists tr.lists-data" ).remove();
							$( "ul#pagination" ).remove();
							$( ".requests-lists tr.lists-header" ).after( '<tr class="lists-data"><td colspan="7" align="center" scope="row">No Leave Listing</td></tr>' );
							$( ".requests-lists #loading" ).remove();
							
						} else {
							$( ".requests-lists #loading" ).remove();
						}
					},
					error: function(jqXHR, textStatus, errorThrown) {
					    $( ".requests-lists #loading" ).remove();
						alert(jqXHR + " :: " + textStatus + " :: " + errorThrown);
					}
				});
			});
			$( "#prev" ).click(function() {
				paged--;
				$( ".requests-lists tr.lists-data" ).remove();
				$.ajax({
					url: ajaxurl,
					dataType: "json",
					type: 'POST',
					data: {
						action: "requests_listing_info",
						order:  order,
						attr:  attr,
						paged: paged
					},
					beforeSend : function(){
						$( ".requests-lists tr.lists-header" ).append('<tr id="loading" style="text-align:center"><td>' +
                            '<img src="' + rthrmurl +'app/assets/img/loading.gif"/>' +
                            '</td></tr>'
						);
					},
					success: function( data ) {
						if ( data.length != 0 ){
							max_num_pages = data[0].max_num_pages;
						}
						if ( max_num_pages == paged ){
							$( "li#next" ).hide();
						} else {
							$( "li#next" ).show();
						}
						if ( 1 == paged ){
							$( "li#prev" ).hide();
						} else {
							$( "li#prev" ).show();
						}
						$.each( data, function( i, val ) {
							$( ".requests-lists tr.lists-header" ).after( '<tr class="lists-data"><td align="center" scope="row">' + data[i].avatar + '</td><td class="leaveuservalue">' + data[i].leaveuservalue + '<br /><span><a href="' + data[i].editpostlink + '">Edit</a></span>&nbsp;&#124;<a href="' + data[i].permalink + '">View</a>&#124;&nbsp;<a href="' + data[i].deletepostlink + '">Delete</a></td><td>' + data[i].leavetype + '</td><td>' + data[i].leavestartdate + '</td><td>' + data[i].leaveenddate + '</td><td>' + data[i].poststatus + '</td><td>' + data[i].approver + '</td></tr>' );
						});
						if ( data.length === 0 ){
							$( ".requests-lists tr.lists-data" ).remove();
							$( "ul#pagination" ).remove();
							$( ".requests-lists tr.lists-header" ).after( '<tr class="lists-data"><td colspan="7" align="center" scope="row">No Leave Listing</td></tr>' );
							$( ".requests-lists #loading" ).remove();
							
						} else {
							$( ".requests-lists #loading" ).remove();
						}
					},
					error: function(jqXHR, textStatus, errorThrown) {
					    $( ".requests-lists #loading" ).remove();
						alert(jqXHR + " :: " + textStatus + " :: " + errorThrown);
					}
				});
			});
        }
	}
	rtHRMFrontend.init();
});