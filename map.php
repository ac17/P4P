<!doctype html>
<html>
    <head>
    <meta charset="utf-8">
    <style type="text/css">
      #map-canvas { height:300px; margin: 0; padding: 0;}
    </style>
    
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
    <script src="//code.jquery.com/jquery-1.10.2.js"></script>
    <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
    <!-- Google Analytics -->
    <script>
      (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
      (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
      m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
      })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

      ga('create', 'UA-62930829-1', 'auto');
      ga('send', 'pageview');

    </script>
    <script>
	var spinner;
    $(function() {
        $( "#passDate" ).datepicker({
          onSelect: function( dateText ) {
			getMatchingExchanges();
          }
         });
        
        $( "#eatingClub" ).selectmenu({
          change: function( event, data ) {
          }
         });
		
		$( "#radio" ).buttonset();
		
        spinner = $( "#spinner" ).spinner({ min: 0 });       
    }); 
    </script>
    
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
<div id="map-canvas"></div>
<br/>
<table>
<tr>
<td>
    <form action="#">
      <fieldset>
        <label for="eatingClub">Eating Club: </label>
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
<td>
	<label for="spinner">Number of Passes:</label>
	<input id="spinner" name="value">
</td>
<td>
    Pass Date: <br /><input type="text" id="passDate" onChange=""><br /><br />
</td>
</tr>
</table>

<div id="result"></div>
<div id="debug"></div>

<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDuz6mK7HrFf1z04PdsgNkEv6AfQtYBH5o"></script>
<script src="js/map.js"></script>
    
</body>
</html>



