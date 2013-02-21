<?php
/* ***************************************************************
 * TwitterTools 
 * v. 3.0 - 20/07/2011
 * by @erikaheidi
 * http://twittertools.in
 *****************************************************************/

class TwitterTools{

/********************************************
 * API KEYS
 * you can configure with your own values
 * or leave as it is
 * *********************************************/
	
	/* Bit.ly
	 * register here: http://www.bit.ly/account/register?rd=/
	 * api key: http://bit.ly/account/your_api_key/
	 */
	static $bl_login = "twittertools";
	static $bl_apikey = "R_623ae4653a902137617f3e699bfe236c";
	
	/* 
	 * TwitPic
	 * 
	 * */
	static $twitpic_apikey = "01afe13c27f2988a1f3b7a550d5df6fe";

/* api keys end */
	
	var $consumer_key;
	var	$consumer_secret;

	var $atoken;
	var $atoken_secret;
	
	var $rtoken;
	var $rtoken_secret;
	
	var $state;
	
	function __construct($consumer_key,$consumer_secret,$atoken=0,$atoken_secret=0)
	{
		$this->consumer_key = $consumer_key;
		$this->consumer_secret = $consumer_secret;
		$this->state = 0;
		
		if($atoken AND $atoken_secret)
		{
			$this->atoken = $atoken;
			$this->atoken_secret = $atoken_secret;
			$this->state = 2;
		}
		else
		{
			$tokens = unserialize($_SESSION['tokens']);
			$tokens_secrets = unserialize($_SESSION['tokens_secrets']);
			
			if(!empty($_SESSION['oauth_access_token']))
			{
				//logged
				$this->state = 2;
				$this->atoken =  $_SESSION['oauth_access_token'];
				$this->atoken_secret =  $_SESSION['oauth_access_token_secret'];
			}
			else
			{
				
				if(!empty($_REQUEST['oauth_token']))
				{
					$key = @array_search($_REQUEST['oauth_token'],$tokens);
					
					if($key !== false)
					{
						$this->rtoken = $_REQUEST['oauth_token'];
						$this->rtoken_secret = $tokens_secrets[$key];
					
						if(!$this->state)
						{
							//returned
							$this->state = 1;
							$this->getAccessToken();
						}
					}
					
				}
				else
				{
					$to = new TwitterOAuth($this->consumer_key, $this->consumer_secret);
					$tok = $to->getRequestToken();
					$this->rtoken = $rtoken = $tok['oauth_token'];
					$this->rtoken_secret = $rtoken_secret = $tok['oauth_token_secret'];
					
					$tokens[] = $rtoken;
					$tokens_secrets[] = $rtoken_secret;
					
					$_SESSION['tokens'] = serialize($tokens);
					$_SESSION['tokens_secrets'] = serialize($tokens_secrets);
				}
			
			}
		}

	}
	
	
	function checkState()
	{
		
		if(isset($_SESSION['oauth_state']) && !empty($this->atoken_secret))
			$state = $_SESSION['oauth_state'] = "logged";
		elseif($_REQUEST['oauth_token'] != NULL && $_SESSION['oauth_state'] === 'start') 
			$state = $_SESSION['oauth_state'] = "returned";
		else
			$state = $_SESSION['oauth_state'] = "start";
			
		return $state;
	}
	
	function getAuthLink()
	{
		$to = new TwitterOAuth($this->consumer_key, $this->consumer_secret);
		
		if(empty($this->rtoken))
		{
			$tokens = unserialize($_SESSION['tokens']);
			$tokens_secrets = unserialize($_SESSION['tokens_secrets']);
			
			$tok = $to->getRequestToken();
			$this->rtoken = $rtoken = $tok['oauth_token'];
			$this->rtoken_secret = $rtoken_secret = $tok['oauth_token_secret'];
			
			$tokens[] = $rtoken;
			$tokens_secrets[] = $rtoken_secret;
			
			$_SESSION['tokens'] = serialize($tokens);
			$_SESSION['tokens_secrets'] = serialize($tokens_secrets);
		}
		
		return $to->getAuthorizeURL($this->rtoken);
	}
	
	function getAccessToken()
	{
		$to = new TwitterOAuth($this->consumer_key, $this->consumer_secret, $this->rtoken, $this->rtoken_secret);			 
		$tok = $to->getAccessToken();
		$_SESSION['oauth_access_token'] = $this->atoken = $tok['oauth_token'];
		$_SESSION['oauth_access_token_secret'] = $this->atoken_secret = $tok['oauth_token_secret'];
	}
	
	function makeRequest($api_url,$args=null,$method='GET')
	{
		$twitter = new TwitterOAuth($this->consumer_key, $this->consumer_secret, $this->atoken, $this->atoken_secret);
		
		return $twitter->OAuthRequest($api_url,$args,$method);
	}
	
	function remainingCalls()
	{		
		return json_decode($this->makeRequest('http://api.twitter.com/1/account/rate_limit_status.json'),1);
	}
	
