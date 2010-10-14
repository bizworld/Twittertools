<?php
//essencial!
session_name("TTDemo");
session_start();

if(isset($_GET['logout']))
{
	session_unset();
	session_destroy();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>TwitterTools - DEMO</title>
<link href="style.css" rel="stylesheet" type="text/css" />
</head>
<body>
<h2>Twitter Tools Demo - Friends</h2>
<a href="../index.php">Back</a>
<?php
require_once("../lib/TwitterTools.php");
require_once("../lib/TwitterOAuth.php");
require_once("../lib/OAuth.php");

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


if($tw->state)
{
	if(isset($_GET['user']) AND !empty($_GET['user']) )
		$user = $_GET['user'];
	else
		$user = $credentials->screen_name;
		
	$tweets = $tw->getFriends($user);
	if($tweets)
	{
	?>
	<div class="box">
	<h4><?=$user?>'s Friends (100 latest)</h4>
	<form><p>Show friends from another user: <input type="text" name="user" /><input type="submit" value="Show"/></p></form>
	<?
		foreach($tweets as $tweet)
		{
			
			?>
			<div class="tweet">
			<img src="<?=$tweet->profile_image_url?>" style="float:left;margin:5px;" width="48" height="48"/> <strong><?=$tweet->screen_name?></strong> <?=utf8_decode($tweet->status->text)?><br/>
			<small><?=$tweet->created_at?></small>
			<br clear="all"/>
			</div>
			<br clear="all"/>
			<?
		}
	?>
	</div>
	<?
	}
	else
		echo "an error ocurred.";
}

?>
</body>
</html>
