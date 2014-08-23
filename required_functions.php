<?php
//http://naveensnayak.wordpress.com/2013/03/12/simple-php-encrypt-and-decrypt/
function encryptDecrypt($action, $string) {
	$output = false;

	$encrypt_method = "AES-256-CBC";
	$secret_key = 'This is my secret key';
	$secret_iv = 'This is my secret iv';

	// hash
	$key = hash('sha256', $secret_key);

	// iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
	$iv = substr(hash('sha256', $secret_iv), 0, 16);

	if( $action == 'encrypt' ) {
		$output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
		$output = base64_encode($output);
	}
	else if( $action == 'decrypt' ){
		$output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
	}

	return $output;
}
function getAllTweets(){
	
	$access_token=$_SESSION['access_token'];
	$connection=new TwitterOAuth(consumer, consumer_secret,$access_token['oauth_token'],$access_token['oauth_token_secret']);
	
	// Follow this like for better understanding:  https://dev.twitter.com/docs/working-with-timelines
	//Fetching tweets from user
	
	//Initializing required variable for wokring with timeline
	$max_id=$since_id=0;
	$new_max_id=0;
	
	//Fetching tweets from user
	$val=$connection->get('statuses/user_timeline',array('include_rts' => 'true','count' => 200, 'screen_name' => encryptDecrypt('decrypt',$_GET['j'])));
	
	//Getting Largest ID
	$since_id=$val[0]->id;
	//Getting Smallest ID
	$max_id=$val[count($val)-1]->id;
	
	while(true){
	
		//Getting old tweets containing ids lesser then max_id
		$new_var=$connection->get('statuses/user_timeline',array('include_rts' => 'true','count' => 200,'max_id' => $max_id, 'screen_name' => encryptDecrypt('decrypt',$_GET['j'])));
		$new_max_id=$new_var[count($new_var)-1]->id;
	
		//Comparing new_max_id with max_id to see whether we've reached at the end or not
		if($new_max_id==$max_id){
			break;
		}else{
			//Decreasing new max_id by 1 Because of redundancy of Last tweet
			$max_id=$new_max_id-1;
		}
	
		//Merging two arrays
		$val=array_merge($val,$new_var);
	
	}
	return $val;
}

?>