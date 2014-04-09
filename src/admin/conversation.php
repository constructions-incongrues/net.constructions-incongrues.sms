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
?>

<!--
<h3>
<span class="label label-primary"><i class='glyphicon glyphicon-comment'></i> Primary</span>
<span class='pull-right muted'>xx-xx-xxx</span>
</h3>

<h3><span class="label label-default">Default blsbdwql qwfl qwf  wqf qw;f qw;f qw;f qw</span></h3>
<h3><span class="label label-primary"><i class='glyphicon glyphicon-comment'></i> bla bla ?</span></h3>
<h3><span class="label label-default">Il etait une fois Default</span></h3>
-->


<?php
function conversationHtml(array $conv)
{
    if (count($conv)<1) {
        return "<div class='alert alert-info'>No conversation with xxx</div>";
    }

    $html=[];
    foreach ($conv as $t => $v) {
        //echo $t;
        if (@$v['in']) {
            //$html[]=date("d/m/Y H:i", $t);
            $message = "<i class='glyphicon glyphicon-user'></i> " . $v['in'];
            $html[]="<h3>";
            $html[]="<span class='label label-primary'>$message</span>";
            $html[]="<span class='pull-right small'>".date("d/m/Y H:i", $t)."</span>";
            $html[]="</h3>";
        }
        if (@$v['out']) {

            $message = "<i class='glyphicon glyphicon-hand-right'></i> " . $v['out'];
            $html[]="<h3>";
            $html[]="<span class='label label-default'>$message</span>";
            //$html[]="<span class='pull-right muted'>xx-xx-xxx</span>";
            $html[]="</h3>";
        }
    }

    return implode("", $html);
}
