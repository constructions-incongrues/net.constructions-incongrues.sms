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
$name=$smspi->numberName($number);


echo "<h1><i class='glyphicon glyphicon-retweet'></i> Conversation with $number - $name</h1>";


$conv=$smspi->conversation($number);

echo conversationHtml($conv);

function conversationHtml(array $conv)
{
    if (count($conv)<1) {
        return "<div class='alert alert-info'>No conversation with xxx</div>";
    }

    $html=[];
    foreach ($conv as $t => $v) {
        //echo $t;
        if (@$v['in']) {
            $html[]=date("d/m/Y H:i", $t);
            $message = "<i class='glyphicon glyphicon-user'></i> " . $v['in'];
            $html[]="<div class='alert alert-success'>$message</div>";
        }
        if (@$v['out']) {
            $message = "<i class='glyphicon glyphicon-hand-right'></i> " . $v['out'];
            $html[]="<div class='alert alert-info'>$message</div>";
        }
    }

    return implode("", $html);
}
