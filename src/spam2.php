<?php
/**
 * SuperSMS
 * Brutal Spam System
 * Produce random numbers, and produce a number of messages
 */
header('Content-Type: text/html; charset=utf-8');

// Composer
require_once(__DIR__.'/../vendor/autoload.php');

// Uses
use ConstructionsIncongrues\Sms\SmsPi;

echo "BRUTAL SPAM\n";

// Load configuration
$config = json_decode(file_get_contents(__DIR__.'/config.json'));

$smspi = new SmsPi($config);


$services=['sympa','pardon','motivator','demotivator'];
shuffle($services);
$service=$smspi->serviceByName($services[0]);

// var_dump($service);
$url=$service['url'];
$text=file($url)[0];

//var_dump($f);
//$dest=$smspi->spamGetDest();//get a 'random' spam dest
//print_r($dest);

//$phonenumber=$dest['phonenumber'];
$phonenumber=$smspi->randomPhoneNumber();

echo "service url: $url\n";
echo "to: $phonenumber\n";
echo "text: $text\n";

if (!$text) {
    die("error: !text");
}

// time check
if (date('G') < 11||date('G') > 21) {
    //die("No: It's not polite to send a message at this time\n");
}

// time check
if (date('G')%2==1) {
    //die("No: pas cette fois, je le sens pas\n");
}

if (!$smspi->queue_add($phonenumber, $text)) {
    $smspi->log('error', "Msg not added to the queue");
} else {
    //$smspi->spammed($dest['id']);
    echo "spammed :)\n";
}
