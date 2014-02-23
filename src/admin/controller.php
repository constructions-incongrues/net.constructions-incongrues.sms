<?php
/**
 * Admin controller
 */
header('Content-Type: text/html; charset=utf-8');

require __DIR__."/../../vendor/autoload.php";

//use ConstructionsIncongrues\Curl;
use ConstructionsIncongrues\Sms\Gammu;
use ConstructionsIncongrues\Sms\SmsPi;

$config = json_decode(file_get_contents(__DIR__.'/../config.json'));
$smspi = new SmsPi($config);



switch($_POST['do']) {

    case 'numberAdd':
        //print_r( $_POST );
        if (!preg_match("/^\+33[0-9]{9}$/", $_POST['number'])){
            die("Le format doit etre : +33xxxxxxxxx");
        }
        if ($smspi->numberAdd($_POST['number'])) {
            die("Ok : Number added !");
        }
        break;

    case 'numberTest':
        
        //print_r($_POST);
        
        $id = $smspi->queue_add($_POST['number'], $_POST['body']);
        if($id) {
            die("In queue : msg #$id");
        } else {
            die("Error");
        }
        break;

    default:die("Error:" . $_POST['do']);
}
