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
    <!-- <link rel="icon" href="../../favicon.ico"> -->

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
    <link type="text/css" rel="stylesheet" href="css/chat_style.css" />
    <link href="css/exchangeManager.css" rel="stylesheet">
    <link rel="stylesheet" href="css/global.css">
    <link type="text/css" rel="stylesheet" href="/css/chatPopup.css"/>

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
        <div class="col-sm-3 col-md-2 sidebar">
          <ul class="nav nav-sidebar">
          <!-- User Info -->
          <?php
          		echo '<img src="img/palm.jpg" width="100%" height="100%"></img>';
				$RequestedQuery = "SELECT reputation FROM Users WHERE netId='{$_SESSION['user']['netId']}' LIMIT 1;";
              	$RequestedResult = mysql_query($RequestedQuery);
				$Reputation = mysql_fetch_array(($RequestedResult));
				echo "Reputation: " . $Reputation['reputation'];
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
                    <form action="#">
                      <fieldset>
                        <label for="searchEatingClub">Eating Club: </label>
                        <select name="searchEatingClub" id="searchEatingClub">
                          <option>Ivy Club</option>
                          <option>Tiger Inn</option>
                          <option selected="selected">Colonial</option>
                          <option>Cottage</option>
                          <option>Cap & Gown</option>
                          <option>Tiger Inn</option>
                          <option>All</option>
                        </select>
                        </fieldset>
                    </form>
                    </div>
                    <div class="col-md-4">
                    <label for="numPasses">Number of Passes:</label>
                    <input id="numPasses" name="value">
                    </div>
                    <div class="col-md-4">
                    <br />Pass Date: <br /><input type="text" id="searchPassDate" onChange=""><br />
                    </div>
                </div>
                
                <div class="col-md-12" ><br /><br /></div>
                
                <div class="col-md-12" id="map-canvas"></div>
                
                <div class="col-md-12" ><br /><br /></div>

                	<input type="submit" value="Share Current Location" id="shareLocation" onClick="shareCurrentLocation('<?php echo $_SESSION['user']['netId']; ?>')">

                <!--chatclient -->
              </div>
              
              <!-- Exchange Manager -->
              <div id="tab-2">                    
                <div class="col-md-12">          
                    <div class="col-md-4">
                    Pass Date: <br /><input type="text" id="passDate"><br /><br />
					</div>
                	<div class="col-md-4">
                    <label for="spinner">Number of Passes:</label>
                    <input id="spinner" name="value">
                    </div>
                    <div class="col-md-4">
                    <form>
                      <fieldset>
                        <label for="eatingClub">Eating Club: </label>
                        <select name="eatingClub" id="eatingClub">
                          <option>Ivy Club</option>
                          <option>Tiger Inn</option>
                          <option selected="selected">Colonial</option>
                          <option>Cottage</option>
                          <option>Cap & Gown</option>
                        </select>
                        </fieldset>
                    </form>
                    </div>
                 </div>
                 <div class="col-md-12">
                	<div class="col-md-4">
                    </div>
                    <div class="col-md-4">
                    <label for="comment">Comment:</label>
                    <textarea id="comment" rows="5" cols="20"></textarea>
                    <input type="submit" value="Post" id="postExchange">
                    <!-- used to pass netid to on click function for Post -->
                    <input type="hidden" id="netId" value="<?php echo $_SESSION['user']['netId']; ?>">
                    </div>
                 </div>

                <div class="col-md-12"><br  /></div>
              	<div class="col-md-12">Trades</div>
                <div class="col-md-12"><br  /></div>
                <div class="col-md-12"><br  /></div>
                <div class="col-md-12" id="tradeList">
                </div>
                
                <div class="col-md-12"><br  /><br  /></div>
                <div class="col-md-12">Your Open Offers</div>
                <div class="col-md-12">
                    <ol id="offerList" class="selectable">
                    </ol>
                </div>
                <div class="col-md-12">
                <input type="submit" value="Delete Selected Offers" onMouseDown="removeSelectedOffers('<?php echo $_SESSION['user']['netId']; ?>')">
                </div>
                
                <div class="col-md-12"><br  /><br  /></div>
                <div class="col-md-12">Your Pending Requests</div>
                <div class="col-md-12">
                    <ol id="requestList" class="selectable">
                    </ol>
                </div>
                
                <input type="submit" value="Delete Selected Requests" onMouseDown="removeSelectedRequests('<?php echo $_SESSION['user']['netId']; ?>')">

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
                    else
                      $otherUsername = mysql_result($getName, 0);

                    /* identify if the message was sent by "You" or the other user*/
                    if (strcmp($row['User_From'], $_SESSION['user']['netId']) !== 0) 
                        $userFrom = $otherUsername;
                      else
                        $userFrom = "You";

                    /* print mst recent chat and link to chat with the other user*/
                    if ($counter === 1){
                      echo '<tr><td style="width:20%">'.$row['Time'].'</td><td style="width:10%"><a href = "/php/chat.php?recipient='.$otherUser.'" target="popup" onclick="window.open("/php/chat.php?recipient='.$otherUser.'","Chat","width=600,height=400")>'.$otherUsername.'</a></td><td style="width:70%">' . $userFrom . ': ' . $row['Conversation'] .'</td></tr>';  //$row['index'] the index here is a field name
                    }
                    /*else
                      echo '<tr><td>' . $userFrom . '</td><td>' . $row['Conversation'] .'</td></tr>';  //$row['index'] the index here is a field name*/
                    $counter = $counter + 1;    
                  }

                  echo "</table>"; //Close the table in HTML
                ?>  


                <script>
                //this function can remove a array element.
                Array.remove = function(array, from, to) {
                  var rest = array.slice((to || from) + 1 || array.length);
                  array.length = from < 0 ? array.length + from : from;
                  return array.push.apply(array, rest);
                };

                //this variable represents the total number of popups can be displayed according to the viewport width
                var total_popups = 0;
            
                //arrays of popups ids
                var popups = [];

                //this is used to close a popup
                function close_popup(id)
                {
                  for(var iii = 0; iii < popups.length; iii++)
                  {
                    if(id == popups[iii])
                    {
                      Array.remove(popups, iii);

                      document.getElementById(id).style.display = "none";

                      calculate_popups();

                      return;
                    }
                  }   
                }

                //displays the popups. Displays based on the maximum number of popups that can be displayed on the current viewport width
                function display_popups()
                {
                  var right = 220;

                  var iii = 0;
                  for(iii; iii < total_popups; iii++)
                  {
                    if(popups[iii] != undefined)
                    {
                      var element = document.getElementById(popups[iii]);
                      element.style.right = right + "px";
                      right = right + 320;
                      element.style.display = "block";
                    }
                  }

                  for(var jjj = iii; jjj < popups.length; jjj++)
                  {
                    var element = document.getElementById(popups[jjj]);
                    element.style.display = "none";
                  }
                }
            
                //creates markup for a new popup. Adds the id to popups array.
                function register_popup(id, name)
                {

                  for(var iii = 0; iii < popups.length; iii++)
                  {   
                    //already registered. Bring it to front.
                    if(id == popups[iii])
                    {
                      Array.remove(popups, iii);

                      popups.unshift(id);

                      calculate_popups();


                      return;
                    }
                  }               

                  var element = '<div class="popup-box chat-popup" id="'+ id +'">';
                  element = element + '<div class="popup-head">';
                  element = element + '<div class="popup-head-left">'+ name +'</div>';
                  element = element + '<div class="popup-head-right"><a href="javascript:close_popup(\''+ id +'\');">&#10005;</a></div>';
                  element = element + '<div style="clear: both"></div></div><div class="popup-messages"></div></div>';

                  document.getElementsByTagName("body")[0].innerHTML = document.getElementsByTagName("body")[0].innerHTML + element;  

                  popups.unshift(id);

                  calculate_popups();

                }

                //calculate the total number of popups suitable and then populate the toatal_popups variable.
                function calculate_popups()
                {
                  var width = window.innerWidth;
                  if(width < 540)
                  {
                    total_popups = 0;
                  }
                  else
                  {
                    width = width - 200;
                    //320 is width of a single popup box
                    total_popups = parseInt(width/320);
                  }

                  display_popups();

                }

               //recalculate when window is loaded and also when window is resized.
                window.addEventListener("resize", calculate_popups);
                window.addEventListener("load", calculate_popups);           
              </script>
              
              <div class="chat-sidebar">
                <div class="sidebar-name">
                  <!-- Pass username and display name to register popup -->
                  <a href="javascript:register_popup('narayan-prusty', 'Narayan Prusty');">
                    <img width="30" height="30" src="https://fbcdn-profile-a.akamaihd.net/hprofile-ak-xap1/v/t1.0-1/p50x50/1510656_10203002897620130_521137935_n.jpg?oh=572eaca929315b26c58852d24bb73310&oe=54BEE7DA&__gda__=1418131725_c7fb34dd0f499751e94e77b1dd067f4c" />
                    <span>Narayan Prusty</span>
                  </a>
                </div>
                <div class="sidebar-name">
                  <a href="javascript:register_popup('qnimate', 'QNimate');">
                    <img width="30" height="30" src="https://fbcdn-profile-a.akamaihd.net/hprofile-ak-xap1/v/t1.0-1/p50x50/1510656_10203002897620130_521137935_n.jpg?oh=572eaca929315b26c58852d24bb73310&oe=54BEE7DA&__gda__=1418131725_c7fb34dd0f499751e94e77b1dd067f4c" />
                    <span>QNimate</span>
                  </a>
                </div>
                <div class="sidebar-name">
                  <a href="javascript:register_popup('qscutter', 'QScutter');">
                    <img width="30" height="30" src="https://fbcdn-profile-a.akamaihd.net/hprofile-ak-xap1/v/t1.0-1/p50x50/1510656_10203002897620130_521137935_n.jpg?oh=572eaca929315b26c58852d24bb73310&oe=54BEE7DA&__gda__=1418131725_c7fb34dd0f499751e94e77b1dd067f4c" />
                    <span>QScutter</span>
                  </a>
                </div>
                <div class="sidebar-name">
                  <a href="javascript:register_popup('qidea', 'QIdea');">
                    <img width="30" height="30" src="https://fbcdn-profile-a.akamaihd.net/hprofile-ak-xap1/v/t1.0-1/p50x50/1510656_10203002897620130_521137935_n.jpg?oh=572eaca929315b26c58852d24bb73310&oe=54BEE7DA&__gda__=1418131725_c7fb34dd0f499751e94e77b1dd067f4c" />
                    <span>QIdea</span>
                  </a>
                </div>
                <div class="sidebar-name">
                  <a href="javascript:register_popup('qazy', 'QAzy');">
                    <img width="30" height="30" src="https://fbcdn-profile-a.akamaihd.net/hprofile-ak-xap1/v/t1.0-1/p50x50/1510656_10203002897620130_521137935_n.jpg?oh=572eaca929315b26c58852d24bb73310&oe=54BEE7DA&__gda__=1418131725_c7fb34dd0f499751e94e77b1dd067f4c" />
                    <span>QAzy</span>
                  </a>
                </div>
                <div class="sidebar-name">
                  <a href="javascript:register_popup('qblock', 'QBlock');">
                    <img width="30" height="30" src="https://fbcdn-profile-a.akamaihd.net/hprofile-ak-xap1/v/t1.0-1/p50x50/1510656_10203002897620130_521137935_n.jpg?oh=572eaca929315b26c58852d24bb73310&oe=54BEE7DA&__gda__=1418131725_c7fb34dd0f499751e94e77b1dd067f4c" />
                    <span>QBlock</span>
                  </a>
                </div>
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

    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDr4bn-X3zDwAemTNf8KEnTGbc8yFnoio4"></script>
	<script src="js/map.js"></script>
    <script src="js/exchangeManager.js"></script>
    <script src="js/dashboard.js"></script>

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