	/* user related */
	function getCredentials()
	{
		return json_decode($this->makeRequest('http://api.twitter.com/account/verify_credentials.json'),1);	
	}
	
	function checkConnections($users)
	{
		$ret = json_decode($this->makeRequest('http://api.twitter.com/1/friendships/lookup.json',array("screen_name"=>$users)),1);
		foreach($ret as $user)
			$retorno[$user['id_str']] = $user['connections'];
		
		return $retorno;
	}
	
	// returns 1 if user_a follows user_b
	function isFollower($user_a,$user_b)
	{
		return json_decode($this->makeRequest('http://api.twitter.com/1/friendships/exists.json',array("screen_name_a"=>$user_a,"screen_name_b"=>$user_b)),1);
	}
	
	function follow($to)
	{
		return json_decode($this->makeRequest('http://api.twitter.com/1/friendships/create.json', array("screen_name"=>$to), 'POST'),1);
	}
	
	function unfollow($to)
	{
		return json_decode($this->makeRequest('http://api.twitter.com/1/friendships/destroy.json', array("screen_name"=>$to), 'POST'),1);
	}
	
	function getFollowers($screen_name=0,$cursor=-1)
	{
		$params['cursor'] = $cursor;
		if($screen_name)
			$params['screen_name'] = $screen_name;
			
		return json_decode($this->makeRequest('http://api.twitter.com/1/followers/ids.json',$params),1);

	}
		
	function getFriends($screen_name,$cursor=-1)
	{
		$params['cursor'] = $cursor;
		if($screen_name)
			$params['screen_name'] = $screen_name;
			
		return json_decode($this->makeRequest('http://api.twitter.com/1/friends/ids.json',$params),1);
	}
		
	function getUsersInfo($lista_users)
	{
		return json_decode($this->makeRequest('http://api.twitter.com/1/users/lookup.json',array("user_id"=>$lista_users)),1);	
	}
	
	/* tweets related */
	function update($msg,$inreplyto=0,$autoshort=0)
	{		
		$message = strip_tags($msg);
		
		if($autoshort)
		{			
			$message = preg_replace_callback('@(https?://([-\w\.]+)+(:\d+)?(/([\w/_\.]*(\?\S+)?)?)?)@', create_function(
            '$matches',
            'return TwitterTools::getSmallLink($matches[1]);'
			), $message);			
		}
		$message = substr($message,0,140);
		 
		return json_decode($this->makeRequest('http://api.twitter.com/statuses/update.json', array('status' => $message,'in_reply_to_status_id'=>$inreplyto), 'POST'),1);
	}
	
	function deleteTweet($id)
	{
		return json_decode($this->makeRequest('http://api.twitter.com/1/statuses/destroy/'.$id.'.json',array("id"=>$id),'POST'),1);
	}

	function getSmallLink($longurl,$api='bitly')
	{
		switch($api)
		{
			case "bitly":
				return self::shortBitLy($longurl);
				break;

		}

	}
	
	function getTweet($id)
	{	
		return json_decode($this->makeRequest('http://api.twitter.com/1/statuses/show/'.$id.'.json'),1);		
	}
	
	function getTimeline($limit=10)
	{	
		return json_decode($this->makeRequest('http://api.twitter.com/1/statuses/home_timeline.json',array("count"=>$limit)),1);		
	}
	
	function getMentions($limit=10)
	{	
		return json_decode($this->makeRequest('http://api.twitter.com/1/statuses/mentions.json',array("include_rts"=>1,"count"=>$limit)),1);
	}
	
	function getRetweets($limit=10)
	{	
		$ret = json_decode($this->makeRequest('http://api.twitter.com/1/statuses/retweets_of_me.json',array("trim_user"=>1,"count"=>$limit)),1);
		
		foreach($ret as $tweet)
		{
			$users = json_decode($this->makeRequest('http://api.twitter.com/1/statuses/'.$tweet['id_str'].'/retweeted_by/ids.json'),1);
			
			$result[] = array("tweet"=>$tweet,"retweeted_by"=>$users);
		}
		return $result;
	}
		
	function getFavorites($page=1,$limit=10)
	{
		return json_decode($this->makeRequest('http://api.twitter.com/1/favorites.json',array("page"=>$page,"count"=>$limit)),1);
	}
	
	function favorite($id)
	{
		return json_decode($this->makeRequest('http://api.twitter.com/1/favorites/create/'.$id.'.json',array("id"=>$id),'POST'),1);
	}
	
	function unFavorite($id)
	{
		return json_decode($this->makeRequest('http://api.twitter.com/1/favorites/destroy/'.$id.'.json',array("id"=>$id),'POST'),1);
	}
	
	function retweet($id)
	{
		return json_decode($this->makeRequest('http://api.twitter.com/1/statuses/retweet/'.$id.'.json',array("id"=>$id),'POST'),1);
	}
	
	/* DM's - your app need RWD permission to use these metods */
	function getDms($limit=10)
	{
		return json_decode($this->makeRequest('http://api.twitter.com/1/direct_messages.json',array("cursor"=>$limit)),1);
	}
	
