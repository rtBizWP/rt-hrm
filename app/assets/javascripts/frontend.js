jQuery(document).ready(function($) {

	var rtHRMFrontend = {
		/**
		 *
		 */
		init : function(){
            rtHRMFrontend.leaveListing();
			//rtHRMFrontend.requestsListing();
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
			$( ".order" ).change(function() {
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
						$( ".lists #loading" ).remove();
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
						max_num_pages = data[0].max_num_pages;
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
						$( ".lists #loading" ).remove();
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
						max_num_pages = data[0].max_num_pages;
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
						$( ".lists #loading" ).remove();
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
			$( ".order" ).change(function() {
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
						$( ".lists #loading" ).remove();
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
						max_num_pages = data[0].max_num_pages;
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
						$( ".lists #loading" ).remove();
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
						max_num_pages = data[0].max_num_pages;
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
						$( ".lists #loading" ).remove();
					}
				});
			});
        }
	}
	rtHRMFrontend.init();
});