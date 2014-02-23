<?php
// Composer
require_once(__DIR__.'/../vendor/autoload.php');

use ConstructionsIncongrues\Curl;
use ConstructionsIncongrues\Sms\Gammu;
use ConstructionsIncongrues\Sms\SmsPi;

// Configuration
include __DIR__."/config.php";

$start = time();

// Instruct MySQLi to throw exceptions
// @see http://stackoverflow.com/a/21048373/3157702
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$db = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
if (false === $db) {
    throw new \RuntimeException("No database connection");
}
*/

// Detect modem
if (!is_writable($modem)) {
    throw new \RuntimeException("Modem is not writable - modem=".$modem);
}

$gammu = new Gammu();
$smspi = new SmsPi($db);

echo date('c') . "\n";

// Get SMS from Device
echo "Get SMS...\n";
$response = $gammu->Get();

if (!isset($response['inbox']) || count($response['inbox']) === 0) {
    echo 'Inbox is empty';
    exit(0);
} else {
    // Saving sms's
    foreach ($response['inbox'] as $k => $v) {
        // skip multipart messages
        if (isset($v['link']) && is_array($v['link'])) {
            continue;
        }
        $smspi->saveSms($v);
    }
}

// Generating and sending replies
$dat = $smspi->getUnread();

echo count($dat) . " unread message(s)\n";
echo "--------------------------\n";
if (is_array($dat) && count($dat)) {
    foreach ($dat as $k => $r) {
        print_r($r);

        $r['body'] = trim($r['body']);

        echo $r['remote_number'] . " say " . $r['body'] . "\n";

        $words = explode(' ', strtolower($r['body']));

        // We call the first word the 'command'
        $cmd = $words[0];

        $service = $smspi->serviceGet($cmd);

        echo "cmd=$cmd\n";


        // Todo : big things here
        if($service)
        {
            $cc = new Curl();

            //$URL = "http://127.0.0.1/sms/$cmd/?num=".$r['remote_number'] . "&body=" . $r['body']
            $URL = "http://127.0.0.1/sms/" . $service['url'] . "/?num=".$r['remote_number'] . "&body=" . urlencode( $r['body'] );

            $html = $cc->get($URL);
            $httpCode = $cc->httpCode();
            $content_type = $cc->contentType();
            $content_length = $cc->contentLength();

            if ($httpCode == 200) {
                $text = $html;
                echo "$text\n";
            } else {
                $text = "SMS Error $httpCode";
            }

        } else {
            $text = $smspi->error_message();
        }

        //Send the computed reply//
        echo "reply : $text\n";

        $response = '';
        $gammu->Send($r['remote_number'], $text, $response);
        echo "$response\n";

        if (preg_match("/error/i", $response)) {
            error_log("$response\n", 3, "errors.txt");
        }

        if (!$smspi->markAsRead($r['i'] )) {
            echo "Error with markAsRead( ".$r['i']." )\n";
        }
    }
}



// Get the queue and send the replies //

$queue = $smspi->queue_get();

echo count( $queue ) . " msg(s) in the queue\n";

foreach( $queue as $q_id=>$r )
{
	$text = $r['q_body'];
	//Send the computed reply//
	echo "reply : $text\n";

	$response = '';	
	if( $text )$gammu->Send( $r['q_number'], $text, $response );
	echo "$response\n";
	
	if( preg_match("/error/i", $response)){
		echo $response;
		$smspi->log( 'error' , "$response" );
		//error_log("$response\n" , 3 , "errors.txt");
		
	}else{
		//Log as sent
		$smspi->logSent( $r['q_number'] , $text , $response );
		$smspi->queue_del( $q_id );
	}

}






/* Clear all SMS's */
if (count($response['inbox'])) {
    echo "ClearAllSms();\n";
    $gammu->ClearAllSms();

    $end = time() - $start;
    echo "done in $end seconds\n";
}
