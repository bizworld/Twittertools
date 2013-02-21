<?php
//this is mandatory
session_name("TTPlayground");
session_start();


if(isset($_GET['logout']))
	session_unset();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>TwitterTools - DEMO</title>
<style>
body {
	font-family: arial;
	font-size: 12px;
	color: #666;
}

ul.methods {
	float: left;
	width: 260px;
	margin: 10px;
	font-size: 14px;
}

ul.methods li a{
	background-color: #E5E5E5;
	padding: 5px;
	text-decoration: none;
	display: block;
	margin: 1px;
}

ul.methods li a:hover {
	font-weight: bold;
	background-color: #F5F5F5;
}

div.boxRetorno {
	color: #FFF;
	background-color: #000;
	padding: 10px;
	margin: 10px;
}

</style>
</head>
<body>
<h2>Twitter Tools - Return Samples</h2>

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
		$logado = 1;
		$credentials = $tw->getCredentials();
		
		?>
		<p>You are logged in as: <strong><?=$credentials['screen_name']?></strong> [ <a href="./?logout=1">LOGOUT</a> ]</p>
	<?
	}//else	
?>
<p>Click on the method to check its return (you need to log in first).</p>


<ul class="methods">
	<li><strong>User-related</strong></li>
	<li><a href="./playground.php?command=remainingCalls">remainingCalls()</a></li>
	<li><a href="./playground.php?command=getCredentials">getCredentials()</a></li>
	<li><a href="./playground.php?command=checkConnections">checkConnections(users_logins)</a></li>
	<li><a href="./playground.php?command=isFollower">isFollower(user_login)</a></li>
	<li><a href="./playground.php?command=follow">follow(user_login)<strong>*</strong></a></li>
	<li><a href="./playground.php?command=unfollow">unfollow(user_login)<strong>*</strong></a></li>
	<li><a href="./playground.php?command=getFollowers">getFollowers([user_login,cursor])</a></li>
	<li><a href="./playground.php?command=getFriends">getFriends([user_login,cursor])</a></li>
	<li><a href="./playground.php?command=getUsersInfo">getUsersInfo(users_ids)</a></li>
</ul>
<ul class="methods">
	<li><strong>Tweets-related</strong></li>
	<li><a href="./playground.php?command=update">update(msg[,inreplyto,autoshort]) <strong>**</strong></a></li>
	<li><a href="./playground.php?command=getTweet">getTweet(id)</a></li>
	<li><a href="./playground.php?command=getTimeline">getTimeline([limit])</a></li>
	<li><a href="./playground.php?command=getMentions">getMentions([limit])</a></li>
	<li><a href="./playground.php?command=getRetweets">getRetweets([limit])</a></li>
	<li><a href="./playground.php?command=getFavorites">getFavorites([page,limit])</a></li>
</ul>

<ul class="methods">
	<li><strong>Public</strong></li>
	<li><a href="./playground.php?command=getTrending">getTrending()</a></li>
	<li><a href="./playground.php?command=getSearch">search(query)</a></li>
</ul>

<br clear="all"/>
<p><small>*when clicking, you will follow/unfollow @TToolslib profile</small><br/><small>**when clicking, you will post a tweet like this: "testing twittertools php lib - http://t.co/ovUE5ZXX ."</small></p>
<?
		if($logado AND isset($_GET['command']))
		{
			switch($_GET['command'])
			{
				case "remainingCalls":
					$retorno = $tw->remainingCalls();
					break;
				
				case "getCredentials":
					$retorno = $tw->getCredentials();
					break;
				
				case "isFollower":
					$msg = "Testing Profile: @erikaheidi. Will return 1 if you are following @erikaheidi or 0 if you are not following.";
					$retorno = $tw->isFollower($credentials['screen_name'],'erikaheidi');
					break;
				
				case "checkConnections":
					$msg = "Testing Profiles: @erikaheidi (id: 19625601) and @tweetauditor (id:204955540). Response is the connection between logged user (you) and the specified users.";
					$retorno = $tw->checkConnections("erikaheidi,tweetauditor");
					break;
				
				case "getFollowers":
					$retorno = $tw->getFollowers();
					break;
			
				case "getFriends":
					$retorno = $tw->getFriends();
					break;		
				
				case "getUsersInfo":
					$msg = "Testing Profiles: @erikaheidi and @tweetauditor";
					$retorno = $tw->getUsersInfo('19625601,204955540');
					break;
				
				case "update":
					#twitter automatically shorten links with his t.co shortner
					$retorno = $tw->update("testing twittertools php lib - http://erikaheidi.com/open-source/twittertools");
					break;
					
				case "getTweet":
					$msg = "Test Tweet: http://twitter.com/#!/erikaheidi/status/93309305482264576";
					$retorno = $tw->getTweet('93309305482264576');
					break;
					
				case "getTimeline":
					$retorno = $tw->getTimeline();
					break;
				
				case "getMentions":
					$retorno = $tw->getMentions();
					break;
				
				case "getRetweets":
					$retorno = $tw->getRetweets();
					break;
				
				case "getFavorites":
					$retorno = $tw->getFavorites();
					break;
																	
				case "getTrending":
					$retorno = $tw->getTrending();
					break;
				
				case "getSearch":
					$msg = "Searching for the term '#php'";
					$retorno = $tw->search("#php");
					break;
									
				case "follow":
					$msg = "Testing Profile: @TToolslib";
					$retorno = $tw->follow('TToolslib');
					break;
				
				case "unfollow":
					$msg = "Testing Profile: @TToolslib";
					$retorno = $tw->unfollow('TToolslib');
					break;
				
			}
?>			
			
			<h3><?=$_GET['command']?></h3>
			<p><?=$msg?></p>
			<div class="boxRetorno">		
			<?
			if($retorno)
			{
				echo "<pre>"; 
				print_r($retorno); 
				echo "</pre>";
			
			}
			else
				echo "Return was empty or equal to zero.";
			?> 
			</div>
<?
		}//if

?>

</body>
</html>
