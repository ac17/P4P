// JavaScript Document
var numPasses;
var selectedRequests = new Array();
var selectedOffers = new Array();

// Jquery ui funstions 
$(function() {
    $(function() {
    	$( "#tabs" ).tabs({
			activate: function (event, ui) {
				// upon enterting the tab which contains the map, refresh the map
				if (ui.newPanel.attr('id') == "tab-1")
				{
					getMatchingExchanges();
				}
				
				// upon enterting the tab which contains exchange manager, refresh the requests and offers
				if (ui.newPanel.attr('id') == "tab-2")
				{
					getUserActiveTrades(document.getElementById("netId").value);
					getUserActiveExchanges(document.getElementById("netId").value);
				}
			}			  
		});
 	});
	
	// functions for the elements of search form 
	$( "#searchPassDate" ).datepicker({
	  defaultDate: new Date(),
	  onSelect: function( dateText ) {
		getMatchingExchanges();
	  }
	 });
	
	$("#searchPassDate").val($.datepicker.formatDate("mm/dd/yy", new Date()));
	
	$( "#searchEatingClub" ).selectmenu({
	  change: function( event, data ) {
		  getMatchingExchanges();
	  }
	 });
	
	numPasses = $( "#numPasses" ).spinner({ 
	   stop: function( event, data ) {
		  if ($( "#numPasses" ).spinner( "isValid" ))
		  {
		      getMatchingExchanges();
		  }
		  else 
		  {
		      $( "#invalid-passNum-dialog" ).dialog( "open" );
			  $( "#numPasses" ).spinner( "value", 1 );
		  }
	   },
	   // defualt the number of passes to 1
	   create: function( event, ui ) {
		   $( "#numPasses" ).spinner( "value", 1 );
	   },
	   min: 1,
	   step: 1
	 });       

	$( "#range-slider" ).slider({
      range: "min",
      value: 37,
      min: 1,
      max: 700,
      slide: function( event, ui ) {
        $( "#amount" ).val( ui.value );
		showMarkersWithinRange( ui.value );
      }
    });
	
    $( "#amount" ).val( $( "#range-slider" ).slider( "value" ) );

	// functions for the elements of request/offer form 
	$( "#passDate" ).datepicker({
		defaultDate: new Date()
	});
	
	$("#passDate").val($.datepicker.formatDate("mm/dd/yy", new Date()));

	$( "#eatingClub" ).selectmenu();
	
	var spinner = $( "#spinner" ).spinner({
		stop: function( event, data ) {
		  if ($( "#spinner" ).spinner( "isValid" ))
		  {
			  getMatchingExchanges();
		  }
		  else 
		  {
			  $( "#invalid-passNum-dialog" ).dialog( "open" );
			  $( "#spinner" ).spinner( "value", 1 );
		  }
		},
		// defualt the number of passes to 1
		create: function( event, ui ) {
		   $( "#spinner" ).spinner( "value", 1 );
		},
		min: 1,
		step: 1
	}); 
				
	$( "#postExchange" )
		.button()
		.click(function( event ) {
			
			if (window.XMLHttpRequest)
			{//  IE7+, Firefox, Chrome, Opera, Safari
			  xmlhttp = new XMLHttpRequest();
			}
			else
			{//  IE6, IE5
			  xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
			}
			  
			xmlhttp.onreadystatechange=function()
			{
				if (xmlhttp.readyState==4 && xmlhttp.status==200)
				{
					getUserActiveExchanges(document.getElementById("netId").value);
					spinner.spinner( "value", 0 );
					document.getElementById("comment").value = "";
					$("#passDate").val($.datepicker.formatDate("mm/dd/yy", new Date()));
				}
			}

			xmlhttp.open("GET", "./php/addExchange.php?netId=" + document.getElementById("netId").value + "&passDate=" + $( "#passDate" ).val() + "&type=Offer" + "&numPasses=" + spinner.spinner( "value" ) + "&club=" + $('#eatingClub :selected').text() + "&comment=" + document.getElementById("comment").value, true);
			xmlhttp.send();
	});
	
	// current user's active offer and request lists 
	$( "#requestList" ).selectable({
		selecting: function( event, ui ) {
			selectedRequests = [];
		},
		selected: function( event, ui ) {
			selectedRequests.push([ui.selected.attributes.requestid.nodeValue,"Request"]);
		}
	});
	
	$( "#offerList" ).selectable({
		selecting: function( event, ui ) {
			selectedOffers = [];
		},
		selected: function( event, ui ) {
			selectedOffers.push([ui.selected.attributes.offerId.nodeValue,"Offer"]);
		}
	});
	
	$( "#transcationAccordion" ).accordion({
			heightStyle: "content",
		});
	
	// dialogs 
	$( "#invalid-passNum-dialog" ).dialog({
	  modal: true,
	  autoOpen: false,
	  buttons: {
		Ok: function() {
		  $( this ).dialog( "close" );
		}
	  }
	});
	
	$( "#error-dialog" ).dialog({
	  modal: true,
	  autoOpen: false,
	  buttons: {
		Ok: function() {
		  $( this ).dialog( "close" );
		}
	  }
	});
	
});

function showError(errorTitle, errorMsg)
{
	alert(errorTitle); 
	document.getElementById("error-dialog").setAttribute("title", errorTitle);
	document.getElementById("errorMessage").innerHTML = errorMsg;
	$( "#error-dialog" ).dialog( "open" );
}


function loadUserData(netId)
{
	getUserActiveTrades(netId);
	getUserActiveExchanges(netId);
}