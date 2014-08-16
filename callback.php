<?php

session_start();
//require('lib/twitteroauth/twitteroauth/OAuth.php');
require('lib/twitteroauth/twitteroauth/twitteroauth.php');
require('twitterappconfig.php');

if(isset($_REQUEST['oauth_token']) && $_SESSION['oauth_token'] !== $_REQUEST['oauth_token']){
	$_SESSION['status']='old token';
	header('Location: clearsession.php');	
}

$connection=new TwitterOAuth(consumer, consumer_secret,$_SESSION['oauth_token'],$_SESSION['oauth_token_secret']);

$access_token=$connection->getAccessToken($_REQUEST['oauth_verifier']);

$_SESSION['access_token']=$access_token;

unset($_SESSION['oauth_token']);
unset($_SESSION['oauth_token_secret']);

if($connection->http_code==200){
	$_SESSION['status']='verified';
	header('Location: index.php');
}else{
	header('Location: clearsession.php');
}

?>