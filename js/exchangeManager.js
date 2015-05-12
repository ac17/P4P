// JavaScript Document

/* Functions which handel actions performed on trades, offer and requests. 
These include fetching lists and accepting, completing and rejecting trades, offers, and requests. */

/* Function to get all the active requests and offers of the currentUserNetId user. */
function getUserActiveExchanges(currentUserNetId)
{
	// Clear the lists of offers and requests 
	document.getElementById("requestList").innerHTML = "";
	document.getElementById("offerList").innerHTML = "";
	
	if (window.XMLHttpRequest)
	{//  IE7+, Firefox, Chrome, Opera, Safari
	  getUserActiveExchanges_xmlhttp = new XMLHttpRequest();
	}
	else
	{//  IE6, IE5
	  getUserActiveExchanges_xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	  
	getUserActiveExchanges_xmlhttp.onreadystatechange=function()
	{
		// On sucessful AJAX return 
		if (getUserActiveExchanges_xmlhttp.readyState==4 && getUserActiveExchanges_xmlhttp.status==200)
		{			
			var json;
			
			// If an output is returened try to create an array based on it, or how the error which was returened 
			try 
			{
				json = JSON.parse(getUserActiveExchanges_xmlhttp.responseText);
			}
			catch(err)
			{
				showError("A Small Problem...", getUserActiveExchanges_xmlhttp.responseText);
			}
			
			if (json.Exchanges.length>0) { 
				for (i=0; i<json.Exchanges.length; i++) { 
					var exchange = json.Exchanges[i];
					var passNoun = "pass";
					// add requests and offers to the lists
					if (exchange.type == "Request")
					{
						if (exchange.passNum > 1)
						{
							passNoun = "passes";
						}
						document.getElementById("requestList").innerHTML = document.getElementById("requestList").innerHTML + '<li class="ui-widget-content col-md-12" requestid="'+exchange.id+'"><div class="col-md-4 tableCell">' + exchange.club + '</div><div class="col-md-4 tableCell">' + exchange.passNum + " " + passNoun + '</div><div class="col-md-4 tableCell">' +  exchange.passDate + "</div></li>";
					}
					else
					{
						if (exchange.passNum > 1)
						{
							passNoun = "passes";
						}
						document.getElementById("offerList").innerHTML = document.getElementById("offerList").innerHTML + '<li class="ui-widget-content col-md-12" offerid="'+exchange.id+'"><div class="col-md-12" ><div class="col-md-4 tableCell">' + exchange.club + '</div><div class="col-md-4 tableCell">' + exchange.passNum + ' ' + passNoun + '</div><div class="col-md-4 tableCell">' +  exchange.passDate + '</div></div>';
						// add all requests for the current offer from other users 
						var listOfRequests = "";
						var associatedExchanges = JSON.parse(exchange.associatedExchanges);
						for (j=0; j<associatedExchanges.length; j++)
						{
							// associatedExchanges[j] contains the netId of the user who made the request
							listOfRequests = listOfRequests + '<div class="requestSubListItem col-md-12">Request From: ' + exchange.names[j] + ' <input type="submit" value="Chat" style="float:right" onMouseDown="register_popup(\''+associatedExchanges[j]+'\', \''+exchange.names[j]+ '\')"><input type="submit" value="Decline" style="float:right" onMouseDown="declineRequest('+exchange.id+',\''+document.getElementById("netId").value+'\',\''+associatedExchanges[j]+'\')"><input type="submit" value="Accept" style="float:right" onMouseDown="acceptRequest('+exchange.id+',\''+document.getElementById("netId").value+'\',\''+associatedExchanges[j]+'\')"></div>';
						}						
						document.getElementById("offerList").innerHTML = document.getElementById("offerList").innerHTML + listOfRequests + "</li>";
						
					}
				}
			} 
		}
	}
	
	// Request offer and request lists
	getUserActiveExchanges_xmlhttp.open("GET", "./php/userActiveExchanges.php?currentUserNetId=" + currentUserNetId, true);
	getUserActiveExchanges_xmlhttp.send();
}

/* Function to get the trades of user with netId currentUserNetId */
function getUserActiveTrades(currentUserNetId)
{
	// Clear the trade list 
	document.getElementById("tradeList").innerHTML = "";
	
	if (window.XMLHttpRequest)
	{//  IE7+, Firefox, Chrome, Opera, Safari
	  getUserActiveTrades_xmlhttp = new XMLHttpRequest();
	}
	else
	{//  IE6, IE5
	  getUserActiveTrades_xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	  
	getUserActiveTrades_xmlhttp.onreadystatechange=function()
	{
		// On sucessful AJAX return 
		if (getUserActiveTrades_xmlhttp.readyState==4 && getUserActiveTrades_xmlhttp.status==200)
		{
			var json;
			
			// Try to create an array based on the returned data. 
			try 
			{
				json = JSON.parse(getUserActiveTrades_xmlhttp.responseText);
			}
			catch(err)
			{
				showError("A Small Problem...", "Could not get the list of trades.");
			}
			
			if (json.Trades.length>0) { 
				for (i=0; i<json.Trades.length; i++) { 
					var trade = json.Trades[i];	
					var passNoun = "pass"; 
					// add trades to the list
					// check if the trade is based on the user's requets or offer
					if (trade.recipient == currentUserNetId)
					{
						if (trade.passNum > 1)
						{
							passNoun = "passes";
						}
						
						document.getElementById("tradeList").innerHTML = document.getElementById("tradeList").innerHTML + '<div class="tradeDiv" offerId="'+trade.id+'"> *Trade with ' + trade.providerName + " for " + trade.passNum + " " + passNoun + " to " +  trade.club + "  Date: " + trade.passDate+ '<input type="submit" value="Chat" style="float:right" onMouseDown="register_popup(\''+trade.provider+'\', \''+trade.providerName+ '\')"><input type="submit" value="Cancel" style="float:right" onMouseDown="cancelTrade(\''+currentUserNetId+'\',\''+trade.provider+'\',\''+trade.recipient+'\',\''+trade.offerId+'\',\''+trade.requestId+'\')"><input type="submit" value="Complete" style="float:right" onMouseDown="completeTrade(\''+currentUserNetId+'\',\''+trade.provider+'\',\''+trade.recipient+'\',\''+trade.offerId+'\',\''+trade.requestId+'\')"><div>';
					}
					else
					{
						document.getElementById("tradeList").innerHTML = document.getElementById("tradeList").innerHTML + '<div class="tradeDiv" offerId="'+trade.id+'"> Trade with ' + trade.recipientName + " for " + trade.passNum + " " + passNoun + " to " +  trade.club + "  Date: " + trade.passDate+ '<input type="submit" value="Chat" style="float:right" onMouseDown="register_popup(\''+trade.recipient+'\', \''+trade.recipientName+ '\')"><input type="submit" value="Cancel" style="float:right" onMouseDown="cancelTrade(\''+currentUserNetId+'\',\''+trade.provider+'\',\''+trade.recipient+'\',\''+trade.offerId+'\',\''+trade.requestId+'\')"><input type="submit" value="Complete" style="float:right" onMouseDown="completeTrade(\''+currentUserNetId+'\',\''+trade.provider+'\',\''+trade.recipient+'\',\''+trade.offerId+'\',\''+trade.requestId+'\')"><div>';
					}
				}
			} 
		}
	}
	
	// Request the trade list
	getUserActiveTrades_xmlhttp.open("GET", "./php/userActiveTrades.php?currentUserNetId=" + currentUserNetId, true);
	getUserActiveTrades_xmlhttp.send();
}

/* Function for completing a trade of provider with recipient based on offer and request with offerId and requestId. */
function completeTrade(currentUserNetId, provider, recipient, offerId, requestId)
{
	if (window.XMLHttpRequest)
	{//  IE7+, Firefox, Chrome, Opera, Safari
	  completeTrade_xmlhttp = new XMLHttpRequest();
	}
	else
	{//  IE6, IE5
	  completeTrade_xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	  
	completeTrade_xmlhttp.onreadystatechange=function()
	{
		// On sucessful AJAX return 
		if (completeTrade_xmlhttp.readyState==4 && completeTrade_xmlhttp.status==200)
		{
			// unless an error message is returned, updatethe trade and exchange lists
			if(completeTrade_xmlhttp.responseText != "")
			{
				showError("A Small Problem...", completeTrade_xmlhttp.responseText);
			}
			
			getUserActiveTrades(currentUserNetId);
			getUserActiveExchanges(currentUserNetId);
		}
	}
	
	// Call the script which completes the trade 
	completeTrade_xmlhttp.open("GET", "./php/completeTrade.php?currentUserNetId=" + currentUserNetId + "&provider=" + provider + "&recipient=" + recipient + "&offerId=" + offerId +"&requestId=" + requestId, true);
	completeTrade_xmlhttp.send();
}

/* Function for cancelling a trade. */
function cancelTrade(currentUserNetId, provider, recipient, offerId, requestId)
{
	if (window.XMLHttpRequest)
	{//  IE7+, Firefox, Chrome, Opera, Safari
	  cancelTrade_xmlhttp = new XMLHttpRequest();
	}
	else
	{//  IE6, IE5
	  cancelTrade_xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	  
	cancelTrade_xmlhttp.onreadystatechange=function()
	{
		// On sucessful AJAX return 
		if (cancelTrade_xmlhttp.readyState==4 && cancelTrade_xmlhttp.status==200)
		{
			// Update the trade and exchanges lists
			getUserActiveTrades(currentUserNetId);
			getUserActiveExchanges(currentUserNetId);
		}
	}
	
	// Call the script which cancels the trade
	cancelTrade_xmlhttp.open("GET", "./php/cancelTrade.php?currentUserNetId=" + currentUserNetId + "&provider=" + provider + "&recipient=" + recipient + "&offerId=" + offerId +"&requestId=" + requestId, true);
	cancelTrade_xmlhttp.send();
}

/* Function for deleting offers selected in the list */
function removeSelectedOffers(currentUserNetId)
{	
	if (window.XMLHttpRequest)
	{//  IE7+, Firefox, Chrome, Opera, Safari
	  removeSelectedOffers_xmlhttp = new XMLHttpRequest();
	}
	else
	{//  IE6, IE5
	  removeSelectedOffers_xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	  
	removeSelectedOffers_xmlhttp.onreadystatechange=function()
	{
		// On sucessful AJAX return 
		if (removeSelectedOffers_xmlhttp.readyState==4 && removeSelectedOffers_xmlhttp.status==200)
		{
			// Update the trade and exchanges lists
			getUserActiveTrades(currentUserNetId);
			getUserActiveExchanges(currentUserNetId);
		}
	}
	
	// Call the script to delete the requests 
	removeSelectedOffers_xmlhttp.open("POST", "./php/removeExchanges.php?currentUserNetId=" + currentUserNetId, true);
	removeSelectedOffers_xmlhttp.setRequestHeader( "Content-Type", "application/json" );
	// Pass the array of request ids of requests to delete
	removeSelectedOffers_xmlhttp.send(JSON.stringify(selectedOffers));
}

/* Function for deleting requests selected in the list */
function removeSelectedRequests(currentUserNetId)
{	
	if (window.XMLHttpRequest)
	{//  IE7+, Firefox, Chrome, Opera, Safari
	  removeSelectedRequests_xmlhttp = new XMLHttpRequest();
	}
	else
	{//  IE6, IE5
	  removeSelectedRequests_xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	  
	removeSelectedRequests_xmlhttp.onreadystatechange=function()
	{
		// On sucessful AJAX return 
		if (removeSelectedRequests_xmlhttp.readyState==4 && removeSelectedRequests_xmlhttp.status==200)
		{
			// Update the trade and exchanges lists
			getUserActiveTrades(currentUserNetId);
			getUserActiveExchanges(currentUserNetId);
		}
	}
	
	// Call the script to delete the offer 
	removeSelectedRequests_xmlhttp.open("POST", "./php/removeExchanges.php?currentUserNetId=" + currentUserNetId, true);
	removeSelectedRequests_xmlhttp.setRequestHeader( "Content-Type", "application/json" );
	// Pass the array of offer ids of offers to delete
	removeSelectedRequests_xmlhttp.send(JSON.stringify(selectedRequests));
}

/* Function to decline a request for offer with offerId requested by requesterNetId.*/
function declineRequest(offerId, currentUserNetId, requesterNetId)
{	

	if (window.XMLHttpRequest)
	{//  IE7+, Firefox, Chrome, Opera, Safari
	  declineRequest_xmlhttp = new XMLHttpRequest();
	}
	else
	{//  IE6, IE5
	  declineRequest_xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	  
	declineRequest_xmlhttp.onreadystatechange=function()
	{
		// On sucessful AJAX return 
		if (declineRequest_xmlhttp.readyState==4 && declineRequest_xmlhttp.status==200)
		{
			// Update the trade and exchanges lists
			getUserActiveTrades(currentUserNetId);
			getUserActiveExchanges(currentUserNetId);
		}
	}
	
	// Call the script which declines a request 
	declineRequest_xmlhttp.open("GET", "./php/declineRequest.php?requesterNetId=" + requesterNetId + "&currentUserNetId=" +currentUserNetId+ "&offerId=" + offerId, true);
	declineRequest_xmlhttp.send();
}

/* Function to accept a request for offer with offerId requested by requesterNetId.*/
function acceptRequest(offerId, currentUserNetId, requesterNetId)
{
	
	if (window.XMLHttpRequest)
	{//  IE7+, Firefox, Chrome, Opera, Safari
	  acceptRequest_xmlhttp = new XMLHttpRequest();
	}
	else
	{//  IE6, IE5
	  acceptRequest_xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	  
	acceptRequest_xmlhttp.onreadystatechange=function()
	{
		// On sucessful AJAX return 
		if (acceptRequest_xmlhttp.readyState==4 && acceptRequest_xmlhttp.status==200)
		{
			// Check for an error message
			if(acceptRequest_xmlhttp.responseText != "")
			{
				showError("A Small Problem...", acceptRequest_xmlhttp.responseText);
			}
			
			// Update the trade and exchanges lists
			getUserActiveTrades(currentUserNetId);
			getUserActiveExchanges(currentUserNetId);
		}
	}
	
	// Call the script which accepts a request 
	acceptRequest_xmlhttp.open("GET", "./php/acceptRequest.php?requesterNetId=" + requesterNetId + "&currentUserNetId=" +currentUserNetId+ "&offerId=" + offerId, true);
	acceptRequest_xmlhttp.send();
}
