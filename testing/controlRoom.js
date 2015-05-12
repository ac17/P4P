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
				
				if (ui.newPanel.attr('id') == "tab-2")
				{
					getServerStats();
				}

				// upon enterting the tab which contains exchange manager, refresh the requests and offers
				if (ui.newPanel.attr('id') == "tab-3")
				{
					getAllNPCs();
				}
				
				if (ui.newPanel.attr('id') == "tab-5")
				{
					getGarbageStats();
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
			  aixmlhttp = new XMLHttpRequest();
			}
			else
			{//  IE6, IE5
			  aixmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
			}
			  
			aixmlhttp.onreadystatechange=function()
			{
				if (aixmlhttp.readyState==4 && aixmlhttp.status==200)
				{
					alert("AI finished.");
					document.getElementById("output").innerHTML = aixmlhttp.responseText;
					
				}
			}

			var checkedActions = new Array(); 
			var inputElements = document.getElementsByClassName('actionCheckBox');
			for(var i=0; i < 5; ++i){
			  if(inputElements[i].checked){
				   checkedActions.push(inputElements[i].value);
			  }
			}
			
			aixmlhttp.open("POST", "./AI.php?hb=" + spinner.spinner( "value" ), true);
			aixmlhttp.setRequestHeader( "Content-Type", "application/json" );
			aixmlhttp.send(JSON.stringify(checkedActions));
			document.getElementById("output").innerHTML = "Running";
	});
		
	$( "#runAsyncAI" )
		.button()
		.click(function( event ) {
			var checkedActions = new Array(); 
			var inputElements = document.getElementsByClassName('actionCheckBox');
			for(var i=0; i < 5; ++i){
			  if(inputElements[i].checked){
				   checkedActions.push(inputElements[i].value);
			  }
			}
			
			window.open("./asyncAI.php?hb=" + spinner.spinner( "value" ) + "&actions="+JSON.stringify(checkedActions),'_blank');
	});	
	
	$( "#addNPC" )
		.button()
		.click(function( event ) {
			
			if (window.XMLHttpRequest)
			{//  IE7+, Firefox, Chrome, Opera, Safari
			  npc_xmlhttp = new XMLHttpRequest();
			}
			else
			{//  IE6, IE5
			  npc_xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
			}
			  
			npc_xmlhttp.onreadystatechange=function()
			{
				if (npc_xmlhttp.readyState==4 && npc_xmlhttp.status==200)
				{			
					getAllNPCs();
				}
			}

			npc_xmlhttp.open("GET", "./createUsers.php", true);
			npc_xmlhttp.send();
	});
		
	$( "#purgeNPCs" )
	.button()
	.click(function( event ) {
		
		if (window.XMLHttpRequest)
		{//  IE7+, Firefox, Chrome, Opera, Safari
		  purgeNPC_xmlhttp = new XMLHttpRequest();
		}
		else
		{//  IE6, IE5
		  purgeNPC_xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		}
		  
		purgeNPC_xmlhttp.onreadystatechange=function()
		{
			if (purgeNPC_xmlhttp.readyState==4 && purgeNPC_xmlhttp.status==200)
			{				
				getAllNPCs();
			}
		}

		purgeNPC_xmlhttp.open("GET", "./purgeAllNPCs.php", true);
		purgeNPC_xmlhttp.send();
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
}

/* Return data on all NPCs */
function getAllNPCs()
{
	if (window.XMLHttpRequest)
	{//  IE7+, Firefox, Chrome, Opera, Safari
	  getNPC_xmlhttp = new XMLHttpRequest();
	}
	else
	{//  IE6, IE5
	  getNPC_xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	  
	getNPC_xmlhttp.onreadystatechange=function()
	{
		if (getNPC_xmlhttp.readyState==4 && getNPC_xmlhttp.status==200)
		{
			document.getElementById("npcList").innerHTML = getNPC_xmlhttp.responseText;
		}
	}

	getNPC_xmlhttp.open("GET", "./getAllNPCs.php", true);
	getNPC_xmlhttp.send();
}

/* Delete specified NPC */
function deleteNPC(NPCid)
{
	if (window.XMLHttpRequest)
	{//  IE7+, Firefox, Chrome, Opera, Safari
	  delNPC_xmlhttp = new XMLHttpRequest();
	}
	else
	{//  IE6, IE5
	  delNPC_xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	  
	delNPC_xmlhttp.onreadystatechange=function()
	{
		if (delNPC_xmlhttp.readyState==4 && delNPC_xmlhttp.status==200)
		{
			getAllNPCs();
		}
	}

	delNPC_xmlhttp.open("GET", "./deleteNPC.php?npcId=" + NPCid, true);
	delNPC_xmlhttp.send();
}

/* Fetches server statics */
function getServerStats()
{
	if (window.XMLHttpRequest)
	{//  IE7+, Firefox, Chrome, Opera, Safari
	  stats_xmlhttp = new XMLHttpRequest();
	}
	else
	{//  IE6, IE5
	  stats_xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	  
	stats_xmlhttp.onreadystatechange=function()
	{
		if (stats_xmlhttp.readyState==4 && stats_xmlhttp.status==200)
		{
			document.getElementById("serverStats").innerHTML = stats_xmlhttp.responseText;
		}
	}

	stats_xmlhttp.open("GET", "./getServerStats.php", true);
	stats_xmlhttp.send();
}

/* Fetches statics on old chat messages and old offer/exhanges */
function getGarbageStats()
{
	if (window.XMLHttpRequest)
	{//  IE7+, Firefox, Chrome, Opera, Safari
	  garbage_xmlhttp = new XMLHttpRequest();
	}
	else
	{//  IE6, IE5
	  garbage_xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	  
	garbage_xmlhttp.onreadystatechange=function()
	{
		if (garbage_xmlhttp.readyState==4 && garbage_xmlhttp.status==200)
		{
			document.getElementById("garbageCollector").innerHTML = garbage_xmlhttp.responseText;
		}
	}

	garbage_xmlhttp.open("GET", "./getGarbage.php", true);
	garbage_xmlhttp.send();
}

/* Deletes all chat messages older than 10 days. */
function cleanChatMessages()
{
	if (window.XMLHttpRequest)
	{//  IE7+, Firefox, Chrome, Opera, Safari
	  clean_xmlhttp = new XMLHttpRequest();
	}
	else
	{//  IE6, IE5
	  clean_xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	  
	clean_xmlhttp.onreadystatechange=function()
	{
		if (clean_xmlhttp.readyState==4 && clean_xmlhttp.status==200)
		{
			if(clean_xmlhttp.responseText != "")
			{
				showError("A Small Problem...", clean_xmlhttp.responseText);
			}
			getGarbageStats();
		}
	}

	clean_xmlhttp.open("GET", "./cleanChatMessages.php", true);
	clean_xmlhttp.send();
}

/* Deletes all exhaneges older than today. */
function cleanExchanges()
{
	if (window.XMLHttpRequest)
	{//  IE7+, Firefox, Chrome, Opera, Safari
	  cleanExch_xmlhttp = new XMLHttpRequest();
	}
	else
	{//  IE6, IE5
	  cleanExch_xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	  
	cleanExch_xmlhttp.onreadystatechange=function()
	{
		if (cleanExch_xmlhttp.readyState==4 && cleanExch_xmlhttp.status==200)
		{
			if(cleanExch_xmlhttp.responseText != "")
			{
				showError("A Small Problem...", cleanExch_xmlhttp.responseText);
			}
			getGarbageStats();
		}
	}

	cleanExch_xmlhttp.open("GET", "./cleanExchanges.php", true);
	cleanExch_xmlhttp.send();
}