<?php
session_start();
ob_start();
error_reporting(0);
@ini_set('display_errors', 0);

//Including Twitter library
require('lib/twitteroauth/twitteroauth/twitteroauth.php');
require('twitterappconfig.php');

// Method below is used to see if the user is logged in. 
// If the user is logged out or if the session has expired, redirect the user to "connect.php" file after clearing session.
if(isset($_REQUEST['oauth_token']) && $_SESSION['oauth_token'] !== $_REQUEST['oauth_token']){
	$_SESSION['status']='old token';
	header('Location: clearsession.php');	
}

//Creating new TwitterOAuth Object
$connection=new TwitterOAuth(consumer, consumer_secret,$_SESSION['oauth_token'],$_SESSION['oauth_token_secret']);

//Getting the access_token to accessing user details.
$access_token=$connection->getAccessToken($_REQUEST['oauth_verifier']);

//Saving the $access_token in the session variable 
$_SESSION['access_token']=$access_token;

//Unsetting unnecessary variable
unset($_SESSION['oauth_token']);
unset($_SESSION['oauth_token_secret']);

//Final check has to be done whether the http_code is 200 or not.
if($connection->http_code==200){
	$_SESSION['status']='verified';
	header('Location: index.php');
}else{
	header('Location: clearsession.php');
}

?>