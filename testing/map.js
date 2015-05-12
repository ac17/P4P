// JavaScript Document

/* Functions which handel actions performed on the map. */

var map;
var markers = [];
var infoWindows = [];

/* Function to initialize the map. */

function initialize() {
	var mapOptions = {
	  center: { lat: 40.348374, lng: -74.652918},
	  zoom: 17
	};
	map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
		
}

/* Initialize the map when the page loads. */
google.maps.event.addDomListener(window, 'load', initialize);

/* Function to load all offers which match the query specified by the user interface */
function getAllExchanges()
{
	// Clear the map of all markers.
	deleteMarkers();
	if (window.XMLHttpRequest)
	{//  IE7+, Firefox, Chrome, Opera, Safari
	  mapxmlhttp = new XMLHttpRequest();
	}
	else
	{//  IE6, IE5
	  mapxmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	
	// On sucessful AJAX return 
	mapxmlhttp.onreadystatechange=function()
	{
		if (mapxmlhttp.readyState==4 && mapxmlhttp.status==200)
		{			
			var json = JSON.parse(mapxmlhttp.responseText);
			if (json.Users.length>0) {
				var i;
				for (i=0; i<json.Users.length; i++) { 
					var user = json.Users[i];
					addUserToMap(user);
				} 
			}
		}
	}
	
	// Get all exchanges to display them on the map
	mapxmlhttp.open("GET", "../php/getAllExchanges.php", true);
	mapxmlhttp.send();
}

/* Function to get all exchanges and display them on map.
Used for the control room map. */
function addUserToMap(user) {
	var contentString = '<div class="infoWinContent">'+
      '<h4 class="infoWinHeading">'+
	  user.name +
	  '</h4>'+
      '<div class="infoWinbodyContent">';
	  
      //offers
	  var i;
	  for (i=0; i<user.exchanges.length; i++) {
		var exchange = user.exchanges[i];
		// disable pursueOffer is the offer has already been requested

		contentString = contentString + '<div id="'+exchange.id+'" class="offerDiv"">';

		contentString = contentString +
		'<h4>' + exchange.club + '</h4>'+
		'<div> # Passes:  '+ exchange.passNum + '<br />' + exchange.passDate + '<br />' + exchange.comment +
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
	  title: location.name,
	  icon: '../img/ticket.png'
	});
	
	google.maps.event.addListener(marker, 'click', function() {
    	infowindow.open(map,marker);
  	});
	
	markers.push(marker);
	infoWindows.push(infowindow);
}

/* Function to pursue an offer with offerID */
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
					if(xmlhttp.responseText != "")
					{
						showError("A Small Problem...", xmlhttp.responseText);
						getMatchingExchanges();
					}
				}
			}

			xmlhttp.open("GET", "./php/pursueOffer.php?netId=" + document.getElementById("netId").value + "&offerId=" + offerId, true);
			xmlhttp.send();
}

/* Below helper functions are based on code from:
https://developers.google.com/maps/documentation/javascript/examples/marker-remove */ 

/* Function to show all markers on the map */
function setAllMap(map) {
  for (var i = 0; i < markers.length; i++) {
    markers[i].setMap(map);
  }
}

/* Function to clear the markers from the map */
function clearMarkers() {
  setAllMap(null); 
  for (var i = 0; i < markers.length; i++) {
    markers[i].setVisible(false);
  }
}

/* Function to shows markers the array.*/
function showMarkers() {
  setAllMap(map);
  for (var i = 0; i < markers.length; i++) {
    markers[i].setVisible(true);
  }
}

/* Function to delete all markers in the array*/
function deleteMarkers() {
  clearMarkers();
  markers = [];
  infoWindows = [];
}

/* Function to change map zoom and center to show all markers. 
Based on code from: 
http://stackoverflow.com/questions/19304574/center-set-zoom-of-map-to-cover-all-markers-visible-markers */
function updateMapToShowAllMarkers()
{
	var bounds = new google.maps.LatLngBounds();
	for(i=0;i<markers.length;i++) {
	   bounds.extend(markers[i].getPosition());
	}
	
	//center the map to the geometric center of all markers
	map.setCenter(bounds.getCenter());
	
	map.fitBounds(bounds);
	
	//remove one zoom level to ensure no marker is on the edge.
	map.setZoom(map.getZoom()-1); 
	
	// set a minimum zoom 
	// if you got only 1 marker or all markers are on the same address map will be zoomed too much.
	if(map.getZoom()> 15){
	  map.setZoom(15);
	}
}

/* Function to get the users current location and upload it to the database */
function shareCurrentLocation(currentUserNetId)
{
	// Try W3C Geolocation (Preferred)
	if(navigator.geolocation) {
	browserSupportFlag = true;
	navigator.geolocation.getCurrentPosition(function(position) {
	  initialLocation = new google.maps.LatLng(position.coords.latitude,position.coords.longitude);
	  map.setCenter(initialLocation);
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
				map.setCenter(position);
			}
		}
		
		xmlhttp.open("GET", "./php/updateLocation.php?currentUserNetId="+currentUserNetId+"&lat="+position.coords.latitude+"&lng="+position.coords.longitude, true);
		xmlhttp.send();	
		
	}, function() {
	  handleNoGeolocation(browserSupportFlag);
	});
	}
	// Browser doesn't support Geolocation
	else {
	browserSupportFlag = false;
	handleNoGeolocation(browserSupportFlag);
	}
	
	function handleNoGeolocation(errorFlag) {
	if (errorFlag == true) {
	  alert("Geolocation service failed.");
	  initialLocation = newyork;
	} else {
	  alert("Your browser doesn't support geolocation. We've placed you in Siberia.");
	  initialLocation = siberia;
	}
	map.setCenter(initialLocation);
	}
}