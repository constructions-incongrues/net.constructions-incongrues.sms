<?php
/**
 * Get sms's and save them to DB
 * Then do a bit of cleaning
 */
require "class.gammu.php";
require "class.smspi.php";
require "class.curl.php";
include __DIR__."/config.php";
include __DIR__."/sms_errors.php";

$start = time();

$db = new mysqli( $dbhost, $dbuser, $dbpass , $dbname );
if(!$db)die("No database connection");

// Detect modem //
if(!is_writable( $modem ))
{    
    die("Error : $modem is not writable\n");
}

$gammu = new gammu();
$smspi = new smspi( $db );

//Identify
//$gammu->Identify( $identify );
//die( print_r( $identify ));
//
//$detect = $gammu->detect();
//die( print_r( $detect ));

echo date('c') . "\n"; 

// Get SMS from Device
echo "Get SMS...\n";
$response = $gammu->Get();

if(!count(@$response['inbox']))
{
	die("No SMS\n"); 
}
else
{
	// Saving sms's
	foreach( $response['inbox'] as $k=>$v )
	{
		//skip multipart messages//
		if( is_array( @$v['link'] ) )continue;
		$smspi->saveSms( $v );
		//print_r( $v );
	}
}





//Generating and sending replies//
$dat = $smspi->getUnread();

echo count( $dat ) . " unread message(s)\n";
echo "--------------------------\n";
if( is_array($dat) && count($dat))
{
	foreach( $dat as $k=>$r ){
		
		print_r( $r );
		
		echo $r['remote_number'] . " say " . $r['body'] . "\n";

		$words = explode(' ', strtolower( $r['body'] ));
		$cmd = $words[0]; 

		echo "cmd=$cmd\n";

		//Todo : big things here
		if(is_dir( __DIR__ . "/$cmd"))
		{
			$cc = new cURL();	
			$html = $cc->get( "http://127.0.0.1/sms/$cmd/?num=".$r['remote_number'] . "&body=" . $r['body'] );
			$httpCode = $cc->httpCode();
			$content_type = $cc->contentType();
			$content_length = $cc->contentLength();
			
			if( $httpCode==200 )
			{
				$text = $html;
				echo "$text\n";
			}else{
				$text = "SMS Error";
			}

		}
		else
		{
			shuffle( $errors );
			$text = $errors[0];
		}
		echo "reply : $text\n";

		$response = '';	
		$gammu->Send($r['remote_number'], $text, $response );
		echo "$response\n";
		
		if( preg_match("/error/i",$response)){
			error_log("$response\n" , 3 , "errors.txt");
		}

		if( !$smspi->markAsRead( $r['i'] ) ){
			echo "Error with markAsRead( ".$r['i']." )\n";
		}
	}
}



/* Clear all SMS's */
if( count(@$response['inbox'] ) )
{
	echo "ClearAllSms();\n";
	$gammu->ClearAllSms();

	$end = time()-$start;
	echo "done in $end seconds\n";  
}

