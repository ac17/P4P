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
							listOfRequests = listOfRequests + '<li class="requestSubListItem">Request From: ' + associatedExchanges[j] + ' <input type="submit" value="Decline" style="float:right" onMouseDown="declineRequest('+exchange.id+','+document.getElementById("netId").value+','+associatedExchanges[j]+')"><input type="submit" value="Accept" style="float:right" onMouseDown="acceptRequest('+exchange.id+','+associatedExchanges[j]+')"></li>';
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

declineRequest(requestId, currentUserNetId, requesterNetId)
{	
alert('a');

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
		}
	}
	
	xmlhttp.open("GET", "./php/declineRequest.php?requesterNetId=" + requesterNetId + "&currentUserNetId=" +currentUserNetId+ "requestId&=" + requestId, true);
	xmlhttp.send();
}

acceptRequest(requestId, requesterNetId)
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
		}
	}
	
	xmlhttp.open("GET", "./php/acceptRequest.php?netId=" + requesterNetId + "requestId&=" + requestId, true);
	xmlhttp.send();
}
