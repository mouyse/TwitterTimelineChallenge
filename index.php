<<<<<<< HEAD
<?php
session_start();
require('lib/twitteroauth/twitteroauth/twitteroauth.php');
require('twitterappconfig.php');
//Including functions file which are mendatory
require_once('required_functions.php');

if(empty($_SESSION['access_token']) || empty($_SESSION['access_token']['oauth_token']) || empty($_SESSION['access_token']['oauth_token_secret'])){
	header("Location: clearsession.php");
}

$access_token=$_SESSION['access_token'];
$connection=new TwitterOAuth(consumer, consumer_secret,$access_token['oauth_token'],$access_token['oauth_token_secret']);

//Fetching Home Timeline of currently logged in user
$home_timeline=$connection->get('statuses/home_timeline',array('include_rts' => 'true'));
//Fetching Account information of Currently logged in user
$user_info=$connection->get('account/verify_credentials');

?>
<!DOCTYPE html>
=======
<?php session_start(); ?><!DOCTYPE html>
>>>>>>> d92ecaa395f6895ce6889de9abfc6ccf882ad23c
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
<link rel="stylesheet" href="http://code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css">
<script src="http://code.jquery.com/jquery-1.10.2.js"></script>
<script src="http://code.jquery.com/ui/1.11.1/jquery-ui.js"></script>

<!-- jQuery Slider -->

<script src="lib/LeanSlider/modernizr-2.6.1.min.js"></script>    
<script src="lib/LeanSlider/lean-slider.js"></script>
<link rel="stylesheet" href="lib/LeanSlider/lean-slider.css" type="text/css" />
<link rel="stylesheet" href="lib/LeanSlider/sample-styles.css" type="text/css" />

<<<<<<< HEAD
<script type="text/javascript">
    $(document).ready(function() {
        var slider = $('#slider').leanSlider({
            directionNav: '#slider-direction-nav',
            controlNav: '#slider-control-nav'
        });
    });
</script>

=======
>>>>>>> d92ecaa395f6895ce6889de9abfc6ccf882ad23c

</head>

<body>
<<<<<<< HEAD
<?php 
$suggested_users_list=$connection->get('followers/list',array('count' => '200'));
$current_user_name=$user_info->name;
=======
<?php
require('lib/twitteroauth/twitteroauth/twitteroauth.php');
require('twitterappconfig.php');

if(empty($_SESSION['access_token']) || empty($_SESSION['access_token']['oauth_token']) || empty($_SESSION['access_token']['oauth_token_secret'])){
	header("Location: clearsession.php");
}

$access_token=$_SESSION['access_token'];

$connection=new TwitterOAuth(consumer, consumer_secret,$access_token['oauth_token'],$access_token['oauth_token_secret']);

$home_timeline=$connection->get('statuses/home_timeline',array('include_rts' => 'true'));
$user_info=$connection->get('account/verify_credentials');
/*$suggested_users_category=$connection->get('users/suggestions');
$upper_limit=count($suggested_users_category);
//echo "upper limit: ".$upper_limit;
$random_category_no=rand(0,$upper_limit);
//echo "random category: ".$random_category;
//echo "Random Slug:".$suggested_users[$random_category]->slug;
//echo "Random CAte".$rand_user_category;
$random_slug=$suggested_users_category[$random_category_no]->slug;
//echo "<br />";
//print_r($random_slug);
//echo "<br />";
$random_slug='users/suggesstion/'.$random_slug.'/members';*/
//echo "Random Slug: ".$random_slug;
$suggested_users_list=$connection->get('followers/list');
//echo "<pre>".print_r($suggested_users_list)."</pre>";
//$suggested_users[rand(0,count($suggested_users))])
$current_user_name=$user_info->name;
//$autocomplete_user_tweet=$connection->get('statuses/user_timeline',array('include_rts' => 'true','screen_name' => 'mouyse'));
//echo "<pre>";
//print_r($autocomplete_user_tweet);
//echo "</pre>";

