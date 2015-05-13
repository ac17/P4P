<!DOCTYPE html>
<html>
  <head>
    <style type="text/css">
      #map-canvas { height:300px; margin: 0; padding: 0;}
    </style>
    <script type="text/javascript"
      src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAaLYTn01fx9KSANnrdCHp64Yt0hJ2dHAE">
    </script>
    <script type="text/javascript">
	  var map;
	  var markers = [];
      function initialize() {
        var mapOptions = {
          center: { lat: 40.348374, lng: -74.652918},
          zoom: 20
        };
        map = new google.maps.Map(document.getElementById('map-canvas'),
            mapOptions);
      }
      google.maps.event.addDomListener(window, 'load', initialize);
    </script>

    <!-- Google Analytics -->
    <script>
      (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
      (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
      m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
      })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

      ga('create', 'UA-62930829-1', 'auto');
      ga('send', 'pageview');

    </script>
  </head>
  <body>
  
<div id="map-canvas"></div>

<form name="searchBox" action="" method="post">
User Name: <input type="text" name="userName"/><br />
<button type="button" onClick="recordLocation()">Record Location</button>
<button type="button" onClick="stopRecording()">Stop Recording</button>
</form>

<div id="debug"></div>
<br />
<div id="result"></div>

<script type="text/javascript">

var record; 

function recordLocation()
{
	document.getElementById("debug").innerHTML = "Recording";
	record = setInterval(updateLocation, 1000);
}

function stopRecording()
{
	document.getElementById("debug").innerHTML = "Stopped";
	clearInterval(record);
}

function updateLocation()
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
					document.getElementById("result").innerHTML = xmlhttp.responseText;
				}
			}
			
			xmlhttp.open("GET", "./php/updateLocation.php?name="+document.searchBox.userName.value+"&lat="+position.coords.latitude+"&lng="+position.coords.longitude, true);
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

function search()
{
	deleteMarkers();
	document.getElementById("result").innerHTML = "";
	
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
			if (json.Locations.length>0) { 
				for (i=0; i<json.Locations.length; i++) { 
					var location = json.Locations[i];
					document.getElementById("result").innerHTML = document.getElementById("result").innerHTML + location.name + " " + location.lat + " " +  location.lng + "<br />";
					addLocation(location); 
				}  
			} 
		}
	}
	
	xmlhttp.open("GET", "./php/search.php?query=" + document.searchBox.searchQuery.value, true);
	xmlhttp.send();
}

function addLocation(location) {
	var myLatlng = new google.maps.LatLng(location.lat, location.lng);
	var marker = new google.maps.Marker({
	  position: myLatlng,
	  map: map,
	  title: location.name
	});
	markers.push(marker);
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
</script>

	<!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script src="./bootstrap/js/bootstrap.min.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="../../assets/js/ie10-viewport-bug-workaround.js"></script>
    
  </body>
</html>






