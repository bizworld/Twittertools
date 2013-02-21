<?php

require_once("../../lib/TwitterTools.php");
require_once("../../lib/TwitterOAuth.php");
require_once("../../lib/OAuth.php");

/* consumer key & consumer secret*/
$consumer_key = "app_consumer_key";
$consumer_secret = "app_consumer_secret";

/* access token & access token secret from your twitter user */
$access_token = "your_access_token";
$access_token_secret = "your_access_token_secret";

$tw = new TwitterTools($consumer_key,$consumer_secret,$access_token,$access_token_secret);

/* possible answers */

$answers[] = "Isso será realizado.";
$answers[] = "Melhor esquecer.";
$answers[] = "Quem sabe outro dia?";
$answers[] = "A resposta está dentro de você.";
$answers[] = "Você já sabe a resposta.";
$answers[] = "Isso está fora do meu alcance.";
$answers[] = "Certamente.";
$answers[] = "Isso é um mistério. O futuro é uma caixinha de surpresas.";
$answers[] = "Os astros dizem que sim.";
$answers[] = "Isso é possível, só depende de você.";
$answers[] = "Está escrito.";
$answers[] = "Tudo é possível.";


$last_mention_id = 0;
if(is_file("lastmention.id"))
	$last_mention_id = file_get_contents("lastmention.id");

// obter replies e responder

//$result = $tw->getMentions(10);
$params['count'] = 10;
if($last_mention_id)
	$params['since_id'] = $last_mention_id;

$result = json_decode($tw->makeRequest('http://api.twitter.com/1/statuses/mentions.json',$params),1);

if($result)
{
	/*echo "<pre>";
	print_r($result);
	echo "</pre>";*/
	foreach($result as $tweet)
	{
		if(!$lastid)
			$lastid = $tweet['id_str'];
		
		//evita responder duplicadamente
		if(!in_array($tweet['user']['screen_name'],$replies))
		{			
			//pergunta?
			$pos = strpos($tweet['text'],'?');
			if($pos !== false)
			{
				//responde pergunta
				$rand = array_rand($answers);
				$tw->update("@".$tweet['user']['screen_name']." ".$answers[$rand],$tweet['id_str']);
				$replies[] = $tweet['user']['screen_name'];
			}
		}
	}
	
	$fp = fopen("lastmention.id",'w');				
	fwrite($fp,$lastid);
	fclose($fp);

}
else
	echo "Não há mentions para responder.";
?>

