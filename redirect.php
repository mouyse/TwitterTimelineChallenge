<?php
session_start();
ob_start();
error_reporting(0);
@ini_set('display_errors', 0);

//Including Twitter library
require('lib/twitteroauth/twitteroauth/twitteroauth.php');
require('twitterappconfig.php');

//Creating new TwitterOAuth Object
$connection=new TwitterOAuth(consumer,consumer_secret);

//Getting a Request token from Twitter
$request_token=$connection->getRequestToken(oauth_callback);

// Saving a token variable in a session variable
$_SESSION['oauth_token']=$token=$request_token['oauth_token'];
$_SESSION['oauth_token_secret']=$request_token['oauth_token_secret'];
 
// Getting the HTTP response from Twitter
switch ($connection->http_code){
	case 200:
		//Getting the login URL to redirect the user 
		$url=$connection->getAuthorizeURL($token);
		header('Location: '.$url);
		break;
	
	default:
		echo "Oops ! Something went wrong. Check in the twitter docs for this error : ".$connection->http_code;
}

?>