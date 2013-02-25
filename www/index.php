<?php 
require '../autoload.php';

$tw = new \TwitterTools\TwitterTools('g2pV3ooY0pn7rlEW6E3vXQ','8SLNQF8xHnDnLun1QauNaBhtvqKt41anLImloyV6Q');

if(!$tw->getState()) {
    echo "auth url: ";
    echo $tw->getAuthorizeUrl();
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>TwitterTools</title>
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
<h2>Twittertools</h2>

<ul class="methods">
	<li><a title="First Steps - twitter app development" href="http://erikaheidi.com/open-source/twittertools/first-steps/">First Steps</a> - first things you need to know about twitter app development</li>
	<li><a title="a really simple app to show the basics and get you started" href="http://erikaheidi.com/open-source/twittertools/hello-twitter-a-twittertools-example-app/" target="_blank">Hello Twitter example app</a> - your first twittertools application, step by step (including how to register the app on Twitter)</li>
	<li><a title="Multi - user app demo" href="http://erikaheidi.com/open-source/twittertools/basic-multi-user-app-demo/" target="_blank">Simple multi-user app demo</a> - a simple example showing how to authenticate users through Twitter</li>
	<li><a title="On the playground area you can see return data from the most common methods" href="http://erikaheidi.com/os/Twittertools/playground.php">Return of the Methods (playground / sandbox)</a> - here you can check the data returned by the most common methods</li>
</ul>

</body>
</html>
