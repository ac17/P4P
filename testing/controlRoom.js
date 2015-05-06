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
				
				if (ui.newPanel.attr('id') == "tab-6")
				{
					tweenToNewSpeed(controller.fullSpeed);
					setTimeout(stopWheel, 3000); 
				}
			}			  
		});
 	});
	      
	function stopWheel()
	{
		tweenToNewSpeed(0, 10000);
	}
	
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
	
	var scroller = $('#scroller div.innerScrollArea');
	var scrollerContent = scroller.children('ul');
	scrollerContent.children().clone().appendTo(scrollerContent);
	var curX = 0;
	scrollerContent.children().each(function(){
		var $this = $(this);
		$this.css('left', curX);
		curX += $this.outerWidth(true);
	});
	var fullW = curX / 2;
	var viewportW = scroller.width();

	// Scrolling speed management
	var controller = {curSpeed:0, fullSpeed:40};
	var $controller = $(controller);
	var tweenToNewSpeed = function(newSpeed, duration)
	{
		if (duration === undefined)
			duration = 100;
		$controller.stop(true).animate({curSpeed:newSpeed}, duration);
	};


	// Scrolling management; start the automatical scrolling
	var doScroll = function()
	{
		var curX = scroller.scrollLeft();
		var newX = curX + controller.curSpeed;
		if (newX > fullW*2 - viewportW)
			newX -= fullW;
		scroller.scrollLeft(newX);
	};
	setInterval(doScroll, 20);
	
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