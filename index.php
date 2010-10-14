<?php
//essencial!
session_name("TTDemo");
session_start();

if(isset($_GET['logout']))
	session_unset();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>TwitterTools - DEMO</title>
<link href="examples/style.css" rel="stylesheet" type="text/css" />
</head>
<body>
<h2>Twitter Tools Demo</h2>
<?php
require_once("lib/TwitterTools.php");
require_once("lib/TwitterOAuth.php");
require_once("lib/OAuth.php");

	/* consumer key & consumer secret - register an app to get yours at:
	 * http://dev.twitter.com/apps/new
	 */
	$consumer_key = "lgeljXiueyOLElkT4reEwA";
	$consumer_secret = "SkppBLCv652ycImVRnojAKwyy2rJj1gnqGgo4hBtfI";
	
	$tw = new TwitterTools($consumer_key,$consumer_secret);


	if(!$tw->state)
	{		
		$request_link = $tw->getAuthLink();
		echo '<h3>Sign in with your twitter account</h3>';
		echo '<p><a href="'.$request_link.'" title="sign in with your twitter account"><img src="img/sign-in-with-twitter-d.png" /></a></p>';
	} 
	else 
	{	
		$credentials = $tw->getCredentials();
		
		?>
		<p>You are logged in as: <strong><?=$credentials->screen_name?></strong> [ <a href="./?logout=1">LOGOUT</a> ]</p>	
<?
	}//else
?>
			<div class="box">
			<h3>All examples / Tests</h3>
			<p><strong>Update</strong> <a href="examples/update.php">You can update your status in this example.</a></p>
			<p><strong>Follow</strong> <a href="examples/follow.php">Click here to check a follow example.</a></p>
			<p><strong>Timeline</strong> <a href="examples/timeline.php">Click here to view your timelime (now with retweet option).</a></p>
			<p><strong>Mentions</strong> <a href="examples/mentions.php">Click here to view your mentions (@'s).</a></p>
			<p><strong>DMs</strong> <a href="examples/dms.php">Click here to view your DMs.</a></p>
			<p><strong>Followers</strong> <a href="examples/followers.php">Click here to view your followers or any twitter user followers.</a></p>
			<p><strong>Friends</strong> <a href="examples/friends.php">Click here to view your friends or any twitter user friends.</a></p>
			<p><strong>Search</strong> <a href="examples/search.php">Click here to search on Twitter.</a></p>
			</div>
</body>
</html>
