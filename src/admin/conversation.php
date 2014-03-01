<?php
/**
 * Smspi Admin :: Conversation
 */
header('Content-Type: text/html; charset=utf-8');

require __DIR__."/../../vendor/autoload.php";

//use ConstructionsIncongrues\Curl;
//use ConstructionsIncongrues\Sms\Gammu;
use ConstructionsIncongrues\Sms\SmsPi;

$config = json_decode(file_get_contents(__DIR__.'/../config.json'));
$smspi = new SmsPi($config);

include "menu.html";

$number = trim(@$_GET['number']);
$number = preg_replace("/^33/", '+33', $number);

if (!$number) {
    die("<div class='alert alert-danger'>Not a number</div>");
}
//print_r($_GET);
$smspi->numberName($number);


echo "<h1><i class='glyphicon glyphicon-retweet'></i> Conversation with $number</h1>";



$conv=[];

$sql = "SELECT sent as t, body as message FROM inbox WHERE remote_number LIKE '$number' ORDER BY t DESC LIMIT 10;";
$q = $smspi->db->query($sql) or die($smspi->db->error);
//echo "<pre>$sql</pre>";
while ($r=$q->fetch_assoc()) {
    $t = strtotime($r['t']);
    $conv[$t]['in'] = $r['message'];
}

$sql = "SELECT message, time as t FROM log_sent WHERE `number` LIKE '$number' ORDER BY t DESC LIMIT 10;";
$q = $smspi->db->query($sql) or die($smspi->db->error);
//echo "<pre>$sql</pre>";
while ($r=$q->fetch_assoc()) {
    $t = strtotime($r['t']);
    //print_r($r);
    $conv[$t]['out'] = $r['message'];
}

ksort($conv);
//print_r($conv);

//echo "<table class=table>";
foreach ($conv as $t => $v) {
    //echo $t;

    if (@$v['in']) {
        echo date("d/m/Y H:i", $t);
        $message = "<i class='glyphicon glyphicon-user'></i> " . $v['in'];
        echo "<div class='alert alert-success'>$message</div>";
    }
    if (@$v['out']) {
        $message = "<i class='glyphicon glyphicon-hand-right'></i> " . $v['out'];
        echo "<div class='alert alert-info'>$message</div>";
    }
    

}


