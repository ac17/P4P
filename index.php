<?php
include('php/database_connect.php');
include('mailer.php');
include('php/signup.php');
include('login.php');
?>
<!DOCTYPE html>
<html>
    <head> 
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title></title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- favicon.ico is in the root directory -->
        <!-- Boilerplate -->
        <link rel="stylesheet" href="css/normalize.css">
        <link rel="stylesheet" href="css/main.css">
        <!-- Bootstrap -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap-theme.min.css">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
        <!-- Custom -->
        <link rel="stylesheet" href="css/index.css">
        <link rel="stylesheet" href="css/global.css">

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
        <script src="js/smoothscroll.js"></script>
    </head>

<body>
<?php
    if (isset($_POST['hiddenSignup']) && $_POST['hiddenSignup'] == 'true') {
        echo <<< SIGNUPCONFIRMATION
        <script type="text/javascript">
            $(window).load(function(){
                $('#signupModal').modal('show');
            });
        </script>
SIGNUPCONFIRMATION;
    }

    if (isset($_POST['hiddenLogin']) && $_POST['hiddenLogin'] == 'true') {
        echo <<< LOGINCONFIRMATION
        <script type="text/javascript">
            $(window).load(function(){
                $('#loginModal').modal('show');
            });
        </script>
LOGINCONFIRMATION;
    }
?>
<!-- Fixed navbar -->
<nav class="navbar navbar-default navbar-fixed-top" role="navigation">
    <div class="container">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapse-1">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          
        </div>
        <a class="navbar-brand" href="#"><b>Passes for Passes</b></a>
        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="navbar-collapse-1">
         
          <ul class="nav navbar-nav navbar-left">
            <li><a href="#howItWorks">About</a></li>
            <li><a href="#contactUs">Contact</a></li>
          </ul>
          <ul class="nav navbar-nav navbar-right">
            <li>
                <button type="button" class="btn btn-default" id="login" <?php if (!isUserLoggedIn()) echo 'data-toggle="modal" data-target="#loginModal"'; ?> >
                    <?php if (isUserLoggedIn()) echo '<a href="dashboard.php">'; ?>
                    Log In
                    <?php if (isUserLoggedIn()) echo '</a>'; ?>
                </button>
            </li>
          </ul>
        </div><!-- /.navbar-collapse -->
    </div>
</nav>

    <!-- Login Modal-->
    <div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Log In</h2>
                </div>
                <div class="modal-body">
                    <?php
                    if (isset($_POST['hiddenLogin']) && $_POST['hiddenLogin'] == 'true') {
                        $err = array();
                        login($_POST["netID"], $_POST["loginPW"], $err);
                        echo '<div class="alert alert-danger" role="alert">'. $err['login_failure'] .'</div>';
                    }
                    ?>
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" id="loginForm">
                        <div class="input-group">
                            <span class="input-group-addon" id="basic-addon1">@</span>
                            <input type="text" class="form-control" placeholder="netID" id="netID" name="netID" aria-describedby="basic-addon1">
                        </div>
                        <br>
                        <div class="input-group">
                            <span class="input-group-addon" id="basic-addon1"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></span>
                            <input type="password" class="form-control" placeholder="Password" id="loginPW" name="loginPW" aria-describedby="basic-addon1">
                        </div><br>
                        <input type="hidden" name="hiddenLogin" id="hiddenLogin" value="true">
                        <button class="btn btn-default" id="loginSubmit" type="submit" form="loginForm" value="Submit">Submit</button>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-close" data-dismiss="modal"><i class="fa fa-close fa-2x"></i></button>
                </div>
            </div>
        </div>
    </div>

    <!-- Sign Up Modal-->
    <div class="modal fade" id="signupModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Sign Up</h2>
                </div>
                <div class="modal-body">
                    <?php
                    $suForm = <<< SIGNUP
                    <form action="{$_SERVER['PHP_SELF']}" method="post" id="signupForm">
                        <input type="text" class="form-control" placeholder="First name" name="fName" id="fName" required><br>
                        <input type="text" class="form-control" placeholder="Last name" name="lName" id="lName" required><br>
                        <input type="text" class="form-control" placeholder="Princeton netID" name="netid" id="netid" required><br>
                        <input type="password" class="form-control" placeholder="Password" name = "pw" id="pw" required><br>
                        <input type="hidden" name="hiddenSignup" id="hiddenSignup" value="true">
                        <button class="btn btn-default" id="signupSubmit" type="submit" form="signupForm" value="Submit">Submit</button>
                    </form>
