<?php
/**
 * SuperSMS
 * Get sms's, save to DB, process the queue, clear the messages
 * Run Every minutes
 */
header('Content-Type: text/html; charset=utf-8');

// Composer
require_once(__DIR__.'/../vendor/autoload.php');

// Uses
use ConstructionsIncongrues\Curl;
use ConstructionsIncongrues\Sms\Gammu;
use ConstructionsIncongrues\Sms\SmsPi;

// Start stopwatch
$start = time();

// Load configuration
$config = json_decode(file_get_contents(__DIR__.'/config.json'));

$gammu = new Gammu();
$smspi = new SmsPi($config);

if (!is_writable($config->modem)) {
    throw new \RuntimeException('Modem is not writable - modem='.$config->modem);
}


echo date('c') . "\n";

// Get SMS from Device
echo "Get SMS...\n";
$response = $gammu->Get();

if (!count(@$response['inbox'])) {
    //$smspi->log( 'notice' , "No SMS" );
    //die("No SMS\n");
} else {
    
    // Saving sms's
    foreach ($response['inbox'] as $k => $v) {
        //skip multipart messages//
        if (is_array(@$v['link'])) {
            continue;
        }
        $smspi->saveSms($v);
        //print_r( $v );
    }

    /* Clear all SMS's on the sim */
    if (count(@$response['inbox'])) {
        echo "ClearAllSms();\n";
        $gammu->ClearAllSms();
        echo "Sim Cleared\n";
    }

}



// Generating replies and put them in the queue //
$dat = $smspi->getUnread();

echo count($dat) . " unread message(s)\n";
echo "--------------------------\n";
if (is_array($dat) && count($dat)) {

    //$smspi->log('notice', count($dat) . " SMS");

    foreach ($dat as $k => $r) {

        print_r($r);

        $r['body'] = trim($r['body']);

        //echo $r['remote_number'] . " say " . $r['body'] . "\n";

        $words = explode(' ', strtolower($r['body']));
        //We call the first word the 'command'
        $cmd = $words[0];

        //redirection detection
        if (preg_match("/\b((0|0033)[67][\d]{8})\b/", $r['body'], $o)) {
            $o[1] = $smspi->numberCheck($o[1]);
            $smspi->numberAdd($o[1], "Added via " . $r['remote_number']);
            $r['remote_number']=$o[1];
        }
        
        $service = $smspi->serviceByName($cmd);

        echo "cmd=$cmd\n";


        if (!$service) {
            $service=$smspi->serviceByName('default');
            if (!$service) {
                 $smspi->log('error', "Service 'default' not found. Please fix");
                 exit;
            }
        }

        //Todo : big things here
        if ($service) {

            $cc = new cURL();

            if (preg_match("/^http/", $service['url'])) {
                $URL = $service['url']."/?num=".$r['remote_number']."&body=".urlencode($r['body']);
                //$URL = "http://127.0.0.1/sms/src/services/".$service['url']."/?num=".$r['remote_number']."&body=".urlencode($r['body']);
            } else {
                // error
                $smspi->log('error', "Url format error : ".print_r($service, true));
                continue;
                //$URL = "http://127.0.0.1/sms/src/services/".$service['url']."/?num=".$r['remote_number']."&body=".urlencode($r['body']);

                //$URL = $service['url']."/?num=".$r['remote_number']."&body=".urlencode($r['body']);
                //echo "URL=$URL\n";
            }

            $html = $cc->get($URL);//call the service
            $httpCode = $cc->httpCode();
            $content_type = $cc->contentType();
            $content_length = $cc->contentLength();

            if ($httpCode == 200) {
                $text = $html;
                echo "$text\n";
            } else {
                $text = "SMS Error $httpCode";
                //Todo : Log error here
                $smspi->log('error', "SMS Error $httpCode");
            }
            $smspi->serviceUpdate($service['id']);
        
        } else { // service not found
            //$text = $smspi->error_message();
            //$text = "Service not found";
            $smspi->log('error', "This should not happen! cmd=$cmd service=$service");
        }


        if (!$smspi->queue_add($r['remote_number'], $text)) {
            if ($text) {
                $smspi->log('error', "Msg not added to the queue");
            } else {
                $smspi->log('error', "No text");
            }
        }

        if (!$smspi->markAsRead($r['i'])) {
            echo "Error with markAsRead( ".$r['i']." )\n";
            $smspi->log('error', "Error with markAsRead( ".$r['i']." )");
        }
    }
}








// Get the queue and send the replies //

$queue = $smspi->queue_get();

echo count($queue) . " msg(s) in the queue\n";

foreach ($queue as $q_id => $r) {
    $text = $r['q_body'];
    //Send the computed reply//
    echo "reply to ".$r['q_number']." : $text\n";

    $response = '';
    if ($text) {
        $gammu->Send($r['q_number'], $text, $response);
    }
    echo "$response\n";

    if (preg_match("/error/i", $response)) {
        echo $response;
        $smspi->log('error', "$response");
        //error_log("$response\n" , 3 , "errors.txt");

    } else {
        // Log as sent
        $smspi->logSent($r['q_number'], $text, $response);
        $smspi->queue_del($q_id);
    }
}







$end = time()-$start;
echo "done in $end seconds\n";
if ($end>20) {
    $smspi->log('warning', "job done in $end seconds");
}