//$suggested_users_list=$suggested_users_list['users'];
//echo "<pre>";
//print_r($suggested_users_list->users[0]->screen_name);
//echo "</pre>";
//echo "<pre>".print_r($home_timeline,true)."</pre>";
//echo "<pre>".print_r($user_info,true)."</pre>";
>>>>>>> d92ecaa395f6895ce6889de9abfc6ccf882ad23c
?>

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

<div class="jumbotron">
<<<<<<< HEAD
 <h1>Hello, <?php echo ucfirst($user_info->name);?></h1>
 <p></p>
 <p>
 	<a href="http://twitter.com/<?php echo $user_info->screen_name; ?>" class="btn btn-primary btn-lg" target="_blank" role="button"><span class="glyphicon glyphicon-user"></span> View My Twitter &raquo;</a>
 	<p>
 		
 		<span class="label label-danger">Download My All Tweets &raquo;</span> 
 		<a href="process.php?p=<?php echo encryptDecrypt('encrypt',$user_info->screen_name);?>" class="btn btn-primary btn-lg" role="button" target="_blank">PDF</a>
 		<a href="process.php?j=<?php echo encryptDecrypt('encrypt',$user_info->screen_name);?>" class="btn btn-primary btn-lg" role="button" target="_blank">JSON</a>
 		<a href="process.php?c=<?php echo encryptDecrypt('encrypt',$user_info->screen_name);?>" class="btn btn-primary btn-lg" role="button" target="_blank">CSV</a>
 		<a href="process.php?e=<?php echo encryptDecrypt('encrypt',$user_info->screen_name);?>" class="btn btn-primary btn-lg" role="button" target="_blank">XLS</a>
 		<a href="process.php?x=<?php echo encryptDecrypt('encrypt',$user_info->screen_name);?>" class="btn btn-primary btn-lg" role="button" target="_blank">XML</a>
 	</p>
 </p>
</div>
=======
 <h1>Hello, <?php echo $user_info->name;?></h1>
 <p></p>
 <p><a href="http://twitter.com/<?php echo $user_info->screen_name; ?>" class="btn btn-primary btn-lg" target="_blank" role="button">View Your Twitter &raquo;</a></p>
</div>

>>>>>>> d92ecaa395f6895ce6889de9abfc6ccf882ad23c
<div class="jumbotron">
	<h3 id="thumbnails-custom-content" style="text-align: left;">Latest Tweets from <?php echo $current_user_name."'s Home";?></h3>
	<div id="dynamic_slider">  
		<div class="slider-wrapper">
			<div id="slider" style='height:100px;'>
				<?php for($i=0;$i<10;$i++){?>
				<div class="slide<?php echo ($i+1);?>" style='text-align:center;'>
					<table id="tweet_table">
						<tr>
							<td>
								<!-- <a href="http://twitter.com/<?php echo $home_timeline[$i]->user->screen_name;?>"><img src="<?php echo $home_timeline[$i]->user->profile_image_url;?>" class="tweet_feed_image"/></a> -->
								<img src="<?php echo $home_timeline[$i]->user->profile_image_url;?>" class="tweet_feed_image"/>
							</td>
							<td style="padding-left:20px;">
								<?php echo $home_timeline[$i]->text; ?>
							</td>
						</tr>
					</table>
				</div>            
				<?php } ?>                   
			</div>
		  <div id="slider-direction-nav"></div>
		  <div id="slider-control-nav"></div>
		 </div>
	 </div>
</div>
<<<<<<< HEAD

 	 <script>
