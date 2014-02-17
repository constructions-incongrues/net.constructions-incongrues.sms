<?php
/**
 * Get sms's, save to DB, process the queue, clear the messages
 */
require "class.gammu.php";
require "class.smspi.php";
require "class.curl.php";

$start = time();

$config = json_decode( file_get_contents( __DIR__ . '/config.json') );

$gammu = new gammu();
$smspi = new smspi( $config );


// Detect modem //
/*
if(!is_file( $config->modem ))
{	
	$smspi->logClear( "modem $config->modem not found" );
	$smspi->log( 'error' , "modem $config->modem not found" );
    die("Error : modem $config->modem not found\n");
}
*/

if(!is_writable( $config->modem ))
{
	$smspi->logClear( "$config->modem not writable" );
	$smspi->log( 'error' , "$config->modem not writable" );
    die("Error : $config->modem not writable\n");
}


echo date('c') . "\n"; 

// Get SMS from Device
echo "Get SMS...\n";
$response = $gammu->Get();

if(!count(@$response['inbox']))
{
	//$smspi->log( 'notice' , "No SMS" );
	//die("No SMS\n"); 
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
	
	$smspi->log( 'notice' , count($dat) . " SMS" );
	
	foreach( $dat as $k=>$r ){
		
		print_r( $r );
		
		$r['body'] = trim($r['body']);

		echo $r['remote_number'] . " say " . $r['body'] . "\n";

		$words = explode(' ', strtolower( $r['body'] ));
		//We call the first word the 'command'
		$cmd = $words[0]; 

		$service = $smspi->serviceGet( $cmd );		

		echo "cmd=$cmd\n";


		//Todo : big things here
		if( $service )
		{
			$cc = new cURL();	
			
			//$URL = "http://127.0.0.1/sms/$cmd/?num=".$r['remote_number'] . "&body=" . $r['body']
			$URL = "http://127.0.0.1/sms/services/" . $service['url'] . "/?num=".$r['remote_number'] . "&body=" . urlencode( $r['body'] );
			
			$html = $cc->get( $URL );//call the service
			$httpCode = $cc->httpCode();
			$content_type = $cc->contentType();
			$content_length = $cc->contentLength();
			
			if( $httpCode == 200 )
			{
				$text = $html;
				echo "$text\n";
			}else{
				$text = "SMS Error $httpCode";
				//Todo : Log error here
				$smspi->log( 'error' , "SMS Error $httpCode" );	
			}
			$smspi->serviceUpdate( $service['id'] );
		}
		else
		{
			$text = $smspi->error_message();
			//$text = "Service not found";
			$smspi->log( 'warning' , "Service $cmd not found" );
	
		}


		//Send the computed reply//
		echo "reply : $text\n";

		$response = '';	
		$gammu->Send( $r['remote_number'], $text, $response );
		echo "$response\n";
		
		if( preg_match("/error/i", $response)){
			echo $response;
			$smspi->log( 'error' , "$response" );
			//error_log("$response\n" , 3 , "errors.txt");
			
		}else{
			//Log as sent
			$smspi->logSent( $r['remote_number'] , $text , $response );
		}

		if( !$smspi->markAsRead( $r['i'] ) ){
			echo "Error with markAsRead( ".$r['i']." )\n";
			$smspi->log( 'error' , "Error with markAsRead( ".$r['i']." )" );
		}
	}
}



/* Clear all SMS's */
if( count(@$response['inbox'] ) )
{
	echo "ClearAllSms();\n";
	$gammu->ClearAllSms();

}



$end = time()-$start;
echo "done in $end seconds\n";
if( $end > 20 )$smspi->log( 'warning' , "job done in $end seconds" );