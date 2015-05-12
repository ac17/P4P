<?php
  /* Connect to the database. */
  include_once('php/database_connect.php');

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
    <link rel="icon" href="img/icon.png">

	<!-- for HTML5 App -->
    <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1.0, maximum-scale=1.0"/>
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />
    <link rel="apple-touch-icon" href="img/palm.png"/>
    <link rel="apple-touch-startup-image" href="img/palm.png" />
    
    <title>Dashboard</title>

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap-theme.min.css">
    <!-- jquery ui -->
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
    <!-- Custom -->
    <link href="css/dashboard.css" rel="stylesheet">
    <link href="css/inbox.css" rel="stylesheet">
    <link href="css/map.css" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="css/chatStyle.css" />
    <link href="css/exchangeManager.css" rel="stylesheet">
    <link rel="stylesheet" href="css/global.css">
    <link type="text/css" rel="stylesheet" href="css/chatPopup.css"/>

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
    /* Keep the settings modal open if a password or profile picture change just occurred. */
    if (isset($_POST['hiddenSettings']) && $_POST['hiddenSettings'] == 'true') {
        echo <<< CHANGESETTINGS
        <script type="text/javascript">
            $(window).load(function(){
                $('#settingsModal').modal('show');
            });
        </script>
CHANGESETTINGS;
    }
        if (isset($_POST['hiddenProf']) && $_POST['hiddenProf'] == 'true') {
            echo <<< CHANGEPP
            <script type="text/javascript">
                $(window).load(function(){
                    $('#settingsModal').modal('show');
                });
            </script>
CHANGEPP;
        }
    ?>

    <!-- Top Menu Bar -->
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
            <li><a><button type="button" data-toggle="modal" data-target="#settingsModal" class = "btn btn-default">Settings</button></a></li>
            <li><a href="#"><button type="button" data-toggle="modal" data-target="#helpModal" class = "btn btn-default">Help</button></a></li>
            <li><a href="logout.php"><button type="button" class = "btn btn-default">Log Out</button></a></li>
          </ul>
        </div>
      </div>
    </nav>

    <!-- Help Modal -->
    <div class="modal fade" id="helpModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Help</h2>
                </div>
                <div class="modal-body">
                  <h3>Searching for Passes</h3>
                  <h3>Requesting a Pass</h3>
                  <h3>Chatting</h3>
                  <h3>Further Help</h3>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-close" data-dismiss="modal"><i class="fa fa-close fa-2x"></i></button>
                </div>
              </div><!-- ./content -->
            </div><!-- ./dialog -->
          </div><!-- ./modal -->

    <!-- Settings Modal-->
    <div class="modal fade" id="settingsModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>User Settings</h2>
                </div>
                <div class="modal-body">
                    <?php
                    /* Form for changing password */
                    $settingsForm = <<< SETTINGS
                    <form action="{$_SERVER['PHP_SELF']}" method="post" id="settingsForm">
                      <h3>Change your password.</h3>
                        <input type="password" class="form-control" placeholder="Old password" id="oldPW" name="oldPW" aria-describedby="basic-addon1" required>
                        <br>
                        <input type="password" class="form-control" placeholder="New password" id="newPW" name="newPW" aria-describedby="basic-addon1" required>
                        <br>
                        <input type="hidden" name="hiddenSettings" id="hiddenSettings" value="true">
                        <button class="btn btn-default" id="settingsSubmit" type="submit" form="settingsForm" value="Submit">Submit</button>
                    </form>
SETTINGS;
                    /* Form for changing profile photo */
                    $changePhotoForm = <<<PROFPIC
                    <form action="{$_SERVER['PHP_SELF']}" method="post" id="photoForm" enctype="multipart/form-data">
                      <h3>Change your profile photo.</h3>
                      New profile photo: <input type="file" name="profPic" id="profPic" required><br>
                      <input type="hidden" name="hiddenProf" id="hiddenProf" value="true">
                      <button class="btn btn-default" id="profSubmit" type="submit" form="photoForm" value="Submit">Submit</button>
                    </form>
