<?php
/**
 * Send a nice sms reply for each waiting db messages
 * problem : the modem may be busy receiving messages !
 */
require "class.gammu.php";
require "class.smspi.php";
include __DIR__."/config.php";
include __DIR__."/sms_errors.php";

/*
$start = time();

$db = new mysqli( $dbhost, $dbuser, $dbpass , $dbname );

// Detect modem //
if(!is_writable( $modem )) 
{
    die("Error : $modem is not writable\n");
}

$gammu = new gammu();
$smspi = new smspi( $db );

echo date('c');
echo "getUnread()\n";

$dat = $smspi->getUnread();

echo count( $dat ) . " unread message(s)\n";
echo "--------------------------\n";

if(!count( $dat ) )die("Nothing to do\n");

foreach( $dat as $k=>$r ){
	
	print_r( $r );
	
	echo $r['remote_number'] . " say " . $r['body'] . "\n";

	$words = explode(' ', $r['body']);
	$arg = $words[0]; 
	
	//Todo : big things here
	shuffle($errors);
	$text = $errors[0];
	
	echo "reply : $text\n";

	$response = '';
	
	$gammu->Send($r['remote_number'], $text, $response );
	
	echo "$response\n";
	
	if( !$smspi->markAsRead( $r['i'] ) ){
		echo "Error with markAsRead( ".$r['i']." )\n";
	}

	//Send($number,$text,&$respon)
	die("Stop\n");
}


die("done in ".(time() - $start)." second(s)\n" );
*/