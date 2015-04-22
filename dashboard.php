<?php
  /* Connect to the database. */
  include_once('php/database_connect.php');

  /* If the user is not logged in, redirect to the login page. */
  if (!isUserLoggedIn())
    header('Location: loginUser.php');
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="User Dashboard">
    <meta name="author" content="">
    <!-- <link rel="icon" href="../../favicon.ico"> -->

    <title>Dashboard</title>

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap-theme.min.css">
    <!-- jquery ui -->
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
    <!-- Custom -->
    <link rel="stylesheet" href="css/global.css">
    <link href="css/dashboard.css" rel="stylesheet">
    <link href="css/map.css" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="../css/chat_style.css" />
    <link href="css/exchangeManager.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
  </head>

  <body onLoad="getUserActiveExchanges('<?php echo $_SESSION['user']['netId']; ?>')">
  </head>

  <body>

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
            <li><a href="#">Settings</a></li>
            <li><a href="#">Help</a></li>
            <li><a href="logout.php">Log Out</a></li>
          </ul>
        </div>
      </div>
    </nav>

    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-3 col-md-2 sidebar">
          <ul class="nav nav-sidebar">
            <li class="active"><a href=""><b>Notifications</b></a></li>
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
                <li><a href="#tab-3">Transactions</a></li>
              </ul>
              <!-- Map -->
              <div id="tab-1">          
                <table>
                <tr>
                <td>
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
                </td>
                <td>
                <label for="numPasses">Number of Passes:</label>
                <input id="numPasses" name="value">
                </td>
                <td>
                <br />Pass Date: <br /><input type="text" id="searchPassDate" onChange=""><br />
                </td>
                </tr>
                </table>
                
                <div id="map-canvas"></div>
                
                <br /><br />


                <!--chatclient -->

                <link type="text/css" rel="stylesheet" href="../css/chat_style.css" />
                 
                <?php
                $chatto = $_GET['chatwith'];

                /* If the user is not logged in, redirect to the login page. */
                if (!isUserLoggedIn()){
                    header('Location: ../loginUser.php');
                }
                else{
                ?>
                <div id="wrapper">
                    <div id="menu">
                      <!-- welcome message + exit button!-->
                        <p class="welcome">Welcome, <b><?php echo $_SESSION['user']['firstName']; ?></b></p>
                        <p class="logout"><a id="loggingout" href="#">Logout</a></p>
                        <p class="exit_chat"><a id="exit" href="#">Exit Current Chat</a></p>
                        <div style="clear:both"></div>
                    </div>    
                    <div id="chatbox"></div>
                     
                    <!-- form submission for message -->
                    <form name="message" action="">
                        <input name="usermsg" type="text" id="usermsg" size="63" />
                        <input name="submitmsg" type="submit"  id="submitmsg" value="Send" />
                    </form>
                </div>
                <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3/jquery.min.js"></script>
                <script type="text/javascript">
                // jQuery Document
                $(document).ready(function(){
                  ///If user submits the form, log the message in the chat_history table using chat_logmessage.php
                  $("#submitmsg").click(function(){ 
                    var clientmsg = $("#usermsg").val();
                    var postString = 
                    $.post("chatLogmessage.php?chatwith=" + $chatto, {text: clientmsg});        
                    $("#usermsg").attr("value", "");
                    return false;
                  });
                  
                  //Load the data containing the chat log by querying the chat_history table through chat_retrieve.php
                  function loadLog(){   

                    $.ajax({
                      type: "GET",
                      url: "chatRetrieve.php",
                      dataType: "html",
                      cache: false,
                      success: function(response){    
                        $("#chatbox").html(response); //Insert chat log into the #chatbox div       
                        },
                    });
                  }
                  
                  //Load the data containing the chat log by querying the chat_history table through chat_query.php
                  function loadLog(){   
                    var oldscrollHeight = $("#chatbox").attr("scrollHeight") - 20; //Scroll height before the request
                    $.ajax({
                      type: "GET",
                      url: "chatRetrieve.php",
                      dataType: "html",
                      cache: false,
                      success: function(response){    
                        $("#chatbox").html(response); //Insert chat log into the #chatbox div 
                        
                        //Auto-scroll     
                        var newscrollHeight = $("#chatbox").attr("scrollHeight") - 20; //Scroll height after the request
                        if(newscrollHeight > oldscrollHeight){
                          $("#chatbox").animate({ scrollTop: newscrollHeight }, 'normal'); //Autoscroll to bottom of div
                        }       
                        },
                    });
                  }
                  
                  setInterval (loadLog, 2500);  //Reload file every 2500 ms or x ms if you w
                });
                </script>
                <?php
                }
                ?>
                <script type="text/javascript">
                // jQuery Document
                $(document).ready(function(){
                  //If user wants to end session - redirect to logout.php
                  $("#loggingout").click(function(){
                    var loggingout = confirm("Are you sure you want to end the session?");
                    if(loggingout ==true){window.location = '../logout.php';}   
                  });

                  // if the user wants to exit the current chat back to the map
                  $("#exit").click(function(){
                    window.location = '../dashboard.php';   
                  });
                });
                </script>

              </div>
              
              <!-- Exchange Manager -->
              <div id="tab-2">                    
                <table>
                <tr>
                <td >
                    Pass Date: <br /><input type="text" id="passDate"><br /><br />
                </td>
                <td>
                </td>
                <td>
                    <label for="spinner">Number of Passes:</label>
                    <input id="spinner" name="value">
                </td>
                </tr>
                <tr>
                <td>
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
                </td>
                <td>
                    <label for="comment">Comment:</label>
                    <textarea id="comment" rows="5" cols="20"></textarea>
                </td>
                <td>
                    <input type="submit" value="Post" id="postExchange">
                    <!-- used to pass netid to on click function for Post -->
                    <input type="hidden" id="netId" value="<?php echo $_SESSION['user']['netId']; ?>">
                </td>
                </tr>
                </table>
               
                <br  />
                Your Current Requests
                <ol id="requestList" class="selectable">
                </ol>
                <input type="submit" value="Delete Selected Requests" onMouseDown="removeSelectedRequests('<?php echo $_SESSION['user']['netId']; ?>')">
                <br  /><br  />
                Your Current Offers
                <ol id="offerList" class="selectable">
                </ol>
                <input type="submit" value="Delete Selected Offers" onMouseDown="removeSelectedOffers('<?php echo $_SESSION['user']['netId']; ?>')">
             </div>
             
             <!-- Current Transactions -->
             <div id="tab-3">
             <div id="transcationAccordion">
                <h3>Trade Dan for 1 pass for Colonia on 05/04/2015</h3>
                <div>
                    <iframe scrolling="no" frameborder="0" height="500" width="700" src="php/chat.php"></iframe>
                </div>
                <h3>Trade Angelica for 1 pass for Colonia on 05/04/2015</h3>
                <div>
                    <iframe scrolling="no" frameborder="0" height="500" width="700" src="php/chat.php"></iframe>
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
  
  </body>
  <div id="invalid-passNum-dialog" title="Invalid Number of Passes">
  <p>
    <span class="ui-icon ui-icon-circle-check" style="float:left; margin:0 7px 50px 0;"></span>
    Please enter a whole number of passes greater than 0.
  </p>
  </div>

	
  </body>

</html>
