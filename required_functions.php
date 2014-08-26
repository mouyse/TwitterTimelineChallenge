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
	
	$cache = FALSE; //Assume the cache is empty
	$cPath = 'cache/tweets.cache';
	if(file_exists($cPath)) {
		$modtime = filemtime($cPath);
		$timeago = time() - 1800; //30 minutes ago in Unix timestamp format
		if($modtime < $timeago) {
			$cache = FALSE; //Set to false just in case as the cache needs to be renewed
		} else {
			$cache = TRUE; //The cache is not too old so the cache can be used.
		}
	}
		
	$access_token=$_SESSION['access_token'];
	$connection=new TwitterOAuth(consumer, consumer_secret,$access_token['oauth_token'],$access_token['oauth_token_secret']);
	
	// Follow this like for better understanding:  https://dev.twitter.com/docs/working-with-timelines
	//Fetching tweets from user
	
	//Initializing required variable for wokring with timeline
	$max_id=$since_id=0;
	$new_max_id=0;
	if($cache === FALSE) {
		//Fetching tweets from user
		$val=$connection->get('statuses/user_timeline',array('include_rts' => 'true','count' => 200, 'screen_name' => encryptDecrypt('decrypt',$_GET['j'])));
		
		//Let's save our data into the cache
		$fp = fopen($cPath, 'w');
		if(flock($fp, LOCK_EX)) {
			fwrite($fp, json_encode($val));
			flock($fp, LOCK_UN);
		}
		fclose($fp);
	}else {
		//echo "<br />Used Cached version<br />";
	    //cache is TRUE let's load the data from the cache.
	    $val = file_get_contents($cPath);
		//Decoding
		$val=json_decode($val);
	}	
	
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