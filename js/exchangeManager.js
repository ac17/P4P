// JavaScript Document

function getUserActiveExchanges(netId)
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
	
	xmlhttp.open("GET", "./php/userActiveExchanges.php?netId=" + netId, true);
	xmlhttp.send();
}




function getUserActiveTrades(netId)
{
	document.getElementById("tradeList").innerHTML = "";
	
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
			if (json.Trades.length>0) { 
				for (i=0; i<json.Trades.length; i++) { 
					var trade = json.Trades[i];			
					// add requests and offers to the lists
					document.getElementById("tradeList").innerHTML = document.getElementById("tradeList").innerHTML + '<div class="tradeDiv" offerId="'+trade.id+'"> Trade with ' + trade.recipient + " for " + trade.passNum + " pass(es) to " +  trade.club + "  Date: " + trade.passDate+ '<input type="submit" value="Chat" style="float:right" onMouseDown=""><input type="submit" value="Cancel" style="float:right" onMouseDown=""><input type="submit" value="Complete" style="float:right" onMouseDown=""><div>';				
				}
			} 
		}
	}
	
	xmlhttp.open("GET", "./php/userActiveTrades.php?currentUserNetId=" + netId, true);
	xmlhttp.send();
}





function removeSelectedOffers(netId)
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
			alert(xmlhttp.responseText);
			getUserActiveExchanges(netId);
		}
	}
	
	xmlhttp.open("POST", "./php/removeExchanges.php?netId=" + netId, true);
	xmlhttp.setRequestHeader( "Content-Type", "application/json" );
	xmlhttp.send(JSON.stringify(selectedOffers));
}
	
function removeSelectedRequests(netId)
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
			getUserActiveExchanges(netId);
		}
	}
	
	xmlhttp.open("POST", "./php/removeExchanges.php?netId=" + netId, true);
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
	alert("A");
	
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
			alert(xmlhttp.responseText);
			getUserActiveTrades(currentUserNetId);
			getUserActiveExchanges(currentUserNetId);
		}
	}
	
	xmlhttp.open("GET", "./php/acceptRequest.php?requesterNetId=" + requesterNetId + "&currentUserNetId=" +currentUserNetId+ "&offerId=" + offerId, true);
	xmlhttp.send();
}
