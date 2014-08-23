<?php
/**
 * SMS Admin :: sent
 */
header('Content-Type: text/html; charset=utf-8');

require __DIR__."/../../../vendor/autoload.php";

use ConstructionsIncongrues\Sms\SmsAdmin;
use ConstructionsIncongrues\Sms\SmsPi;

$config = json_decode(file_get_contents(__DIR__.'/../../config.json'));
$smspi = new SmsPi($config);


switch ($_POST['do']) {
    
    case 'clearAll':
        //print_r($_POST);
        if ($smspi->queue_clear()) {
            die("document.location.href='?';");
        }
        break;

    case 'delQ':
        //print_r($_POST);
        if ($smspi->queue_del($_POST['id'])) {
            die("document.location.href='?';");
        }
        die("Error");
        break;

    default:
        die("Error 2");
        break;
}

die('error');
