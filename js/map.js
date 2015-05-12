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
function getMatchingExchanges()
{
	// Clear the map of all markers.
	deleteMarkers();
	
	if (window.XMLHttpRequest)
	{//  IE7+, Firefox, Chrome, Opera, Safari
	  getMatchingExchanges_xmlhttp = new XMLHttpRequest();
	}
	else
	{//  IE6, IE5
	  getMatchingExchanges_xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	  
	getMatchingExchanges_xmlhttp.onreadystatechange=function()
	{
		// On sucessful AJAX return 
		if (getMatchingExchanges_xmlhttp.readyState==4 && getMatchingExchanges_xmlhttp.status==200)
		{	
			// Add all matching offers to the map
			var json = JSON.parse(getMatchingExchanges_xmlhttp.responseText);
			if (json.Users.length>0) {
				var i;
				for (i=0; i<json.Users.length; i++) { 
					var user = json.Users[i];
					addUserToMap(user);
				} 
				// Show only those markers which are within a radius specified by the slider of the user's current location
				showMarkersWithinRange($( "#range-slider" ).slider( "value" ));
				// Adjust the map zoom and location to show all the markers 
				updateMapToShowAllMarkers();
			}
		}
	}
	
	// Call script to get all offers which match the user's query
	getMatchingExchanges_xmlhttp.open("GET", "./php/searchExchangesUserSpecific.php?date=" + $( "#searchPassDate" ).val() + "&type=Offer" + "&numPasses=" + numPasses.spinner( "value" ) + "&club=" + $('#searchEatingClub :selected').text() + "&netId=" + document.getElementById("netId").value, true);
	getMatchingExchanges_xmlhttp.send();
}

/* Function to get all exchanges and display them on map.
Used for the control room map. */
function getAllExchanges()
{
	deleteMarkers();
	if (window.XMLHttpRequest)
	{//  IE7+, Firefox, Chrome, Opera, Safari
	  getAllExchanges_xmlhttp = new XMLHttpRequest();
	}
	else
	{//  IE6, IE5
	  getAllExchanges_xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	  
	getAllExchanges_xmlhttp.onreadystatechange=function()
	{
		// On sucessful AJAX return
		if (getAllExchanges_xmlhttp.readyState==4 && getAllExchanges_xmlhttp.status==200)
		{
			// Display all markers
			var json = JSON.parse(getAllExchanges_xmlhttp.responseText);
			if (json.Users.length>0) {
				var i;
				for (i=0; i<json.Users.length; i++) { 
					var user = json.Users[i];
					addUserToMap(user);
				} 
				// Readjust the map to show all markers 
				updateMapToShowAllMarkers();
			}
		}
	}
	
	// Call a script to get all exhanges
	getAllExchanges_xmlhttp.open("GET", "./php/getAllExchanges.php", true);
	getAllExchanges_xmlhttp.send();
}

/* Function to add a marker for a user and an info window which shows all 
of that user's matching offers. */
function addUserToMap(user) {
	var photo; 
	
	if(user.photo == "" || user.photo == null)
	{
		photo = '<div id = "chatlink"><a onclick="register_popup(\''+user.netId+'\', \''+user.name+'\')" ><img class="miniProfilePic" src="img/default.jpg" width="100%"></img>';
	}
	else
	{
		photo = '<div id = "chatlink"><a onclick="register_popup(\''+user.netId+'\', \''+user.name+'\')" ><img class="miniProfilePic" src="img/'+user.photo+'" width="100%"></img>';
	}
	
	var chat = '<div id = "chatlink"><a onclick="register_popup(\''+user.netId+'\', \''+user.name+'\')" >Chat</a></div>';
	
	var contentString = '<div class="infoWinContent">'+
      '<h4 class="infoWinHeading">'+
	  photo+
	  " "+
	  user.name +
	  '</h4></a>'+
      '<div class="infoWinbodyContent">';


      // link for chatting
      //contentString = contentString + 
      //'<span id = "chatlink"><a onclick = "register_popup(\''+ user.netId + '\',\'' + user.name + '\');" >Chat</a></span>';


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
	
	// Create a marker and infowindow and add them to the map
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
	
	// Save the marker and infowindow in the arrays 
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
			  pursueOffer_xmlhttp = new XMLHttpRequest();
			}
			else
			{//  IE6, IE5
			  pursueOffer_xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
			}
			  
			pursueOffer_xmlhttp.onreadystatechange=function()
			{
				if (pursueOffer_xmlhttp.readyState==4 && pursueOffer_xmlhttp.status==200)
				{
					// check if an error message has been returned 
					if(pursueOffer_xmlhttp.responseText != "")
					{
						showError("A Small Problem...", pursueOffer_xmlhttp.responseText);
						getMatchingExchanges();
					}
				}
			}

			pursueOffer_xmlhttp.open("GET", "./php/pursueOffer.php?netId=" + document.getElementById("netId").value + "&offerId=" + offerId, true);
			pursueOffer_xmlhttp.send();
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
	var atLeastOneMarker = false; 
	
	// check if there is atleat one visible marker before 
	// changing the map 
	var bounds = new google.maps.LatLngBounds();
	for(i=0;i<markers.length;i++) {
		if (markers[i].getVisible() == true)
		{
			atLeastOneMarker = true; 
	   		bounds.extend(markers[i].getPosition());
		}
	}
	
	//center the map to the geometric center of all markers
	if (atLeastOneMarker == true)
	{
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

}

/* Function to hide or display markers if they are with in a range from the user's current location.
*/
function showMarkersWithinRange(range)
{
	// the range is in miles
	var milesToMeters = 1609.34;
	range = range*milesToMeters;
	// Try W3C Geolocation (Preferred)
	if(navigator.geolocation) {
	browserSupportFlag = true;
	navigator.geolocation.getCurrentPosition(function(position) {
	  initialLocation = new google.maps.LatLng(position.coords.latitude,position.coords.longitude);
	  // if a location is sucessfuly obtained, clear all marrkes
	  clearMarkers();

	  // redisplay makers which are with the specified range 
	  for(i=0;i<markers.length;i++) {
	   	if (google.maps.geometry.spherical.computeDistanceBetween(initialLocation, markers[i].getPosition()) <= range)
		{ 
			markers[i].setMap(map);
			markers[i].setVisible(true);
		}																	   
	  }
	  
	  // recenter and zoom map to show all visible markers
	  updateMapToShowAllMarkers();
	
	}, function() {
	  handleNoGeolocation(browserSupportFlag);
	});
	}
	// Browser doesn't support Geolocation
	else {
	browserSupportFlag = false;
	handleNoGeolocation(browserSupportFlag);
	}
	
	// if an error occours in getting the location, display all of the markers
	function handleNoGeolocation(errorFlag) {
	if (errorFlag == true) { 
	  showMarkers();
	  updateMapToShowAllMarkers();
	} else {
	  showMarkers();
	  updateMapToShowAllMarkers();
	}
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
	  // if a location is sucessfuly obtained, center the map on it and update the database
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
	
	// Display error messages if location could not be obtained
	function handleNoGeolocation(errorFlag) {
	if (errorFlag == true) {
	  showError("A Small Problem...", "Geolocation service failed.");
	} else {
	  showError("A Small Problem...", "Your browser doesn't support geolocation. :( ");
	}
	}
}
