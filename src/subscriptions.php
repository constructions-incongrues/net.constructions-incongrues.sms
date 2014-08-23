<?php
/**
 * SuperSMS
 * Subscriptions System
 * Run Every Hours or manualy (but not at night)
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

echo "Subscriptions\n";
echo "-------------\n";

if (date('G') < 11||date('G') > 22) {
    die("It's not polite to send news at this hour");
}

// Load configuration
$config = json_decode(file_get_contents(__DIR__.'/config.json'));

$gammu = new Gammu();
$smspi = new SmsPi($config);

$db=$smspi->db;


$subs=$smspi->getSubscribers();
foreach ($subs as $sub) {
    //$serviceName=$sub['service'];
    $service=$smspi->serviceByName($sub['service']);
    if (!$service) {
        echo "Error : service $sub not found\n";
        $smspi->log('error', "service $sub not found");
        continue;
    }

    //phone number data
    $nd=$smspi->numberData($sub['phonenumber']);
    if (!$nd) {
        echo "Error : phone number #$sub[phonenumber] not found\n";
        $smspi->log('error', "service $sub[phonenumber] not found");
        continue;
    }
    //print_r($nd);

    $text=file($service['url'])[0];
    $text=trim($text);

    echo "$text\n";

    if (!$text) {
        echo "error: !text\n";
        continue;
    }

    if (!$smspi->queue_add($nd['phonenumber'], $text)) {
        $smspi->log('error', "Msg not added to the queue");
    }
    $smspi->updateSubscription($sub['id']);
}

//print_r($subs);
