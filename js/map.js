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
	
	xmlhttp.open("GET", "./php/searchExchanges.php?date=" + $( "#searchPassDate" ).val() + "&type=Offer" + "&numPasses=" + numPasses.spinner( "value" ) + "&club=" + $('#searchEatingClub :selected').text(), true);
	xmlhttp.send();
}

function addUserToMap(user) {
	var contentString = '<div id="content">'+
      '<div id="siteNotice">'+
      '</div>'+
      '<h4 id="firstHeading" class="firstHeading">'+
	  user.name +
	  '</h4>'+
      '<div id="bodyContent">';
	  var i;
	  for (i=0; i<user.exchanges.length; i++) {
		var exchange = user.exchanges[i];
		contentString = contentString +
		'<div class="offerDiv" onclick="pursueOffer('+exchange.id+')">' +
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
	alert(offerId);
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