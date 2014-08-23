<?php
/**
 * Admin controller
 */
header('Content-Type: text/html; charset=utf-8');

require __DIR__."/../../../vendor/autoload.php";

//use ConstructionsIncongrues\Curl;
use ConstructionsIncongrues\Sms\Gammu;
use ConstructionsIncongrues\Sms\SmsPi;

$config = json_decode(file_get_contents(__DIR__.'/../../config.json'));
$smspi = new SmsPi($config);



switch($_POST['do']) {

    case 'getLogs':
        //print_r($_POST);
        $dat = $smspi->logs($_POST['filter']);
        echo json_encode($dat);
        exit;
        break;
   
    case 'clearLogs':
        //print_r($_POST);
        if ($smspi->logClear('%')) {
            die("Cleared!");
        }
        die('Error');
        break;
    
    default:
        die("Error:" . $_POST['do']);
}
