// JavaScript Document
var numPasses;
var selectedRequests = new Array();
var selectedOffers = new Array();

// Jquery ui funstions 
$(function() {
    $(function() {
    	$( "#tabs" ).tabs();
 	});
	
	// functions for the elements of search form 
	$( "#searchPassDate" ).datepicker({
	  onSelect: function( dateText ) {
		getMatchingExchanges();
	  }
	 });
	
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

	// functions for the elements of request/offer form 
	$( "#passDate" ).datepicker();
	
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
					$( "#passDate" ).val("");
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
	
	$(function() {
    	$( "#transcationAccordion" ).accordion({
			heightStyle: "content",
		});
  	});
	
	// dialogs 
	$(function() {
		$( "#invalid-passNum-dialog" ).dialog({
		  modal: true,
		  autoOpen: false,
		  buttons: {
			Ok: function() {
			  $( this ).dialog( "close" );
			}
		  }
		});
	});
});

function loadUserData(netId)
{
	getUserActiveTrades(netId);
	getUserActiveExchanges(netId);
}