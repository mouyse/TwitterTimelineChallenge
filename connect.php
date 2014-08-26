<?php
session_start();
ob_start();
error_reporting(0);
@ini_set('display_errors', 0);

//Check if access_token is actually verified
if(!empty($_SESSION['access_token']) && !empty($_SESSION['access_token']['oauth_token']) && !empty($_SESSION['access_token']['oauth_token_secret'])){
	header("Location: index.php");
} 
?>
<!DOCTYPE html>
<html>
<head>
<title>Twitter Timeline Challenge</title>
<meta charset="utf-8">
<meta name="description" content="Login to your twitter account to view your tweets,followers">
<meta name="author" content="Mouyse">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
<!-- Optional theme -->
<link rel="stylesheet" href="bootstrap/css/bootstrap-theme.min.css">
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="bootstrap/js/bootstrap.min.js"></script>

<!-- Custom CSS -->
<link rel="stylesheet" href="css/styles.css">

<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
</head>

<body>
<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
  <div class="container">
	<div class="navbar-header">
	  <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
		<span class="sr-only">Toggle navigation</span>
		<span class="icon-bar"></span>
		<span class="icon-bar"></span>
		<span class="icon-bar"></span>
	  </button>
	  <a class="navbar-brand" href="#">Twitter Timeline Challenge</a>
	</div>
	
  </div>
</div>
<div class="container">
      <div class="starter-template">
	   <div class="row">
        <div class="col-sm-5">
			<div class="panel panel-primary">
				<div class="panel-heading">
				  <h3 class="panel-title">View Tweets,Followers</h3>
				</div>
				<div class="panel-body">				 
				  <a href='redirect.php'><button type="button" class="btn btn-lg btn-primary"><span class="icon-facebook"></span>Login with Twitter</button></a> 
				</div>
		  </div>
		 </div>
		</div>
    </div>
</body>

</html> 