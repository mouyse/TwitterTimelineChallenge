<?php
session_start();
ob_start();
error_reporting(0);
@ini_set('display_errors', 0);
//Including Twitter library
require('lib/twitteroauth/twitteroauth/twitteroauth.php');
require('twitterappconfig.php');

//Including functions file which are mendatory
require_once('required_functions.php');

//Check if access_token is actually verified
if(empty($_SESSION['access_token']) || empty($_SESSION['access_token']['oauth_token']) || empty($_SESSION['access_token']['oauth_token_secret'])){
	header("Location: clearsession.php");
}

//Creating access_token variable
$access_token=$_SESSION['access_token'];
$connection=new TwitterOAuth(consumer, consumer_secret,$access_token['oauth_token'],$access_token['oauth_token_secret']);

//Making an API Call to fetch Home Timeline of currently logged in user
$home_timeline=$connection->get('statuses/home_timeline',array('include_rts' => 'true'));

//Making an API Call to fetch Account information of Currently logged in user
$user_info=$connection->get('account/verify_credentials');

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
<link rel="stylesheet" href="http://code.jquery.com/ui/1.11.1/themes/smoothness/jquery-ui.css">
<script src="http://code.jquery.com/jquery-1.10.2.js"></script>
<script src="http://code.jquery.com/ui/1.11.1/jquery-ui.js"></script>

<!-- jQuery Slider -->

<script src="lib/LeanSlider/modernizr-2.6.1.min.js"></script>    
<script src="lib/LeanSlider/lean-slider.js"></script>
<link rel="stylesheet" href="lib/LeanSlider/lean-slider.css" type="text/css" />
<link rel="stylesheet" href="lib/LeanSlider/sample-styles.css" type="text/css" />

<script type="text/javascript">
    $(document).ready(function() {
        var slider = $('#slider').leanSlider({
            directionNav: '#slider-direction-nav',
            controlNav: '#slider-control-nav'
        });
    });
</script>


</head>

<body>
<?php 
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
$current_user_name=$user_info->name;
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
	
<?php 
	if(!empty($_SESSION['access_token']) && !empty($_SESSION['access_token']['oauth_token']) && !empty($_SESSION['access_token']['oauth_token_secret'])){
?>
	<div class="collapse navbar-collapse">
	  <ul class="nav navbar-nav">
		<li class="active">
			<a href="clearsession.php" >Logout</a>
		</li>
	  </ul>
	</div><!--/.nav-collapse -->
<?php }?>
  </div>
</div>
<div class="jumbotron">
 <h1>Hello, <?php echo ucfirst($user_info->name);?></h1>
 <p></p>
 <p>
 	<a href="http://twitter.com/<?php echo $user_info->screen_name; ?>" class="btn btn-primary btn-lg" target="_blank" role="button"><span class="glyphicon glyphicon-user"></span> View My Twitter &raquo;</a>
 </p>
 	<div class="panel panel-primary">
      <div class="panel-heading">
        <h3 class="panel-title">Download My All Tweets &raquo;</h3>
      </div>
      <div class="panel-body">
        
 		<a href="process.php?p=<?php echo encryptDecrypt('encrypt',$user_info->screen_name);?>" class="btn btn-primary btn-lg" role="button" target="_blank">PDF</a>
 		<a href="process.php?j=<?php echo encryptDecrypt('encrypt',$user_info->screen_name);?>" class="btn btn-primary btn-lg" role="button" target="_blank">JSON</a>
 		<a href="process.php?c=<?php echo encryptDecrypt('encrypt',$user_info->screen_name);?>" class="btn btn-primary btn-lg" role="button" target="_blank">CSV</a>
 		<a href="process.php?e=<?php echo encryptDecrypt('encrypt',$user_info->screen_name);?>" class="btn btn-primary btn-lg" role="button" target="_blank">XLS</a>
 		<a href="process.php?x=<?php echo encryptDecrypt('encrypt',$user_info->screen_name);?>" class="btn btn-primary btn-lg" role="button" target="_blank">XML</a>
 		<a href="process.php?g=<?php echo encryptDecrypt('encrypt',$user_info->screen_name);?>" class="btn btn-primary btn-lg" role="button" target="_blank">Google Spreadsheet</a>
      </div>
    </div>
    <form method="POST" action="process.php" target="_blank">
	 	<div class="panel panel-primary">
	      <div class="panel-heading">
	        <h3 class="panel-title">Email My All Tweets &raquo;</h3>
	      </div>
	      <div class="panel-body">
	      	<div class="input-group">
			  <span class="input-group-addon">@</span>
			  <input type="email" class="form-control" placeholder="Email" required="required" id="email" name="email">
			</div>
			<br />
	        <button type="submit" class="btn btn-primary" >Mail Me PDF </button>
	      </div>
	    </div>
    </form>
 
