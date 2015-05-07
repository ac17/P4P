<?php
  /* Connect to the database. */
  include_once('../php/database_connect.php');

  /* If the user is not logged in, redirect to the login page. */
  if (!isUserLoggedIn())
    header('Location: loginUser.php');
?>

<!DOCTYPE html>
<html manifest="P4P.manifest">

<html lang="en">
  <head> 
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="User Dashboard">
    <meta name="author" content="">
    <link rel="icon" href="../img/icon.png">

	<!-- for HTML5 App -->
    <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1.0, maximum-scale=1.0"/>
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />
    <link rel="apple-touch-icon" href="img/palm.png"/>
    <link rel="apple-touch-startup-image" href="img/palm.png" />
    
    <title>Control Room</title>

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap-theme.min.css">
    <!-- jquery ui -->
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
    <!-- Custom -->
    <link href="../css/dashboard.css" rel="stylesheet">
    <link href="../css/inbox.css" rel="stylesheet">
    <link href="../css/map.css" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="../css/chatStyle.css" />
    <link href="../css/exchangeManager.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/global.css">
    <link type="text/css" rel="stylesheet" href="../css/chatPopup.css"/>
    <link type="text/css" rel="stylesheet" href="controlRoom.css"/>
    <link type="text/css" rel="stylesheet" href="scroller.css"/>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
  </head>

  <body onLoad="loadUserData('<?php echo $_SESSION['user']['netId']; ?>')">
  </head>

  <body>

    <?php
    if (isset($_POST['hiddenSettings']) && $_POST['hiddenSettings'] == 'true') {
        echo <<< CHANGESETTINGS
        <script type="text/javascript">
            $(window).load(function(){
                $('#settingsModal').modal('show');
            });
        </script>
CHANGESETTINGS;
    }
    ?>

    <nav class="navbar navbar-default navbar-fixed-top">
      <div class="container-fluid">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="index.php">Passes for Passes</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav navbar-right">
            <li><a><button type="button" data-toggle="modal" data-target="#settingsModal">Settings</button></a></li>
            <li><a href="#">Help</a></li>
            <li><a href="logout.php">Log Out</a></li>
          </ul>
        </div>
      </div>
    </nav>

    <!-- Settings Modal-->
    <div class="modal fade" id="settingsModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>User Settings</h2>
                </div>
                <div class="modal-body">
                    <?php
                    $settingsForm = <<< SETTINGS
                    <form action="{$_SERVER['PHP_SELF']}" method="post" id="settingsForm">
                      <h3>Change your password.</h3>
                        <input type="password" class="form-control" placeholder="Old password" id="oldPW" name="oldPW" aria-describedby="basic-addon1">
                        <br>
                        <input type="password" class="form-control" placeholder="New password" id="newPW" name="newPW" aria-describedby="basic-addon1">
                        <br>
                        <input type="hidden" name="hiddenSettings" id="hiddenSettings" value="true">
                        <button class="btn btn-default" id="settingsSubmit" type="submit" form="settingsForm" value="Submit">Submit</button>
                    </form>
