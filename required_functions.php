<?php
// on the beginning of your script save original memory limit
//$original_mem = ini_get('memory_limit');
// then set it to the value you think you need (experiment)
require_once 'db_lib.php';
require_once 'twitterappconfig.php';
function getAccountInformation(){
	
	if(isset($_SESSION['account_verify_credentials'])){
		return $_SESSION['account_verify_credentials'];
	}else{
		
		$access_token=$_SESSION['access_token'];
		$connection=new TwitterOAuth(consumer, consumer_secret,$access_token['oauth_token'],$access_token['oauth_token_secret']);
		
		//Making an API Call to fetch Account information of Currently logged in user
		$user_info=$connection->get('account/verify_credentials');
		$_SESSION['account_verify_credentials']=$user_info;
		
		return $user_info;
	}
	
}
function getTweetList($user_info){
	$user_info=getAccountInformation();
	$oDB=new db();
	//echo "<pre>";
	//print_r($connection);
	//print_r($user_info);
	//echo "</pre>";
	$query='SELECT last_update FROM registered_users WHERE screen_name="'.$user_info->screen_name.'"';
	//echo $query;
	$result=$oDB->select($query);
	$row=mysqli_fetch_assoc($result);
	// If currently logged in user is new user
	if (count($row)==0) {
		//echo $query;
		//$query='';
		//echo "Creating New Data";
		//Creating cache of Currently logged in users account information
		$field_values='user_id="'.$user_info->id.'",'.
				'screen_name="'.$user_info->screen_name.'",'.
				'name="'.$user_info->name.'",'.
				'profile_image_url="'.urlencode($user_info->profile_image_url).'",'.
				'location="'.$user_info->location.'",'.
				'url="'.urlencode($user_info->url).'",'.
				'description="'.$user_info->description.'",'.
				'created_at="'.$user_info->created_at.'",'.
				'followers_count="'.$user_info->followers_count.'",'.
				'friends_count="'.$user_info->friends_count.'",'.
				'statuses_count="'.$user_info->statuses_count.'",'.
				'time_zone="'.$user_info->time_zone.'",'.
				'last_update="'.date('Y-m-d H:i:s').'"';
		$oDB->insert('registered_users',$field_values);
	
		//Creating Cache of All followers for the first time
		$suggested_users_list=getAllFollowers();
	
		for($cnt=0;$cnt<count($suggested_users_list);$cnt++){
			$raw_follower = base64_encode(serialize($suggested_users_list[$cnt]));
			$field_values='user_id="'.$suggested_users_list[$cnt]->id.'",'.
					'registered_users_id="'.$user_info->id.'",'.
					'raw_follower="'.$raw_follower.'"';
			$oDB->insert('followers_cache', $field_values);
		}
	
		//Creating cache of all tweets for the first time
		$val=getAllTweets();
	
		for($cnt=0;$cnt<count($val);$cnt++){
			$raw_tweet = base64_encode(serialize($val[$cnt]));
			$field_values='tweet_id="'.$val[$cnt]->id.'",'.
					'registered_users_id="'.$user_info->id.'",'.
					'raw_tweet="'.$raw_tweet.'"';
			$oDB->insert('json_cache', $field_values);
	
		}
		$oDB->close();
		return $val;
		//echo "<pre>";
		////print_r($suggested_users_list);echo "</pre>";
	}else{
		//echo "Using old data";
		//echo "Row count: ".count($row);
	
		//Calculating the duration of cached data
		$diff=abs(strtotime(date('Y-m-d H:i:s'))-strtotime($row['last_update']));
		$diff=abs( ( $diff / 60 ) % 60 );
	
		//echo "<br />Diff in minutes: ".$diff."<br />";
	
		//Checking if the cached data is older than interval or not
		// Value of a variable "interval" has been defined in "twtiterappconfig.php" file
		if($diff<interval){
			//echo "Using cached data";
			//Cached data is newer
			//Fetching followers data from mysql table
			$query='SELECT * FROM json_cache WHERE registered_users_id="'.$user_info->id.'"';
			//echo "<br />".$query;
			$result=$oDB->select($query);
			//				$row=mysqli_fetch_array($result);
	
			$cnt=0;
			$ret_row="";
			while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
				$ret_row[$cnt]=unserialize(base64_decode($row['raw_tweet']));
				$cnt++;
			}
			$oDB->close();
			//Returning it in a proper usable format
			return $ret_row;
	
		}else{
			//echo "Creating new cache";
			//Cached data is older and expired
			//Deleting old cached data of tweets and followers
			$oDB->select("DELETE FROM followers_cache WHERE registered_users_id='".$user_info->id."'");
			$oDB->select("DELETE FROM json_cache WHERE registered_users_id='".$user_info->id."'");
				
			//Creating new cached copy of followers
			$suggested_users_list=getAllFollowers();
				
			for($cnt=0;$cnt<count($suggested_users_list);$cnt++){
				$raw_follower = base64_encode(serialize($suggested_users_list[$cnt]));
				$field_values='user_id="'.$suggested_users_list[$cnt]->id.'",'.
						'registered_users_id="'.$user_info->id.'",'.
						'raw_follower="'.$raw_follower.'"';
				$oDB->insert('followers_cache', $field_values);
			}
				
				
			//Creating new cached copy of tweets
			$val=getAllTweets();
				
			for($cnt=0;$cnt<count($val);$cnt++){
				$raw_tweet = base64_encode(serialize($val[$cnt]));
				$field_values='tweet_id="'.$val[$cnt]->id.'",'.
						'registered_users_id="'.$user_info->id.'",'.
						'raw_tweet="'.$raw_tweet.'"';
				$oDB->insert('json_cache', $field_values);
					
			}
			
			$field_values='';
			$field_values='last_update="'.date('Y-m-d H:i:s').'"';
			$where_clause='user_id="'.$user_info->id.'"';
			$oDB->update('registered_users',$field_values,$where_clause);
			$oDB->close();
			return $val;
				
		}
	}	
}
function getFollowersList($user_info){
	$oDB=new db();
	//echo "<pre>";
	//print_r($connection);
	//print_r($user_info);
	//echo "</pre>";
	$query='SELECT last_update FROM registered_users WHERE screen_name="'.$user_info->screen_name.'"';
	//echo $query;
	$result=$oDB->select($query);
	$row=mysqli_fetch_assoc($result);
	// If currently logged in user is new user
	if (count($row)==0) {
		//echo $query;
		//$query='';
		//echo "Creating New Data";
		//Creating cache of Currently logged in users account information
		$field_values='user_id="'.$user_info->id.'",'.
				'screen_name="'.$user_info->screen_name.'",'.
				'name="'.$user_info->name.'",'.
				'profile_image_url="'.urlencode($user_info->profile_image_url).'",'.
				'location="'.$user_info->location.'",'.
				'url="'.urlencode($user_info->url).'",'.
				'description="'.$user_info->description.'",'.
				'created_at="'.$user_info->created_at.'",'.
				'followers_count="'.$user_info->followers_count.'",'.
				'friends_count="'.$user_info->friends_count.'",'.
				'statuses_count="'.$user_info->statuses_count.'",'.
				'time_zone="'.$user_info->time_zone.'",'.
				'last_update="'.date('Y-m-d H:i:s').'"';
		$oDB->insert('registered_users',$field_values);
	
		//Creating Cache of All followers for the first time
		$suggested_users_list=getAllFollowers();
		
		for($cnt=0;$cnt<count($suggested_users_list);$cnt++){
			$raw_follower = base64_encode(serialize($suggested_users_list[$cnt]));
			$field_values='user_id="'.$suggested_users_list[$cnt]->id.'",'.
					'registered_users_id="'.$user_info->id.'",'.
					'raw_follower="'.$raw_follower.'"';
			$oDB->insert('followers_cache', $field_values);
		}
		
		//Creating cache of all tweets for the first time
		$val=getAllTweets();
		
		for($cnt=0;$cnt<count($val);$cnt++){
			$raw_tweet = base64_encode(serialize($val[$cnt]));
			$field_values='tweet_id="'.$val[$cnt]->id.'",'.
					'registered_users_id="'.$user_info->id.'",'.
					'raw_tweet="'.$raw_tweet.'"';
			$oDB->insert('json_cache', $field_values);
				
		}
		
		$oDB->close();
		//echo "<pre>";print_r($val);exit();
		return $suggested_users_list;
		//echo "<pre>";
		////print_r($suggested_users_list);echo "</pre>";
	}else{		
		//echo "Using old data";
		//echo "Row count: ".count($row);
		
		//Calculating the duration of cached data 
		$diff=abs(strtotime(date('Y-m-d H:i:s'))-strtotime($row['last_update']));
		$diff=abs( ( $diff / 60 ) % 60 );
		
		//echo "<br />Diff in minutes: ".$diff."<br />";
		
		//Checking if the cached data is older than interval or not
		// Value of a variable "interval" has been defined in "twtiterappconfig.php" file
		if($diff<interval){
				//echo "Using cached data";
				//Cached data is newer
				//Fetching followers data from mysql table 
				$query='SELECT * FROM followers_cache WHERE registered_users_id="'.$user_info->id.'"';
				//echo "<br />".$query;
				$result=$oDB->select($query);
//				$row=mysqli_fetch_array($result);
				
				$cnt=0;
				$ret_row="";
				while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
					$ret_row[$cnt]=unserialize(base64_decode($row['raw_follower']));
					$cnt++;
				}
				$oDB->close();
				//Returning it in a proper usable format
				return $ret_row;
				
		}else{
			//echo "Creating new cache";
			//Cached data is older and expired
			//Deleting old cached data of tweets and followers
			$oDB->select("DELETE FROM followers_cache WHERE registered_users_id='".$user_info->id."'");
			$oDB->select("DELETE FROM json_cache WHERE registered_users_id='".$user_info->id."'");
			
			//Creating new cached copy of followers
			$suggested_users_list=getAllFollowers();
			
			for($cnt=0;$cnt<count($suggested_users_list);$cnt++){
				$raw_follower = base64_encode(serialize($suggested_users_list[$cnt]));
				$field_values='user_id="'.$suggested_users_list[$cnt]->id.'",'.
						'registered_users_id="'.$user_info->id.'",'.
						'raw_follower="'.$raw_follower.'"';
				$oDB->insert('followers_cache', $field_values);
			}
			
			
			//Creating new cached copy of tweets
			$val=getAllTweets();
			
			for($cnt=0;$cnt<count($val);$cnt++){
				$raw_tweet = base64_encode(serialize($val[$cnt]));
				$field_values='tweet_id="'.$val[$cnt]->id.'",'.
						'registered_users_id="'.$user_info->id.'",'.
						'raw_tweet="'.$raw_tweet.'"';
				$oDB->insert('json_cache', $field_values);
					
			}
			
			$field_values='';
			$field_values='last_update="'.date('Y-m-d H:i:s').'"';
			$where_clause='user_id="'.$user_info->id.'"';
			$oDB->update('registered_users',$field_values,$where_clause);
			$oDB->close();
			return $suggested_users_list;
			
		}
	}		
}
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
	/*
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
	*/
	
	// Follow this like for better understanding:  https://dev.twitter.com/docs/working-with-timelines
	//Fetching tweets from user	
	
	$access_token=$_SESSION['access_token'];
	$connection=new TwitterOAuth(consumer, consumer_secret,$access_token['oauth_token'],$access_token['oauth_token_secret']);
	
	//Fetching tweets from user
	$val=$connection->get('statuses/user_timeline',array('include_rts' => 'true','count' => 200, 'screen_name' => encryptDecrypt('decrypt',$_GET['j'])));
	//echo "<pre>";
	//echo "<br />".print_r($val);
	/*
	if($cache === FALSE) {
		
		$access_token=$_SESSION['access_token'];
		$connection=new TwitterOAuth(consumer, consumer_secret,$access_token['oauth_token'],$access_token['oauth_token_secret']);
		
		//Fetching tweets from user
		$val=$connection->get('statuses/user_timeline',array('include_rts' => 'true','count' => 200, 'screen_name' => encryptDecrypt('decrypt',$_GET['j'])));
		
		// If there's a ", ', :, or ; in object elements, serialize() gets corrupted
		// You should also use base64_encode() before saving this
		$raw_tweet = base64_encode(serialize($val));
		
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
	*/
	//Getting Largest ID
	$since_id=$val[0]->id;
	//Getting Smallest ID
	$max_id=$val[count($val)-1]->id;
	//echo "<pre>";
