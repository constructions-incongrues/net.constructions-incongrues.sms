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

    // Create a new phone number
    case 'numberAdd':
        //print_r( $_POST );

        if (preg_match("/^0[67][0-9]{8}$/", $_POST['number'])) {
            //die("Format court a corriger");
            $_POST['number'] = preg_replace("/^0([67])/", "+33$1", $_POST['number']);
        }

        if (!preg_match("/^\+33[0-9]{9}$/", $_POST['number'])) {
            die("Erreur: Le format doit etre : +33xxxxxxxxx");
        }
        if ($id=$smspi->numberAdd($_POST['number'])) {
            die("document.location.href='../phonenumber/?id=$id';");
        }
        break;

  

    case 'phonebook':
        //print_r($_POST);
        $dat = $smspi->phoneBook($_POST['filter'], $_POST['limit']);
        echo json_encode($dat);
        exit;
        break;

    


    default:
        die("Error:" . $_POST['do']);
}