$(function() {
var availableFollowers = [
	<?php for($uc=0;$uc<count($suggested_users_list->users);$uc++){?>
<?php echo '{ value: "'.$suggested_users_list->users[$uc]->name.'",label: "'.$suggested_users_list->users[$uc]->name.'",screen_name: "'.$suggested_users_list->users[$uc]->screen_name.'"},';?>
<?php } ?>
	];
	$( "#followers" ).autocomplete({
		source: availableFollowers,
	    select:function(e,ui) {			    	
    		$.ajax({
    			url: "http://shahinfosolutions.com/EW/TwitterTimelineChallenge/process.php", 				//This is the page where you will handle your SQL insert
    			data: "selected_follower="+ui.item.screen_name,
    			type: "POST",
    			crossDomain: true,
    			async: false,
    		   	success: function(msg){
    				//alert(msg);
    				$("#dynamic_slider").html("<div class='slider-wrapper'><div id='slider' style='height:100px;'></div><div id='slider-direction-nav'></div><div id='slider-control-nav'></div></div>");
    				$("#thumbnails-custom-content").html("Latest Tweets from "+ui.item.value+"'s Home");
    				var counter=0; 			
    				$.each(JSON.parse(msg), function(idx, obj) {
	    				counter=counter+1;
	    				if(counter == 11){return false;}
	    				
	    				//alert(JSON.stringify(obj, null, 4));
	    				var imgUrl = obj.user.profile_image_url;
	    				if((typeof obj.retweeted_status  != "undefined") && (typeof obj.retweeted_status.user  != "undefined") && (typeof obj.retweeted_status.user.profile_image_url  != "undefined") && obj.retweeted_status.user.profile_image_url != ''){
	    					imgUrl = obj.retweeted_status.user.profile_image_url;
		    			}
	    					//$("#slider").append("<div class='slide"+counter+"' style='text-align:center;'><table id='tweet_table'><tr><td><img src='"+obj.retweeted_status.user.profile_image_url+"' class='tweet_feed_image'/></td><td style='padding-left:20px;'>"+obj.retweeted_status.text+"</td></tr></table></div>");
	    				//}else{
	    					$("#slider").append("<div class='slide"+counter+"' style='text-align:center;'><table id='tweet_table'><tr><td><img src='"+imgUrl+"' class='tweet_feed_image'/></td><td style='padding-left:20px;'>"+obj.text+"</td></tr></table></div>");
	    				//}
	    				//alert(obj.text + counter);		    					
	    			});
    				//$("#slider-wrapper").append("<div id='slider-direction-nav'></div><div id='slider-control-nav'></div>");
    				//$("#slider-wrapper").append("<div id='slider-direction-nav'></div><div id='slider-control-nav'></div>");		    				
    				var slider = $('#slider').leanSlider({
    		            directionNav: '#slider-direction-nav',
    		            controlNav: '#slider-control-nav'
    		        });
    			}
    		});
	    	return false;
		     
	    }
	});
	
});
</script>
<div id="user_follower_list">
	<h3 id="thumbnails-custom-content">Your Followers</h3>
	<div class="input-group input-group-lg">
	  <span class="input-group-addon">@</span>
	  <input type="text" class="form-control" placeholder="Search & View your follower's Home:" id="followers">
	</div>
		
