<?php
include_once('database_connect.php');
?>

<html>
<head>
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
	<?php
		$num = $_GET['uID'];
		$verC = $_GET['vc'];
		if (verify($num, $verC, $connection))
			echo "You have been successfully verified!";
		else
			echo "Uh oh, there was a problem verifying you.";
	?>
</body>
</html>