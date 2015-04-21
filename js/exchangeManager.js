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
						document.getElementById("offerList").innerHTML = document.getElementById("offerList").innerHTML + '<li class="ui-widget-content" offerId="'+exchange.id+'">' + exchange.club + " " + exchange.passNum + " " +  exchange.passDate + "</li>";
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