=======
    <script type="text/javascript">
    $(document).ready(function() {
        var slider = $('#slider').leanSlider({
            directionNav: '#slider-direction-nav',
            controlNav: '#slider-control-nav'
        });
    });
    </script>
 	 <script>
		$(function() {
		var availableFollowers = [
			<?php for($uc=0;$uc<count($suggested_users_list->users);$uc++){?>
			<?php echo '{ value: "'.$suggested_users_list->users[$uc]->name.'",label: "'.$suggested_users_list->users[$uc]->name.'",screen_name: "'.$suggested_users_list->users[$uc]->screen_name.'"},';?>
			<?php } ?>
			];
			$( "#followers" ).autocomplete({
				source: availableFollowers,
			    select:function(e,ui) {			    	
		    		$.ajax({
		    			url: "http://shahinfosolutions.com/EW/TwitterTimelineChallenge/process.php", 				//This is the page where you will handle your SQL insert
		    			data: "selected_follower="+ui.item.screen_name,
		    			type: "POST",
		    			crossDomain: true,
		    			async: false,
		    		   	success: function(msg){
		    				//alert(msg);
		    				$("#dynamic_slider").html("<div class='slider-wrapper'><div id='slider' style='height:100px;'></div><div id='slider-direction-nav'></div><div id='slider-control-nav'></div></div>");
		    				$("#thumbnails-custom-content").html("Latest Tweets from "+ui.item.value+"'s Home");
		    				var counter=0; 			
		    				$.each(JSON.parse(msg), function(idx, obj) {
			    				counter=counter+1;
			    				if(counter == 11){return false;}
			    				
			    				//alert(JSON.stringify(obj, null, 4));
			    				var imgUrl = obj.user.profile_image_url;
			    				if((typeof obj.retweeted_status  != "undefined") && (typeof obj.retweeted_status.user  != "undefined") && (typeof obj.retweeted_status.user.profile_image_url  != "undefined") && obj.retweeted_status.user.profile_image_url != ''){
			    					imgUrl = obj.retweeted_status.user.profile_image_url;
				    			}
			    					//$("#slider").append("<div class='slide"+counter+"' style='text-align:center;'><table id='tweet_table'><tr><td><img src='"+obj.retweeted_status.user.profile_image_url+"' class='tweet_feed_image'/></td><td style='padding-left:20px;'>"+obj.retweeted_status.text+"</td></tr></table></div>");
			    				//}else{
			    					$("#slider").append("<div class='slide"+counter+"' style='text-align:center;'><table id='tweet_table'><tr><td><img src='"+imgUrl+"' class='tweet_feed_image'/></td><td style='padding-left:20px;'>"+obj.text+"</td></tr></table></div>");
			    				//}
			    				//alert(obj.text + counter);		    					
			    			});
		    				//$("#slider-wrapper").append("<div id='slider-direction-nav'></div><div id='slider-control-nav'></div>");
		    				//$("#slider-wrapper").append("<div id='slider-direction-nav'></div><div id='slider-control-nav'></div>");		    				
		    				var slider = $('#slider').leanSlider({
		    		            directionNav: '#slider-direction-nav',
		    		            controlNav: '#slider-control-nav'
		    		        });
		    			}
		    		});
			    	return false;
				     
			    }
			});
			
		});
	</script>
<div id="user_follower_list">
	<h3 id="thumbnails-custom-content">Your Followers</h3>
	<div class="ui-widget">
		<label for="followers">Search & View your followers's Home: </label>
		<input id="followers">
	</div>	
>>>>>>> d92ecaa395f6895ce6889de9abfc6ccf882ad23c
	<div class="bs-example">
	    <div class="row">
	    <?php 
	    	  if(count($suggested_users_list->users)>10){
	    		$follower_count=10;
	    	  }else{
	    	  	$follower_count=count($suggested_users_list->users);
	    	  }
	   	?>		
		<?php for($j=0;$j<$follower_count;$j++){?>	
			<div class="col-sm-6 col-md-4" style="padding-top:10px;height:300px;width:200px;">
	    	    <div class="thumbnail">
		          <img data-src="" src="<?php echo str_replace("_normal", "_bigger", $suggested_users_list->users[$j]->profile_image_url);?>" height="128px" width="100px" alt="Not available">
		          <div class="caption">
		            <h3><?php echo $suggested_users_list->users[$j]->name;?></h3>
<<<<<<< HEAD
		            <p><a href="http://twitter.com/<?php echo $suggested_users_list->users[$j]->screen_name;?>" class="btn btn-primary" role="button" target="_blank">View Timeline</a></p>
=======
		            <p><a href="http://twitter.com/<?php echo $suggested_users_list->users[$j]->screen_name;?>" class="btn btn-primary" role="button">View Timeline</a></p>
>>>>>>> d92ecaa395f6895ce6889de9abfc6ccf882ad23c
		    	  </div>
		       </div>
	       </div>
		<?php  }?>	
	  </div>
	</div><!-- /.bs-example -->
</div>
<div id="dynamic_div"></div>    
</body>
</html>