PROFPIC;
                    $err = array();
                    $success['changeSettings'] = FALSE;
                    $success['changeProfPic'] = FALSE;

                    /* Process password change. */
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

                    /* Process profile photo change. */
                    if (isset($_POST['hiddenProf']) && $_POST['hiddenProf'] == 'true') {
                      $target_dir = "img/";
                      $target_file = $target_dir . basename($_FILES["profPic"]["name"]);
                      $uploadOk = 1;
                      $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);

                      // Check if image file is a actual image or fake image
                      if(isset($_POST["submit"])) {
                          $check = getimagesize($_FILES["profPic"]["tmp_name"]);
                          if($check !== false) {
                              $err['changeProfPic'] = "File is an image - " . $check["mime"] . ".";
                              $uploadOk = 1;
                          } else {
                              $err['changeProfPic'] = "File is not an image.";
                              $uploadOk = 0;
                          }
                      }
                      // Check if file already exists
                      if (file_exists($target_file)) {
                          $err['changeProfPic'] = "Sorry, file already exists.";
                          $uploadOk = 0;
                      }
                      // Check file size
                      if ($_FILES["profPic"]["size"] > 5000000) {
                          $err['changeProfPic'] = "Sorry, your file is too large.";
                          $uploadOk = 0;
                      }
                      // Allow certain file formats
                      if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
                      && $imageFileType != "gif" ) {
                          $err['changeProfPic'] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                          $uploadOk = 0;
                      }
                      // Check if $uploadOk is set to 0 by an error
                      if ($uploadOk == 0) {
                          // $err['changeProfPic'] = "Sorry, your file was not uploaded.";
                      // if everything is ok, try to upload file
                      } else {
                          if (move_uploaded_file($_FILES["profPic"]["tmp_name"], $target_file)) {
                            /* Store name of profile photo in database. */
                            $profQ = 'UPDATE Users SET photo="' . basename($_FILES["profPic"]["name"]) . '" WHERE netId="' . $_SESSION['user']['netId'] . '";';
                            $profR = mysql_query($profQ);
                            if ($profR)
                              $success['changeProfPic'] = "The file ". basename($_FILES["profPic"]["name"]). " has been uploaded.";
                            else
                              $err['changeProfPic'] = "Sorry, there was an error uploading your file.";
                          } else {
                              $err['changeProfPic'] = "Sorry, there was an error uploading your file.";
                          }
                      }

                      /* Print appropriate success or error message. */
                      if ($success['changeProfPic'])
                        echo "<p>" . $success['changeProfPic'] . "</p>";
                      else
                        echo '<div class="alert alert-danger" role="alert">'. $err['changeProfPic'] .'</div>';
                    }
                    else {
                      echo $changePhotoForm;
                    }
                    ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-close" data-dismiss="modal"><i class="fa fa-close fa-2x"></i></button>
                </div>
            </div>
        </div>
    </div>

    <!-- Left Side Bar -->
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-3 col-md-2 sidebar">
          <ul class="nav nav-sidebar">
          <!-- User Info -->
          <?php
            if (!empty($_SESSION['user']['photo']))
              echo '<img src="img/' . $_SESSION['user']['photo'] . '" width="100%"></img>';
            else
          		echo '<img src="img/default.jpg" width="100%"></img>';
				$RequestedQuery = "SELECT reputation FROM Users WHERE netId='{$_SESSION['user']['netId']}' LIMIT 1;";
              	$RequestedResult = mysql_query($RequestedQuery);
				$Reputation = mysql_fetch_array(($RequestedResult));
				echo "<br>Reputation: " . $Reputation['reputation'];
          ?>
          <br /><br /><br />
          <!-- Notifications -->
          <li class="active"><a href=""><b>Notifications</b></a></li>
          <?php
              
          ?>
          </ul>
          <!-- Recently Requested Passes -->
          <ul class="nav nav-sidebar">
            <li><a href=""><b>Your Recently Requested Passes</b></a></li>
            <?php
              $RequestedQuery = "SELECT * FROM Exchange_history WHERE requesterNetId='{$_SESSION['user']['netId']}' ORDER BY requestPassDate DESC LIMIT 5;";
              $RequestedResult = mysql_query($RequestedQuery);
              if ($RequestedResult) {
                if (mysql_num_rows($RequestedResult) == 0)
                  echo "<li><a><i>0 records found.</i></a></li>";
                else {
                  for ($i = 0; $i < mysql_num_rows($RequestedResult); $i++) {
                    $row = mysql_fetch_assoc($RequestedResult);
                    $reqDate = date('m-d-Y', strtotime($row['requestPassDate']));
                    echo '<li><a href="#">' . $reqDate . ': ' . $row['requestPassType'] . '</a></li>';
                  }
                }
              }
            ?>
          </ul>
          <!-- Recently Offered Passes -->
          <ul class="nav nav-sidebar">
            <li><a href=""><b>Your Recently Offered Passes</b></a></li>
            <?php
              $RequestedQuery = "SELECT * FROM Exchange_history WHERE providerNetId='{$_SESSION['user']['netId']}' ORDER BY requestPassDate DESC LIMIT 5;";
              $RequestedResult = mysql_query($RequestedQuery);
              if ($RequestedResult) {
                if (mysql_num_rows($RequestedResult) == 0)
                  echo "<li><a><i>0 records found.</i></a></li>";
                else {
                  for ($i = 0; $i < mysql_num_rows($RequestedResult); $i++) {
                    $row = mysql_fetch_assoc($RequestedResult);
                    $reqDate = date('m-d-Y', strtotime($row['requestPassDate']));
                    echo '<li><a href="#">' . $reqDate . ': ' . $row['requestPassType'] . '</a></li>';
                  }
                }
              }
            ?>
          </ul>
        </div>
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
            <h1 class="page-header">Welcome <?php echo $_SESSION['user']['firstName']; ?>!</h1>
            <div id="tabs">
              <ul>
                <li><a href="#tab-1">Search for Passes</a></li>
                <li><a href="#tab-2">Your Offer/Request</a></li>
                <li><a href="#tab-3">Inbox</a></li>
              </ul>
              <!-- Map -->
              <div id="tab-1">
              	<div class="col-md-12">          
                    <div class="col-md-4">
                    <label for="searchEatingClub">Eating Club: </label>
                    <select name="searchEatingClub" id="searchEatingClub">
                      <option>Ivy Club</option>
                      <option selected="selected">Tower Club</option>
                      <option>Cottage</option>
                      <option>Cap and Gown</option>
		      <option>Cannon</option>
                      <option>Tiger Inn</option>
                      <option>All</option>
                    </select>
                    </div>
                    <div class="col-md-4">
                    <label for="numPasses">Number of Passes:</label>
                    <input id="numPasses" name="value">
                    </div>
                    <div class="col-md-4">
                    <label for="passDate">Pass Date:</label>
                    <input type="text" id="searchPassDate" onChange=""><br />
                    </div>
                </div>
                
                <div class="col-md-12" ><br /></div>
                
                <div class="col-md-12" align="center">
                    <b><div id="amount" style="font-weight:bold;"></div></b>       
                    <div id="range-slider"></div>
                </div>
                
                <div class="col-md-12" ><br /><br /></div>
                
                <div class="col-md-12" id="map-canvas"></div>
                
                <div class="col-md-12" ><br /><br /></div>
			
                	<input class = "btn btn-default" type="submit" value="Share Current Location" id="shareLocation" onClick="shareCurrentLocation('<?php echo $_SESSION['user']['netId']; ?>')">
                    <input class = "btn btn-default" type="submit" value="Reposition Map" id="shareLocation" onClick="updateMapToShowAllMarkers()">
			
              </div>
              
              <!-- Exchange Manager -->
              <div id="tab-2">                    
                <div class="col-md-12">          
                    <div class="col-md-4">
                    <form>
                      <fieldset>
                        <label for="eatingClub">Eating Club: </label>
                        <select name="eatingClub" id="eatingClub">
                          	<option>Ivy Club</option>
                      	  	<option selected="selected">Tower Club</option>
                      		<option>Cottage</option>
                      		<option>Cap and Gown</option>
		      				<option>Cannon</option>
                      		<option>Tiger Inn</option>
                        </select>
                        </fieldset>
                    </form>
					</div>
                	<div class="col-md-4">
                    <label for="spinner">Number of Passes:</label>
                    <input id="spinner" name="value">
                    </div>
                    <div class="col-md-4">
                    <label for="passDate">Pass Date:</label>
                    <input type="text" id="passDate"><br /><br />
                    </div>
                 </div>
                 <div class="col-md-12">
                	<div class="col-md-4">
                    </div>
                    <div class="col-md-4">
					<div class = "inputCenter">
                    <label for="comment">Comment:</label>
                    <textarea id="comment" rows="5" cols="20"></textarea> <br/>                  
			<input class = "btn btn-default" type="submit" value="Post" id="postExchange"></div>
                    <!-- used to pass netid to on click function for Post -->
                    <input type="hidden" id="netId" value="<?php echo $_SESSION['user']['netId']; ?>">
                    </div>
                    <div class="col-md-4">
                    </div>
                 </div>

                <div class="col-md-12"><br  /></div>
                <div class="col-md-12"><br  /></div>
              	<div class="col-md-12"><div class="tableTite">Trades</div></div>
                <div class="col-md-12"><br  /></div>
                <div class="col-md-12" id="tradeList">
                </div>
                
                <div class="col-md-12"><br  /><br  /></div>
                <div class="col-md-12"><div class="tableTite">Your Open Offers</div></div>
                <div class="col-md-12">
                    <ol id="offerList" class="selectable">
                    </ol>
                </div>
                
                <div class="col-md-12">
					<div class = "inputCenter">
                		<input class = "btn btn-default" type="submit" value="Delete Selected Offers" onMouseDown="removeSelectedOffers('<?php echo $_SESSION['user']['netId']; ?>')">
                	</div>
                </div>
                
                <div class="col-md-12"><br  /><br  /></div>
                <div class="col-md-12"><div class="tableTite">Your Pending Requests</div></div>
                <div class="col-md-12">
                    <ol id="requestList" class="selectable">
                    </ol>
                </div>
                
                <div class="col-md-12">
					<div class = "inputCenter">
                	<input class = "btn btn-default" type="submit" value="Delete Selected Requests" onMouseDown="removeSelectedRequests('<?php echo $_SESSION['user']['netId']; ?>')">
                	</div>
				</div>
                <font color="#FFFFFF">.</font>
             </div>
             

             <!-- Chat Manager -->
              <div class = "container-fluid" id="tab-3">  
                <?php
                  $query = 'SELECT * FROM chat_history WHERE (User_From = "'.$_SESSION['user']['netId'].'") 
                            OR (User_To = "'.$_SESSION['user']['netId'].'") ORDER BY Time desc;';
                  $result = mysql_query($query);

                  if (!$result) {
                    die("Could not query the database. " . mysql_error());;
                  }

                  echo '<table class = "table table-striped table-bordered table-hover table-condensed">'; // start a table to present the chat
		  //title row
		  echo '<tr><th class = "inboxheader" style="width:20%">Time</th><th class = "inboxheader" style="width:20%">Friend</th><th class = "inboxheader" style="width:60%">Most recent Conversation</th></tr>';

                  //Creates a loop to loop through results
                  $counter = 1;
                  $otherUser = "";
                  $usersInteractedWith = array();
                  while($row = mysql_fetch_array($result)){
                    /* keep track of any new users appearing on the chat history*/
                    if ((strcmp($row['User_From'], $otherUser) !== 0) && (strcmp($row['User_To'], $otherUser) !== 0)){
                      if (strcmp($row['User_From'], $_SESSION['user']['netId']) !== 0) 
                        $otherUser = $row['User_From'];
                      else
                        $otherUser = $row['User_To'];
                      if (!in_array($otherUser, $usersInteractedWith)){
                        $counter = 1;
                        array_push($usersInteractedWith, $otherUser);
                      }
                    }

                    /* retrieve the name of the otherUser*/
                    $getName = mysql_query('SELECT firstName FROM Users WHERE netId = "'.$otherUser.'";'); 
                    if (!$getName)
                      $otherUsername = "";
                    else if (mysql_num_rows($getName) == 0){
                      $otherUsername = "";
                    }
                    else
                      $otherUsername = mysql_result($getName, 0);
			
		    /*get photo of the otherUser*/
		    $getphoto = mysql_query('SELECT photo FROM Users WHERE netId = "'.$otherUser.'";'); 
    		    if (!$getphoto)
    			$photolink = "";
    		    else if (mysql_num_rows($getphoto) == 0){
                      $photolink = "";
                    }
		    else
    			$photolink = mysql_result($getphoto, 0);

		    /*html to insert picture*/
		    if($photolink === "" || $photolink === null) {
		    	$photo = '<div><img class="miniProfilePic" src="img/default.jpg"></img></div>';
	            } else {
			$photo = '<div><img class="miniProfilePic" src="img/'.$photolink.'"></img></div>';
		    }


                    /* identify if the message was sent by "You" or the other user*/
                    if (strcmp($row['User_From'], $_SESSION['user']['netId']) !== 0) 
                        $userFrom = $otherUsername;
                      else
                        $userFrom = "You";

                    /* print mst recent chat and link to chat with the other user*/
                    if ($counter === 1){

                      echo '<tr><td style="width:20%">'.$row['Time'].'</td><td style="width:20%"><a class = "clickable" onclick = "register_popup(\''.$otherUser.'\', \''.$otherUsername.'\')" >'.$otherUsername.$photo.'</a></td><td style="width:60%">' . $userFrom . ': ' . $row['Conversation'] .'</td></tr>';  //$row['index'] the index here is a field name
                    }
                    $counter = $counter + 1;    
                  }

                  echo "</table>"; //Close the table in HTML
                ?>  
              
              </div> 
            </div>
          </div>        
        </div>
      </div>
    </div>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="js/ie10-viewport-bug-workaround.js"></script>

    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBYvY6acR2OUH98E6icuclS44Gkp1378yo&libraries=geometry"></script>
    <script src="js/map.js"></script>
    <script src="js/exchangeManager.js"></script>
    <script src="js/dashboard.js"></script>
	<script src="js/popup.js"></script>
    

  <div id="invalid-passNum-dialog" title="Invalid Number of Passes">
  <p>
    <span class="ui-icon ui-icon-circle-check" style="float:left; margin:0 7px 50px 0;"></span>
    Please enter a whole number of passes greater than 0.
  </p>
  </div>

   <div id="error-dialog" title="We ran into a problem...">
  <p>
    <span class="ui-icon ui-icon-circle-check" style="float:left; margin:0 7px 50px 0;"></span>
    <div id="errorMessage"></div>
  </p>
  </div>
	
  <div id="chats"></div>
	
  </body>

</html>
