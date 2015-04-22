// JavaScript Document
var map;
var markers = [];
var infoWindows = [];

function initialize() {
	var mapOptions = {
	  center: { lat: 40.348374, lng: -74.652918},
	  zoom: 17
	};
	map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
}

google.maps.event.addDomListener(window, 'load', initialize);

function getMatchingExchanges()
{
	deleteMarkers();
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
			if (json.Users.length>0) {
				var i;
				for (i=0; i<json.Users.length; i++) { 
					var user = json.Users[i];
					addUserToMap(user);
				}  
			} 
		}
	}
	
	xmlhttp.open("GET", "./php/searchExchangesUserSpecific.php?date=" + $( "#searchPassDate" ).val() + "&type=Offer" + "&numPasses=" + numPasses.spinner( "value" ) + "&club=" + $('#searchEatingClub :selected').text() + "&netId=" + document.getElementById("netId").value, true);
	xmlhttp.send();
}

function addUserToMap(user) {
	var contentString = '<div class="infoWinContent">'+
      '<h4 class="infoWinHeading">'+
	  user.name +
	  '</h4>'+
      '<div class="infoWinbodyContent">';

      // link for chatting
      contentString = contentString + 
      '<div id = "chatlink"><a href = "./php/chat.php?offerer=' + user.netid + '">Click here to chat</a><div>';

      //offers
	  var i;
	  for (i=0; i<user.exchanges.length; i++) {
		var exchange = user.exchanges[i];
		// disable pursueOffer is the offer has already been requested
		if (exchange.requested == 0)
		{
		contentString = contentString + '<div id="'+exchange.id+'" class="offerDiv" onclick="pursueOffer('+exchange.id+')">';
		}
		else
		{
		contentString = contentString + '<div id="'+exchange.id+'" class="selectedOfferDiv" ">';
		}
		
		contentString = contentString +
		'<h4>' + exchange.club + '</h4>'+
		'<div> # Passes:  '+ exchange.passNum + '<br />' + exchange.comment +
		'</div>'+		
		'</div>';
	  } 
      contentString = contentString +
	  	'</div>' +
      '</div>';
	  
	var myLatlng = new google.maps.LatLng(user.lat, user.lng);
	
	var infowindow = new google.maps.InfoWindow({
      content: contentString
  	});
	
	var marker = new google.maps.Marker({
	  position: myLatlng,
	  map: map,
	  title: location.name
	});
	
	google.maps.event.addListener(marker, 'click', function() {
    	infowindow.open(map,marker);
  	});
	
	markers.push(marker);
	infoWindows.push(infowindow);
}

function pursueOffer(offerId) {
	// show the offer as selected
	document.getElementById(offerId).className = "selectedOfferDiv";
	
	//disable persuing the offer again 
	document.getElementById(offerId).onclick = "";
	
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
				}
			}

			xmlhttp.open("GET", "./php/pursueOffer.php?netId=" + document.getElementById("netId").value + "&offerId=" + offerId, true);
			xmlhttp.send();
}

// Sets the map on all markers in the array.
function setAllMap(map) {
  for (var i = 0; i < markers.length; i++) {
    markers[i].setMap(map);
  }
}

// Removes the markers from the map, but keeps them in the array.
function clearMarkers() {
  setAllMap(null);
}

// Shows any markers currently in the array.
function showMarkers() {
  setAllMap(map);
}

// Deletes all markers in the array by removing references to them.
function deleteMarkers() {
  clearMarkers();
  markers = [];
  infoWindows = [];
}