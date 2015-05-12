// JavaScript Document

/* Functions which handel actions of the user interface. */

var numPasses;
var selectedRequests = new Array();
var selectedOffers = new Array();

// Jquery ui funstions 
$(function() {
	/* Function for the three main tabs. */
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
      value: 1,
      min: 0.1,
      max: 5,
	  step: 0.1,
      slide: function( event, ui ) {
		// Update the distance display 
		document.getElementById("amount").innerHTML = "Distance: " + ui.value + " mi";
      },
	  stop: function( event, ui ) {
		// Filter markers on map based on distance. */
	  	showMarkersWithinRange( ui.value );
	  }
    });
	
	/* Displays the current selected distance. */
	document.getElementById("amount").innerHTML = "Distance: " + $( "#range-slider" ).slider( "value" ) + " mi";

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
			  postExchange_xmlhttp = new XMLHttpRequest();
			}
			else
			{//  IE6, IE5
			  postExchange_xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
			}
			  
			postExchange_xmlhttp.onreadystatechange=function()
			{
				if (postExchange_xmlhttp.readyState==4 && postExchange_xmlhttp.status==200)
				{
					getUserActiveExchanges(document.getElementById("netId").value);
					spinner.spinner( "value", 0 );
					document.getElementById("comment").value = "";
					$("#passDate").val($.datepicker.formatDate("mm/dd/yy", new Date()));
				}
			}
			
			// Call script to create a new offer
			postExchange_xmlhttp.open("GET", "./php/addExchange.php?netId=" + document.getElementById("netId").value + "&passDate=" + $( "#passDate" ).val() + "&type=Offer" + "&numPasses=" + spinner.spinner( "value" ) + "&club=" + $('#eatingClub :selected').text() + "&comment=" + document.getElementById("comment").value, true);
			postExchange_xmlhttp.send();
	});
	
	// current user's active offer and request lists 
	$( "#requestList" ).selectable({
		filter: 'li',
		selecting: function( event, ui ) {
			// Clear the array of selected requests 
			selectedRequests = [];
		},
		selected: function( event, ui ) {
			// another selected request
			selectedRequests.push([ui.selected.attributes.requestid.nodeValue,"Request"]);
		}
	});
	
	$( "#offerList" ).selectable({
		filter: 'li',
		selecting: function( event, ui ) {
			// Clear the array of selected offers 
			selectedOffers = [];
		},
		selected: function( event, ui ) {
			// another selected offer
			selectedOffers.push([ui.selected.attributes.offerid.nodeValue,"Offer"]);
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

/* Function called to create and show a dialog box 
with an error message errorMsg. */ 
function showError(errorTitle, errorMsg)
{ 
	document.getElementById("error-dialog").setAttribute("title", errorTitle);
	document.getElementById("errorMessage").innerHTML = errorMsg;
	$( "#error-dialog" ).dialog( "open" );
}

/* Function used to load user data when the page is opened
and the body on load event occours. */
function loadUserData(netId)
{
	getUserActiveTrades(netId);
	getUserActiveExchanges(netId);
}


