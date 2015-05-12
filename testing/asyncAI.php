<html>

<!-- Page for running a AI script outside of the control room -->

<div id="output" style="text-align:left; font-family:'MS Serif', 'New York', serif; font-size:14px">
</div>

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

	var checkedActions = JSON.parse('<?php echo $_GET['actions']; ?>');
	
	xmlhttp.open("POST", "./AI.php?hb=" + <?php echo $_GET['hb']; ?>, true);
	xmlhttp.setRequestHeader( "Content-Type", "application/json" );
	xmlhttp.send(JSON.stringify(checkedActions));
	document.getElementById("output").innerHTML = "Running";
</script>

</html>