</div>
<div class="jumbotron">
	<h3 id="thumbnails-custom-content" style="text-align: left;">Latest Tweets from <?php echo $current_user_name."'s ";?><span class="glyphicon glyphicon-home"></span> </h3>
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

 	 <script>
$(function() {
var availableFollowers = [
	<?php for($uc=0;$uc<count($suggested_users_list);$uc++){?>
<?php echo '{ value: "'.$suggested_users_list[$uc]->name.'",label: "'.$suggested_users_list[$uc]->name.'",screen_name: "'.$suggested_users_list[$uc]->screen_name.'"},';?>
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
    				//Replacing whole div with a new div
    				$("#dynamic_slider").html("<div class='slider-wrapper'><div id='slider' style='height:100px;'></div><div id='slider-direction-nav'></div><div id='slider-control-nav'></div></div>");
    				$("#thumbnails-custom-content").html("Latest Tweets from "+ui.item.value+"'s Home");
    				//Initializing counter variable to 0
    				var counter=0; 			
    				$.each(JSON.parse(msg), function(idx, obj) {
        				//Incrementing counter by 1
	    				counter=counter+1;
	    				//Stopping the loop As we only need to have 10 tweets
	    				if(counter == 11){return false;}
	    				//Setting up imgUrl to profile_image_url at first
	    				var imgUrl = obj.user.profile_image_url;
	    				//Replacing imgUrl with new URL if and only if the current tweet was acutually retweeted.
	    				if((typeof obj.retweeted_status  != "undefined") && (typeof obj.retweeted_status.user  != "undefined") && (typeof obj.retweeted_status.user.profile_image_url  != "undefined") && obj.retweeted_status.user.profile_image_url != ''){
	    					imgUrl = obj.retweeted_status.user.profile_image_url;
		    			}
		    			//Adding a new slide
	    				$("#slider").append("<div class='slide"+counter+"' style='text-align:center;'><table id='tweet_table'><tr><td><img src='"+imgUrl+"' class='tweet_feed_image'/></td><td style='padding-left:20px;'>"+obj.text+"</td></tr></table></div>");		    					
	    			});		    				
	    			//Adding new controls to navigate
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
		
	<div class="bs-example">
	    <div class="row">
	    <?php 
	    	  //Initializing follower_count to 10 or below 
	    	  if(count($suggested_users_list)>10){
	    		$follower_count=10;
	    	  }else{
	    	  	$follower_count=count($suggested_users_list);
	    	  }
	   	?>		
		<!-- Iterating through an array to display 10 or less followers -->
		<?php for($j=0;$j<$follower_count;$j++){?>	
			<div class="col-sm-6 col-md-4" style="padding-top:10px;height:300px;width:200px;">
	    	    <div class="thumbnail">
				<!-- Displaying Image -->
		          <img data-src="" src="<?php echo str_replace("_normal", "_bigger", $suggested_users_list[$j]->profile_image_url);?>" height="128px" width="100px" alt="Not available">
		          <div class="caption">
				  <!-- Displaying User's Actual Name -->
		            <h3><?php echo $suggested_users_list[$j]->name;?></h3>
					<!-- Displaying A Link to User's Timeline -->
		            <p><a href="http://twitter.com/<?php echo $suggested_users_list[$j]->screen_name;?>" class="btn btn-primary" role="button" target="_blank">View Timeline</a></p>
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
