<?php
//test commands//
require "./class.curl.php";

$cmd="camion";

echo "cmd=$cmd\n";


if(is_dir("./$cmd")){
	
	$cc = new cURL();	
	$html = $cc->get( "http://127.0.0.1/sms/$cmd" );
	$httpCode = $cc->httpCode();
	$content_type = $cc->contentType();
	$content_length = $cc->contentLength();
	
	echo "httpCode=$httpCode\n";	
	echo "$html\n";
	
}

//die("ok\n");
