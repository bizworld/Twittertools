<?php
session_start();

if(isset($_GET['logout']))
    session_unset();

require '../autoload.php';

$consumer_key = 'g2pV3ooY0pn7rlEW6E3vXQ';
$consumer_secret = '8SLNQF8xHnDnLun1QauNaBhtvqKt41anLImloyV6Q';
$access_token = null;
$access_token_secret = null;

/* first: check if theres already a user logged in */
if ($_SESSION['logged_user']) {
    $logged_user = unserialize($_SESSION['logged_user']);
    $access_token = $logged_user['access_token'];
    $access_token_secret = $logged_user['access_token_secret'];
}

$app_config = array(
    'consumer_key'  =>  $consumer_key,
    'consumer_secret' => $consumer_secret,
    'request_secret' => $request_secret,
    'access_token'    => $access_token,
    'access_token_secret' => $access_token_secret,
);

$myapp = new \TwitterTools\TwitterTools($app_config);


if(!$myapp->getState()) {
    
    /* check if there is a user comming from auth page on twitter */
    if (!empty($_REQUEST['oauth_token'])) {
    
    	/* if so, is time to ask for the access tokens.
    	 * use the request_secret we stored before
    	 * to make the request 
    	 * the method returns a user array with access tokens, id and screen name*/
        $user = $myapp->getAccessTokens($_REQUEST['oauth_token'], $_SESSION['request_secret']);
      
        if (!empty($user['access_token'])) {
            /* congratulations, you have successfully logged in */
            $logged_user = $user;
            $_SESSION['logged_user'] = serialize($user);
        }
    
    } else {
    	
        $result = $myapp->getAuthorizeUrl();
        /* we need to store the token secret for the next request, 
         * after a user has authorized your app         
         */
        $_SESSION['request_secret'] = $result['secret'];
       
        $auth_link = $result['auth_url'];
    }   
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
	margin: 10px 30px;
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

div.login {
    background-color: #333333;
    color: #E5E5E5;
    padding: 10px;
    margin-bottom: 20px;
    font-size: 16px;
    text-align: right;
}

div.login a {
    color: #FFF;
}

</style>
</head>
<body>

<div class="login">
<?php
if ($logged_user)
    echo '<p>Você está logado como <strong>'. $logged_user['screen_name'] . '</strong> [ <a href="./?logout">logout</a> ]</p>';
else
    echo '<p>Você não está logado. <a href="' .$auth_link . '">Clique aqui para fazer login com o Twitter</a></p>';
?>
</div>

<div class="boxRetorno">
<h2>Twittertools Console</h2>
<?php
$test_methods = array(
    'getTimeline',
    'getMentions',
    'getFavorites',
);
?>
<form>
<select name="method">
   <?php 
   foreach($test_methods as $option)
      echo '<option value="' . $option . '">' . $option . '() </option>';
   ?>
</select>

<input type="text" name="path" value="" />
<input type="submit" value="Make Request" />
</form>
</div>

<div class="boxRetorno">
<pre>
<?php 

if ($logged_user) {
    $method = 'getTimeline';
    
    if($_GET['method'])
    {
        if(in_array($_GET['method'], $test_methods))       
            $method = $_GET['method'];
    }
    
    $tl = $myapp->$method();
    
    echo 'Last Request Info: ';
    print_r($myapp->getLastReqInfo());
    echo 'Response: ';
    print_r($tl); 
    
} else {
    echo "please log in";
}
?> 
</pre>
</div>

<ul class="methods">
	<li><a title="First Steps - twitter app development" href="http://erikaheidi.com/open-source/twittertools/first-steps/">First Steps</a> - first things you need to know about twitter app development</li>
	<li><a title="a really simple app to show the basics and get you started" href="http://erikaheidi.com/open-source/twittertools/hello-twitter-a-twittertools-example-app/" target="_blank">Hello Twitter example app</a> - your first twittertools application, step by step (including how to register the app on Twitter)</li>
	<li><a title="Multi - user app demo" href="http://erikaheidi.com/open-source/twittertools/basic-multi-user-app-demo/" target="_blank">Simple multi-user app demo</a> - a simple example showing how to authenticate users through Twitter</li>
	<li><a title="On the playground area you can see return data from the most common methods" href="http://erikaheidi.com/os/Twittertools/playground.php">Return of the Methods (playground / sandbox)</a> - here you can check the data returned by the most common methods</li>
</ul>


</body>
</html>
