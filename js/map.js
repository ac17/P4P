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
			if (json.Exchanges.length>0) { 
				for (i=0; i<json.Exchanges.length; i++) { 
					var exchange = json.Exchanges[i];
					addOfferMarker(exchange);
				}  
			} 
		}
	}
	
	xmlhttp.open("GET", "./php/searchExchanges.php?date=" + $( "#passDate" ).val() + "&type=Offer" + "&numPasses=" + spinner.spinner( "value" ) + "&club=" + $('#eatingClub :selected').text(), true);
	xmlhttp.send();
}

function addOfferMarker(exchange) {
	var contentString = '<div id="content">'+
      '<div id="siteNotice">'+
      '</div>'+
      '<h2 id="firstHeading" class="firstHeading">'+
	  exchange.passNum +
	  " " +
	  exchange.club +
	  " " +
	  exchange.name +
	  '</h2>'+
      '<div id="bodyContent">'+ exchange.comments +
      '</div>'+
      '</div>';
	
	var myLatlng = new google.maps.LatLng(exchange.lat, exchange.lng);
	
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

var timer;
var searchDelay = 250; 
function delaySearch(){
   clearTimeout(timer);
   timer = setTimeout(search, searchDelay);
}

function getNote(id)
{
	xmlhttp = new XMLHttpRequest();
	xmlhttp.open("GET", "./php/displayNote.php?id=" + id, false);
	xmlhttp.send(null);
	document.getElementById("result").innerHTML = xmlhttp.responseText;
}