<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>

 	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
    <script src="//code.jquery.com/jquery-1.10.2.js"></script>
    <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
    <script>
	var selectedRequests = new Array();
	var selectedOffers = new Array();
		
    $(function() {
        $( "#passDate" ).datepicker();
        
        $( "#eatingClub" ).selectmenu();
        
        var spinner = $( "#spinner" ).spinner({ min: 0 }); 
		
		$( "#radio" ).buttonset();
		
    	$( "#requestList" ).selectable({
			selecting: function( event, ui ) {
				selectedRequests = [];
			},
			selected: function( event, ui ) {
				selectedRequests.push(ui.selected.attributes.requestid.nodeValue);
			}
		});
		
		$( "#offerList" ).selectable({
			selecting: function( event, ui ) {
				selectedOffers = [];
			},
			selected: function( event, ui ) {
				selectedOffers.push(ui.selected.attributes.offerId.nodeValue);
			}
		});
					
    	$( "#submit" )
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
						getUserActiveExchanges();
						spinner.spinner( "value", 0 );
						document.getElementById("comment").value = "";
						$( "#passDate" ).val("");
					}
				}

				xmlhttp.open("GET", "./php/addExchange.php?netId=" + document.getElementById("NetID").value + "&passDate=" + $( "#passDate" ).val() + "&type=" + $("#radio :radio:checked + label").text() + "&numPasses=" + spinner.spinner( "value" ) + "&club=" + $('#eatingClub :selected').text() + "&comment=" + document.getElementById("comment").value, true);
				xmlhttp.send();
      	});
		
    });
    </script>
    
    <style>
		.feedback { font-size: 1.4em; }
		.selectable .ui-selecting { background: #FECA40; }
		.selectable .ui-selected { background: #F39814; color: white; }
		.selectable { list-style-type: none; margin: 0; padding: 0; width: 60%; }
		.selectable li { margin: 3px; padding: 0.4em; font-size: 1.4em; height: 18px; }
	</style>
  
    <style>
		fieldset {
		  border: 0;
		}
		label {
		  display: block;
		}
		select {
		  width: 200px;
		}
		.overflow {
		  height: 200px;
		}
    </style>  
</head>

<body>

NetID: <br /><input type="text" id="NetID" onKeyUp="getUserActiveExchanges()"><br /><br />

<table style="border:thick; border:solid">
<tr>
<td style="border:thick; border:solid; padding:10px; vertical-align:middle;">
    Pass Date: <br /><input type="text" id="passDate"><br /><br />
</td>
<td style="border:thick; border:solid; padding:10px; vertical-align:middle;">
    <div id="radio">
        <input type="radio" id="radio1" name="radio" checked="checked"><label for="radio1">Request</label>
        <input type="radio" id="radio2" name="radio"><label for="radio2">Offer</label>
  	</div>
</td>
<td style="border:thick; border:solid; padding:10px; vertical-align:middle;">
	<label for="spinner">Number of Passes:</label><br />
	<input id="spinner" name="value">
</td>
<td style="border:thick; border:solid; padding:10px; vertical-align:middle;">
    <form>
      <fieldset>
        <label for="eatingClub">Eating Club: </label><br />
        <select name="eatingClub" id="eatingClub">
          <option>Ivy Club</option>
          <option>Tiger Inn</option>
          <option selected="selected">Colonial</option>
          <option>Cottage</option>
          <option>Cap & Gown</option>
          <option>Tiger Inn</option>
        </select>
        </fieldset>
    </form>
</td>
<td style="border:thick; border:solid; padding:10px; vertical-align:middle;">
	Comment:
    <textarea id="comment" rows="5" cols="20"></textarea>
</td>
<td style="border:thick; border:solid; padding:10px; vertical-align:middle;">
	<input type="submit" value="Post" id="submit">
</td>
</tr>
</table>

<br  />
Your Current Requests
<ol id="requestList" class="selectable">
</ol>
<input type="submit" value="Delete Selected Requests" onmousedown="removeSelectedRequests()">
<br  />
Your Current Offers
<ol id="offerList" class="selectable">
</ol>
<input type="submit" value="Delete Selected Offers" onmousedown="removeSelectedOffers()">
</body>

<script>
function getUserActiveExchanges()
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
	
	xmlhttp.open("GET", "./php/userActiveExchanges.php?netId=" + document.getElementById("NetID").value, true);
	xmlhttp.send();
}

	function removeSelectedOffers()
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
				getUserActiveExchanges();
			}
		}
		
		xmlhttp.open("POST", "./php/removeExchanges.php?netId=" + document.getElementById("NetID").value, true);
		xmlhttp.setRequestHeader( "Content-Type", "application/json" );
		xmlhttp.send(JSON.stringify(selectedOffers));
	}
	
	function removeSelectedRequests()
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
				getUserActiveExchanges();
			}
		}
		
		xmlhttp.open("POST", "./php/removeExchanges.php?netId=" + document.getElementById("NetID").value, true);
		xmlhttp.setRequestHeader( "Content-Type", "application/json" );
		xmlhttp.send(JSON.stringify(selectedRequests));
	}
</script>
</html>