<?php
include_once('php/database_connect.php');
include_once('login.php');
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
        <link rel="stylesheet" href="css/global.css">
        <link rel="stylesheet" href="css/loginUser.css">

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>

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
        <a class="navbar-brand" href="index.php"><b>Passes for Passes</b></a>
        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="navbar-collapse-1">
         
          <ul class="nav navbar-nav navbar-left">
            <li><a href="#">About</a></li>
            <li><a href="#">Contact</a></li>
          </ul>
        </div><!-- /.navbar-collapse -->
    </div>
</nav>

<!-- Log In -->
<div class="jumbotron" id="banner">
    <div class="container" id="bannerInner">
        <div class="container" id="bannerInnerInner">
            <center>
                <div class="container">
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" id="loginForm">
                       <center><h1><b>Log In</b></h1></center>
                        <?php
                        if (isset($_POST['hiddenLogin']) && $_POST['hiddenLogin'] == 'true') {
                            $err = array();
                            login($_POST["netID"], $_POST["loginPW"], $err);
                            echo '<div class="alert alert-danger" role="alert">'. $err['login_failure'] .'</div>';
                        }
                        ?>
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
                        <center><button class="btn btn-default" id="loginSubmit" type="submit" form="loginForm" value="Submit">Submit</button></center>
                    </form>
                </div>
            </center>
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