<?php
require('lib/twitteroauth/twitteroauth/twitteroauth.php');
require('twitterappconfig.php');
$access_token=$_SESSION['access_token'];
$connection=new TwitterOAuth(consumer, consumer_secret,$access_token['oauth_token'],$access_token['oauth_token_secret']);

if(isset($_POST['selected_follower'])){
	$_SESSION['selected_follower']=$_POST['selected_follower'];
	//echo $_POST['selected_follower'];
}
$val=$connection->get('statuses/user_timeline',array('include_rts' => 'true','screen_name' => $_POST['selected_follower']));
//echo "<pre>".print_r($val)."</pre>";
echo json_encode($val);

?>