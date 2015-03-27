<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<meta name="description" content="">
<meta name="author" content="">
<link rel="icon" href="../../favicon.ico">

<title>Starter Template for Bootstrap</title>

<!-- Bootstrap core CSS -->
<link href="./bootstrap/css/bootstrap.min.css" rel="stylesheet">

<!-- Custom styles for this template -->
<link href="starter-template.css" rel="stylesheet">
    
<title>Untitled Document</title>

<link rel="stylesheet" type="text/css" href="CSS/style.css">

</head>
<body>
<?php 
  //require "./PHP/harvestNote.php";
  //massHarvest("./JeffNoteURLs.txt");
  
?>

<div class="container">
    <nav class="navbar navbar-default">
        <div class="container">
          <div class="navbar-header">
            <a class="navbar-brand" href="#">Find User</a>
          </div>
          <ul class="nav navbar-nav">
            <li><a><form name="searchBox" action="" method="post">
            Search: <input type="text" name="searchQuery" onkeyup="search()" on/><br />
            </form></a></li>
            </ul>
       </div>
     </nav>
     
    <div id="result"></div>
</div>

</body>

<script type="text/javascript">

function search()
{
	xmlhttp = new XMLHttpRequest();
	xmlhttp.open("GET", "./php/searchUsers.php?query=" + document.searchBox.searchQuery.value, false);
	xmlhttp.send(null);
	document.getElementById("result").innerHTML = xmlhttp.responseText;
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
    
</html>
