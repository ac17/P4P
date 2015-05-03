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
					getAllExchanges();
				}
				
				// upon enterting the tab which contains exchange manager, refresh the requests and offers
				if (ui.newPanel.attr('id') == "tab-2")
				{
					getAllNPCs();
				}
			}			  
		});
 	});
	      

	// functions for the elements of request/offer form 	
	var spinner = $( "#spinner" ).spinner({
		stop: function( event, data ) {
		  if ($( "#spinner" ).spinner( "isValid" ))
		  {
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
				
	$( "#runAI" )
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
					alert("AI finished.");
					document.getElementById("output").innerHTML = xmlhttp.responseText;
					
				}
			}

			xmlhttp.open("GET", "./AI.php?hb=" + spinner.spinner( "value" ), true);
			xmlhttp.send();
			document.getElementById("output").innerHTML = "Running";
	});
		
	$( "#runAsyncAI" )
		.button()
		.click(function( event ) {
			window.open("./asyncAI.php?hb=" + spinner.spinner( "value" ),'_blank');
	});	
	
	$( "#addNPC" )
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
					getAllNPCs();
				}
			}

			xmlhttp.open("GET", "./createUsers.php", true);
			xmlhttp.send();
	});
		
	$( "#purgeNPCs" )
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
				getAllNPCs();
			}
		}

		xmlhttp.open("GET", "./purgeAllNPCs.php", true);
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
	setInterval(getAllExchanges, 3000);
	getAllNPCs();
}

function getAllNPCs()
{
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
			document.getElementById("npcList").innerHTML = xmlhttp.responseText;
		}
	}

	xmlhttp.open("GET", "./getAllNPCs.php", true);
	xmlhttp.send();
}