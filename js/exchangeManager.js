// JavaScript Document

function getUserActiveExchanges(currentUserNetId)
{
	document.getElementById("requestList").innerHTML = "";
	document.getElementById("offerList").innerHTML = "";
	
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
			var json = JSON.parse(xmlhttp.responseText);
			if (json.Exchanges.length>0) { 
				for (i=0; i<json.Exchanges.length; i++) { 
					var exchange = json.Exchanges[i];			
					// add requests and offers to the lists
					if (exchange.type == "Request")
					{
						document.getElementById("requestList").innerHTML = document.getElementById("requestList").innerHTML + '<li class="ui-widget-content" requestId="'+exchange.id+'">' + exchange.club + " " + exchange.passNum + " " +  exchange.passDate + "</li>";
					}
					else
					{
						document.getElementById("offerList").innerHTML = document.getElementById("offerList").innerHTML + '<li class="ui-widget-content" offerId="'+exchange.id+'">' + exchange.club + " " + exchange.passNum + " " +  exchange.passDate;
						
						var listOfRequests = "";
						var associatedExchanges = JSON.parse(exchange.associatedExchanges);
						for (j=0; j<associatedExchanges.length; j++)
						{
							// associatedExchanges[j] contains the netId of the user who made the request
							listOfRequests = listOfRequests + '<li class="requestSubListItem">Request From: ' + associatedExchanges[j] + ' <input type="submit" value="Decline" style="float:right" onMouseDown="declineRequest('+exchange.id+',\''+document.getElementById("netId").value+'\',\''+associatedExchanges[j]+'\')"><input type="submit" value="Accept" style="float:right" onMouseDown="acceptRequest('+exchange.id+',\''+document.getElementById("netId").value+'\',\''+associatedExchanges[j]+'\')"></li>';
						}						
						document.getElementById("offerList").innerHTML = document.getElementById("offerList").innerHTML + listOfRequests + "</ul></li>";
						
					}
				}
			} 
		}
	}
	
	xmlhttp.open("GET", "./php/userActiveExchanges.php?currentUserNetId=" + currentUserNetId, true);
	xmlhttp.send();
}




function getUserActiveTrades(currentUserNetId)
{
	document.getElementById("tradeList").innerHTML = "";
	
	if (window.XMLHttpRequest)
	{//  IE7+, Firefox, Chrome, Opera, Safari
	  xmlhttp1 = new XMLHttpRequest();
	}
	else
	{//  IE6, IE5
	  xmlhttp1 = new ActiveXObject("Microsoft.XMLHTTP");
	}
	  
	xmlhttp1.onreadystatechange=function()
	{
		if (xmlhttp1.readyState==4 && xmlhttp1.status==200)
		{
			var json = JSON.parse(xmlhttp1.responseText);
			if (json.Trades.length>0) { 
				for (i=0; i<json.Trades.length; i++) { 
					var trade = json.Trades[i];			
					// add requests and offers to the lists
					// check if the trade is based on the user's requets or offer
					if (trade.recipient == currentUserNetId)
					{
						document.getElementById("tradeList").innerHTML = document.getElementById("tradeList").innerHTML + '<div class="tradeDiv" offerId="'+trade.id+'"> *Trade with ' + trade.provider + " for " + trade.passNum + " pass(es) to " +  trade.club + "  Date: " + trade.passDate+ '<input type="submit" value="Chat" style="float:right" onMouseDown=""><input type="submit" value="Cancel" style="float:right" onMouseDown="cancelTrade(\''+currentUserNetId+'\',\''+trade.provider+'\',\''+trade.recipient+'\',\''+trade.offerId+'\',\''+trade.requestId+'\')"><input type="submit" value="Complete" style="float:right" onMouseDown="completeTrade(\''+currentUserNetId+'\',\''+trade.provider+'\',\''+trade.recipient+'\',\''+trade.offerId+'\',\''+trade.requestId+'\')"><div>';
					}
					else
					{
						document.getElementById("tradeList").innerHTML = document.getElementById("tradeList").innerHTML + '<div class="tradeDiv" offerId="'+trade.id+'"> Trade with ' + trade.recipient + " for " + trade.passNum + " pass(es) to " +  trade.club + "  Date: " + trade.passDate+ '<input type="submit" value="Chat" style="float:right" onMouseDown=""><input type="submit" value="Cancel" style="float:right" onMouseDown="cancelTrade(\''+currentUserNetId+'\',\''+trade.provider+'\',\''+trade.recipient+'\',\''+trade.offerId+'\',\''+trade.requestId+'\')"><input type="submit" value="Complete" style="float:right" onMouseDown="completeTrade(\''+currentUserNetId+'\',\''+trade.provider+'\',\''+trade.recipient+'\',\''+trade.offerId+'\',\''+trade.requestId+'\')"><div>';
					}
				}
			} 
		}
	}
	
	xmlhttp1.open("GET", "./php/userActiveTrades.php?currentUserNetId=" + currentUserNetId, true);
	xmlhttp1.send();
}


function completeTrade(currentUserNetId, provider, recipient, offerId, requestId)
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
			getUserActiveTrades(currentUserNetId);
			getUserActiveExchanges(currentUserNetId);
		}
	}
	
	xmlhttp.open("GET", "./php/completeTrade.php?currentUserNetId=" + currentUserNetId + "&provider=" + provider + "&recipient=" + recipient + "&offerId=" + offerId +"&requestId=" + requestId, true);
	xmlhttp.send();
}

function cancelTrade(currentUserNetId, provider, recipient, offerId, requestId)
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
			getUserActiveTrades(currentUserNetId);
			getUserActiveExchanges(currentUserNetId);
		}
	}
	
	xmlhttp.open("GET", "./php/cancelTrade.php?currentUserNetId=" + currentUserNetId + "&provider=" + provider + "&recipient=" + recipient + "&offerId=" + offerId +"&requestId=" + requestId, true);
	xmlhttp.send();
}


function removeSelectedOffers(currentUserNetId)
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
			getUserActiveTrades(currentUserNetId);
			getUserActiveExchanges(currentUserNetId);
		}
	}
	
	xmlhttp.open("POST", "./php/removeExchanges.php?currentUserNetId=" + currentUserNetId, true);
	xmlhttp.setRequestHeader( "Content-Type", "application/json" );
	xmlhttp.send(JSON.stringify(selectedOffers));
}
	
function removeSelectedRequests(currentUserNetId)
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
			getUserActiveTrades(currentUserNetId);
			getUserActiveExchanges(currentUserNetId);
		}
	}
	
	xmlhttp.open("POST", "./php/removeExchanges.php?currentUserNetId=" + currentUserNetId, true);
	xmlhttp.setRequestHeader( "Content-Type", "application/json" );
	xmlhttp.send(JSON.stringify(selectedRequests));
}

function declineRequest(offerId, currentUserNetId, requesterNetId)
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
			getUserActiveTrades(currentUserNetId);
			getUserActiveExchanges(currentUserNetId);
		}
	}
	
	xmlhttp.open("GET", "./php/declineRequest.php?requesterNetId=" + requesterNetId + "&currentUserNetId=" +currentUserNetId+ "&offerId=" + offerId, true);
	xmlhttp.send();
}

function acceptRequest(offerId, currentUserNetId, requesterNetId)
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
			getUserActiveTrades(currentUserNetId);
			getUserActiveExchanges(currentUserNetId);
		}
	}
	
	xmlhttp.open("GET", "./php/acceptRequest.php?requesterNetId=" + requesterNetId + "&currentUserNetId=" +currentUserNetId+ "&offerId=" + offerId, true);
	xmlhttp.send();
}