SIGNUP;
                    $dupUser = "Sorry, you have already signed up!";
                    $welcomeUser = "Thank you for signing up! Check your @princeton.edu email address (including your spam inbox) for a verification email - you must verify your account before using Passes for Passes.";
                    /* sign up new user */
                    if (isset($_POST['hiddenSignup']) && $_POST['hiddenSignup'] == 'true') {
                    $err = 0;
                    $_POST['pw'] = stripslashes($_POST['pw']);
                        if (signup($_POST['netid'], $_POST['fName'], $_POST['lName'], md5($_POST['pw']), $err)) {
                            echo $welcomeUser;
                        }
                        elseif ($err == -1)
                            echo $dupUser;
                    }
                    else
                        echo $suForm;
                    ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-close" data-dismiss="modal"><i class="fa fa-close fa-2x"></i></button>
                </div>
            </div>
        </div>
    </div>

        <!-- Banner -->
        <div class="jumbotron" id="banner">
            <div class="container" id="bannerInner">
                <div class="container" id="bannerInnerInner">
                    <center>
                        <div id="shareTheMoment">
                            <h1><b>Share the Moment</b></h1>
                            <button type="button" class="btn btn-default" id="signup" data-toggle="modal" data-target="#signupModal">Sign Up</button>
                        </div>
                    </center>
                </div>
            </div>
        </div>

        <!-- How it works -->
        <div class="container-fluid jumbotron" id="howItWorks">
            <center><h1>How it Works</h1></center>
            <div class="row">
                <div class="col-md-4">
                    <h2><i class="fa fa-user fa-5x"></i></h2><br>
                    <p><b>Set up your profile.</b></p>
                </div>
                <div class="col-md-4">
                    <h2><i class="fa fa-ticket fa-5x"></i></h2><br>
                    <p><b>Offer and request passes.</b></p>
                </div>
                <div class="col-md-4">
                    <h2><i class="fa fa-exchange fa-5x"></i></h2><br>
                    <p><b>Meet up to exchange!</b></p>
                </div>
            </div>
        </div>

        <!-- Contact Us -->
        <div class="container-fluid jumbotron" id="contactUs">
            <div class="row">
                <div class="col-md-6">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3040.925934636111!2d-74.65144800000006!3d40.34398900000001!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c3e6d8cd98b6e9%3A0x2ba7ed6fa90024f!2sPrinceton+University!5e0!3m2!1sen!2sus!4v1428258829494" width="100%" height="100%" frameborder="0" style="border:0"></iframe>
                </div>
                <div class="col-md-6">
                    <h2>Comments? Questions? Concerns? Tell us below.</h2>
                    <form method="post" id="contactForm">
                        <input type="text" name="firstName" id="firstName" placeholder="First Name"><br><br>
                        <input type="text" name="lastName" id="lastName" placeholder="Last Name"><br><br>
                        <input type="email" name="emailAdd" id="emailAdd" placeholder="jsmith@princeton.edu"><br><br>
                        <textarea rows="5" cols="40">Message</textarea><br><br>
                        <div class="g-recaptcha" data-sitekey="6LdV3QQTAAAAADzwPlAoGytMDhlc0E9G1Pjp_f7_"></div>
                        <button class="btn btn-default" id="contactSubmit" type="submit" form="contactForm" value="Submit">Submit</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="footer">
            <div class="container" id="footerInner">
                <h2><i class="fa fa-facebook-square fa-lg"></i>
                    <i class="fa fa-twitter-square fa-lg"></i>
                    <i class="fa fa-instagram fa-lg"></i>
                </h2>
            </div>
        </footer>

        <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
        <script src="js/ie10-viewport-bug-workaround.js"></script>
    </body>
</html>