//	//echo "<br /> Since ID: ".$since_id;
	//echo "<br /> Max ID: ".$max_id;
	//echo "<br />count: ".count($val);
	//exit();
	if(count($val)==200 || count($val)==199){
		
		while(true){
				
			//Getting old tweets containing ids lesser then max_id
			$new_var=$connection->get('statuses/user_timeline',array('include_rts' => 'true','count' => 200,'max_id' => $max_id, 'screen_name' => encryptDecrypt('decrypt',$_GET['j'])));
			$new_max_id=$new_var[count($new_var)-1]->id;
		
			//echo "<br />";
			//echo "<br /> New Max ID: ".$new_max_id;
			//echo "<br />";
		
			//print_r($new_var);
			//exit();
			//Comparing new_max_id with max_id to see whether we've reached at the end or not
			if($new_max_id==$max_id || $new_max_id=='' || $new_max_id==null){
				break;
			}else{
				//Decreasing new max_id by 1 Because of redundancy of Last tweet
				$max_id=$new_max_id-1;
			}
			
			/*$new_var=$connection->get('statuses/user_timeline',array('include_rts' => 'true','count' => 200,'max_id' => $max_id, 'screen_name' => encryptDecrypt('decrypt',$_GET['j'])));
			$new_max_id=$new_var[count($new_var)-1]->id;
			
			echo "<br />";
			echo "<br /> New Max ID: ".$new_max_id;
			echo "<br />";
			
			print_r($new_var);
			exit();*/
			//Merging two arrays
			$val=array_merge($val,$new_var);
		
			//echo "<br /> Since ID: ".$since_id;
		
		}
		
		$new_var=$connection->get('statuses/user_timeline',array('include_rts' => 'true','count' => 200,'since_id' => $since_id, 'screen_name' => encryptDecrypt('decrypt',$_GET['j'])));
		$new_max_id=$new_var[count($new_var)-1]->id;
		
		$val=array_merge($val,$new_var);
		
	}	
	//echo "From Function<pre>";
	//print_r($val);exit();
	
	return $val;
}
function getAllFollowers(){
	
	$access_token=$_SESSION['access_token'];
	$connection=new TwitterOAuth(consumer, consumer_secret,$access_token['oauth_token'],$access_token['oauth_token_secret']);
	
	//Using cursor to navigate throught all the followers
	$suggested_users_list=$connection->get('followers/list',array('count' => '200'));
	//$next_cursor=$suggested_users_list[count($suggested_users_list)-1]->id;
	$next_cursor=$suggested_users_list->next_cursor;
	$suggested_users_list=$suggested_users_list->users;
	while($next_cursor!=0){
		$new_suggested_users_list=$connection->get('followers/list',array('cursor'=>$next_cursor));
		$next_cursor=$new_suggested_users_list->next_cursor;
		$new_suggested_users_list=$new_suggested_users_list->users;
		//print_r($new_suggested_users_list);
		$suggested_users_list=array_merge($suggested_users_list,$new_suggested_users_list);
	}

	return $suggested_users_list;
}

?>