	function sendDm($to,$text)
	{
		$message = substr(strip_tags($text,0,140));
		return json_decode($this->makeRequest('http://api.twitter.com/1/direct_messages/new.json',array("screen_name"=>$to,"text"=>$message),'POST'),1);
	}
	
	function deleteDm($id)
	{
		return json_decode($this->makeRequest('http://api.twitter.com/1/direct_messages/destroy/'.$id.'.json',array("id"=>$id),'POST'),1);
	}
	
	/* public */
	
	function getTrending($woeid=1)
	{
		#woeid = yahoo's where on earth ID of the location. default is global = 1
		return json_decode($this->makeRequest('http://api.twitter.com/1/trends/'.$woeid.'.json'),1);
	}
	
	function search($query,$limit=30)
	{	
		$ret = $this->makeRequest('http://search.twitter.com/search.json',array("show_user"=>"true","q"=>$query));
		if($ret)
		{			
			$all = json_decode($ret,true);
			return $all['results'];
		}
	}


	/* 3rd party api's */
	
	//bit.ly
	function shortBitLy($longurl) 
	{	
		
		$login = self::$bl_login;
		$apiKey = self::$bl_apikey;
		//$longurl = rawurlencode($longurl);
		$url = "http://api.bitly.com/v3/shorten?longUrl=$longurl&login=$login&apiKey=$apiKey&format=json&history=1";
		$result = file_get_contents($url);
		$obj = json_decode($result, true);
		$link = $obj['data']['url'];
		if(empty($link))
			return $obj['status_txt'];
		else
			return $longurl;
			
	}
	
	//twitpic
	function postTwitPic($FILEPATH,$legenda="Posted with TwitterTools")
	{
		$post_url='http://api.twitpic.com/2/upload.json';
		
		
		$consumer = new OAuthConsumer($this->consumer_key,$this->consumer_secret);
		
		$header = array('X-Auth-Service-Provider: https://api.twitter.com/1/account/verify_credentials.json',
		'X-Verify-Credentials-Authorization: OAuth realm="http://api.twitter.com/"');
		
		 // instantiating OAuth customer
		 $consumer = new OAuthConsumer($this->consumer_key,$this->consumer_secret);
		 
		 // instantiating signer
		 $sha1_method = new OAuthSignatureMethod_HMAC_SHA1();
		 
		 // user's token
		 $token = new OAuthConsumer($this->atoken, $this->atoken_secret);
		 
		 // Generate all the OAuth parameters needed
		 $signingURL = 'https://api.twitter.com/1/account/verify_credentials.json';
		 
		 $request = OAuthRequest::from_consumer_and_token($consumer, $token, 'GET', $signingURL, array());
		 
		 $request->sign_request($sha1_method, $consumer, $token);
		
		
		$header[1] .= ", oauth_consumer_key=\"".$this->consumer_key."\"";
		$header[1] .= ", oauth_signature_method=\"" . $request->get_parameter('oauth_signature_method') ."\"";
		$header[1] .= ", oauth_token=\"" . $request->get_parameter('oauth_token') ."\"";
		$header[1] .= ", oauth_timestamp=\"" . $request->get_parameter('oauth_timestamp') ."\"";
		$header[1] .= ", oauth_nonce=\"" . $request->get_parameter('oauth_nonce') ."\"";
		$header[1] .= ", oauth_version=\"" . $request->get_parameter('oauth_version') ."\"";
		$header[1] .= ", oauth_signature=\"" . urlencode($request->get_parameter('oauth_signature')) ."\"";
		
		
		//open connection
		$ch = curl_init();

		//Set paramaters
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

		//set the url, number of POST vars, POST data
		curl_setopt($ch,CURLOPT_URL,$post_url);

		$media_data = array(
		'media' => '@'.$FILEPATH,
		'message' =>$legenda,
		'key'=>self::$twitpic_apikey
		);

		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$media_data);

		 //execute post
		 $result = curl_exec($ch);
		 $response_info=curl_getinfo($ch);
		 
		 //close connection
		 curl_close($ch);
		


		if($response_info['http_code'] == 200) //Success
		{
			//Decode the response
			$json = json_decode($result,true);
			$id = $json['id'];
			$twitpicURL = $json['url'];
			$text = $json['text'];
			$message = trim($text) . " " . $twitpicURL;
			
			return $message;
		}
		else
		{
			$content = "<p>Twitpic upload failed. No idea why!</p>";
			$json = json_decode($result);
			$content .= "<br / /><b>message</b> " . urlencode($legenda);
			$content .= "<br / /><b>json</b> " . print_r($json);
			$content .= "<br / /><b>Response</b> " . print_r($response_info);
			$content .= "<br / /><b>header</b> " . print_r($header);
			$content .= "<br / /><b>media_data</b> " . print_r($media_data);
			$content .= "<br /><b>URL was</b> " . $twitpicURL;
			$content .= "<br /><b>File uploaded was</b> " . $FILEPATH;
			
			return array("error"=>1,"code"=>$response_info['http_code'],"debug"=>$content);
		}

	}

	
}


?>
