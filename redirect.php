<?php

session_start();
//require('lib/twitteroauth/twitteroauth/OAuth.php');
require('lib/twitteroauth/twitteroauth/twitteroauth.php');
require('twitterappconfig.php');

$connection=new TwitterOAuth(consumer,consumer_secret);

$request_token=$connection->getRequestToken(oauth_callback);
//print_r($request_token);exit();
$_SESSION['oauth_token']=$token=$request_token['oauth_token'];
//echo $connection->http_code;exit();
$_SESSION['oauth_token_secret']=$request_token['oauth_token_secret'];

switch ($connection->http_code){
	case 200:
		$url=$connection->getAuthorizeURL($token);
		//echo $url;exit();
		header('Location: '.$url);
		break;
	
	default:
		echo "Oops ! Something went wrong. Check in the twitter docs for this error : ".$connection->http_code;
}

?>