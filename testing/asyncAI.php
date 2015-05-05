<html>
<script>
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
			alert("AI finished.");
			document.getElementById("output").innerHTML = xmlhttp.responseText;
			
		}
	}

	xmlhttp.open("GET", "./AI.php?hb=" + <?php echo $_GET['hb']; ?>, true);
	xmlhttp.send();
	document.getElementById("output").innerHTML = "Running";
</script>

<div id="output" style="text-align:left; font-family:'MS Serif', 'New York', serif; font-size:14px">
</div>

</html>