SETTINGS;
                    $err = array();
                    $success['changeSettings'] = FALSE;
                    if (isset($_POST['hiddenSettings']) && $_POST['hiddenSettings'] == 'true') {
                      $old = stripslashes(htmlspecialchars($_POST['oldPW']));
                      $new = stripslashes(htmlspecialchars($_POST['newPW']));

                      /* check that old password is correct */
                      $oldPWHash = md5($old);
                      if ($oldPWHash == $_SESSION['user']['password']) {
                        $changeQuery = 'UPDATE Users SET password="' . md5($new) . '" WHERE netId="' . $_SESSION['user']['netId'] . '";';
                        $changeResult = mysql_query($changeQuery);
                        if ($changeResult) {
                          $success['changeSettings'] = 'Your password has been successfully changed!';
                        }
                        else {
                          $err['changeSettings'] = 'Sorry, we could not connect to the database at this time. Please reload the page and try again.';
                        }
                      }
                      else {
                        $err['changeSettings'] = 'Sorry, your old password is incorrect! Please reload the page and try again.';
                      }

                      /* Print appropriate success or error message. */
                      if ($success['changeSettings'])
                        echo "<p>" . $success['changeSettings'] . "</p>";
                      else
                        echo '<div class="alert alert-danger" role="alert">'. $err['changeSettings'] .'</div>';
                    }
                    else {
                      echo $settingsForm;
                    }
                    ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-close" data-dismiss="modal"><i class="fa fa-close fa-2x"></i></button>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-12 main">
            <h1 class="page-header">Welcome to the Control Room <?php echo $_SESSION['user']['firstName']; ?>!</h1>
            <?php
          		echo '<img src="../img/base.jpg" width="100%" height="100%"></img>';
            ?>
            <div id="tabs">
              <ul>
                <li><a href="#tab-1">World Map</a></li>
                <li><a href="#tab-2">Server Status</a></li>
                <li><a href="#tab-3">NPCs</a></li>
                <li><a href="#tab-4">AI</a></li>
                <li><a href="#tab-5">Garbage Collector</a></li>
                <li><a href="#tab-6">?</a></li>
              </ul>
              <!-- Map -->
              <div id="tab-1">
              	<div class="col-md-12" ><br /><br /></div>
                
                <div class="col-md-12" id="map-canvas"></div>
                
                <div class="col-md-12" ><br /><br /></div>

                <input type="submit" value="Reposition Map" id="shareLocation" onClick="updateMapToShowAllMarkers()">
              </div>
              
              <div id="tab-2">
                
				<div class="col-md-12" id="serverStats"></div>
                <input type="submit" value="Refresh" onClick="getServerStats()">
              </div>
              
              <!-- NPC -->
              <div id="tab-3">

                  <div class="col-md-12">                    
                    <div class="col-md-6">
                        <input type="submit" value="Add New NPC" id="addNPC">
                    </div>
                    <div class="col-md-6">
                        <input type="submit" value="Purge NPCs" id="purgeNPCs">
                    </div>
                  </div>
				  <br />
                  <br />
                  <div class="col-md-12">
                      <div class="col-md-3 tableHeader"> netId </div>
                      <div class="col-md-3 tableHeader"> firstName </div>
                      <div class="col-md-3 tableHeader"> lastName </div>
                      <div class="col-md-2 tableHeader"> reputation </div>
                      <div class="col-md-1 tableHeader"> delete </div>
                      <div id="npcList">
                      </div>
                  </div>
                  
              <input type="submit" value="Refresh" onClick="getAllNPCs()">
              </div>
              
             <!-- Chat Manager -->
              <div class = "container-fluid" id="tab-4">  
                
                 <div class="col-md-12">          
                	<div class="col-md-4">
                    <label for="spinner">Heartbeats:</label>
                    <input id="spinner" name="value">
                    </div>
                    <div class="col-md-4" align="left">
                    <label for="checkbox">Actions:</label>
                    <form action="">
                    	<input type="checkbox" class="actionCheckBox" name="action" value="moveUser"> moveUser <br>
                    	<input type="checkbox" class="actionCheckBox" name="action" value="randomOffer"> randomOffer  <br>
                        <input type="checkbox" class="actionCheckBox" name="action" value="seekAndPursueOffer"> seekAndPursueOffer  <br>
                        <input type="checkbox" class="actionCheckBox" name="action" value="manageOffersAndRequests"> manageOffersAndRequests  <br>
                        <input type="checkbox" class="actionCheckBox" name="action" value="trade"> trade <br>
                    </form>
                    </div>
                    <div class="col-md-4">
                    <input type="submit" value="Run AI" id="runAI">
                    <input type="submit" value="Run Async AI" id="runAsyncAI">
                    </div>
                 </div>
                 <br />
                 <br />
                 <br />
                 <br /><br />
                 <br /><br />
                 <br /><br />
                 <br /><br />
                 <div class="col-md-12" id="output" style="text-align:left; font-family:'MS Serif', 'New York', serif; font-size:14px">
                 </div>
              </div> 
              
              <div class = "container-fluid" id="tab-5">  
                <div class="col-md-12" id="garbageCollector"></div>
                <input type="submit" value="Refresh" onClick="getGarbageStats()">
              </div>
              
              <div class = "container-fluid" id="tab-6">
              	<div id="scroller" style="width: 550px; height: 400px; margin: 0 auto;">
                	<div class="innerScrollArea">
                        <ul>
                        <!-- Define photos here -->
                            <li><img src="../img/BrianKernighan.jpg" height="300" width="200"/></li>
                            <li><img src="../img/BrianKernighan.jpg" height="300" width="200"/></li>
                            <li><img src="../img/BrianKernighan.jpg" height="300" width="200"/></li>
                            <li><img src="../img/BrianKernighan.jpg" height="300" width="200"/></li>
                            <li><img src="../img/BrianKernighan.jpg" height="300" width="200"/></li>
                            <li><img src="../img/BrianKernighan.jpg" height="300" width="200"/></li>
                            <li><img src="../img/BrianKernighan.jpg" height="300" width="200"/></li>
                        </ul>
                	</div>
                </div>
              </div> 
            </div>
          </div>        
        </div>
      </div>
    </div>
	
    <br />
    <br />
    <br />
    <br />
    <br />
    <br />
    <br />
    <br />
    <br />
    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="js/ie10-viewport-bug-workaround.js"></script>

    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB_TP0TtPiuDslXloAv3TKPMDnKgIQu65U"></script>
	<script src="map.js"></script>
    <script src="../js/exchangeManager.js"></script>
    <script src="controlRoom.js"></script>
    <script src="../js/popup.js"></script>
    
    

  <div id="invalid-passNum-dialog" title="Invalid Number of Passes">
  <p>
    <span class="ui-icon ui-icon-circle-check" style="float:left; margin:0 7px 50px 0;"></span>
    Please enter a whole number of passes greater than 0.
  </p>
  </div>

  <div id="error-dialog" title="Invalid Number of Passes">
  <p>
    <span class="ui-icon ui-icon-circle-check" style="float:left; margin:0 7px 50px 0;"></span>
    <div id="errorMessage"></div>
  </p>
  </div>

	
  </body>

</html>
