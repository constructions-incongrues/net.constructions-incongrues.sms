<?php
/**
 * Get sms's, save to DB, process the queue, clear the messages
 */
header('Content-Type: text/html; charset=utf-8');


require "class.curl.php";

$start = time();

$config = json_decode( file_get_contents( __DIR__ . '/config.json') );

//$gammu = new gammu();
//$smspi = new smspi( $config );



$cc = new cURL();	


//$URL = "https://www.google.fr/#q=define:basket";
$URL = 'http://en.wikipedia.org/w/api.php?action=query&titles=paris&prop=revisions&rvprop=content&rvsection=0';
$URL = 'http://www.crisco.unicaen.fr/des/synonymes/panier';

$html = $cc->get( $URL );//call the service
$httpCode = $cc->httpCode();
$content_type = $cc->contentType();
$content_length = $cc->contentLength();

if( $httpCode == 200 )
{
	$text = $html;
	echo $text;exit;
	echo htmlentities( $text );exit;
}else{

	//Todo : Log error here
	echo "Error $httpCode";	
}

