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
<h2>Twitter Tools Demo - Follow</h2>
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

if(isset($_GET['follow']))
{
	$ret = $tw->follow("erikaheidi");

	//echo "<p>You are now following @erikaheidi.</p>";
	echo "<p>Retorno:</p><pre>";
	echo $ret;
	echo "</pre>";
}

if($tw->state)
{
?>
						
			<div class="box">
			<h3>Follow Test</h3>
			
			<p><strong>stay in touch ;P!</strong> <a href="follow.php?follow=1">Clique here to follow @erikaheidi on twitter.</a></p>
			</div>
<?
}

?>
</body>
</html>
