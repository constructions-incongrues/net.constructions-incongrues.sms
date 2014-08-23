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

    //Create a new phone number
    /*
    case 'numberAdd':
        //print_r( $_POST );

        if (preg_match("/^0[67][0-9]{8}$/", $_POST['number'])) {
            //die("Format court a corriger");
            $_POST['number'] = preg_replace("/^0([67])/", "+33$1", $_POST['number']);
        }



        if (!preg_match("/^\+33[0-9]{9}$/", $_POST['number'])) {
            die("Erreur: Le format doit etre : +33xxxxxxxxx");
        }
        if ($smspi->numberAdd($_POST['number'])) {
            die("document.location.href='?number=".$_POST['number']."';");
        }
        break;
    */
   
    //Save phonenumber informations
    case 'numberSave':

        if ($smspi->numberSave($_POST['id'], $_POST['name'], $_POST['comment'], $_POST['email'])) {
            echo "<div class='alert alert-success'>Number Saved!</div>";
        } else {
            echo "Error saving number\n";
            print_r($_POST);
        }
        exit;
        break;


    //Send a message to a given number
    //(actualy add the message to the queue )
    case 'numberTest':
        $id = $smspi->queue_add($_POST['number'], $_POST['body']);
        if ($id) {
            die("In queue : msg #$id");
        } else {
            die("Error");
        }
        break;

    case 'numberDelete':
        //print_r($_POST);
        if ($smspi->numberDelete($_POST['id'])) {
            die("document.location.href='phonebook.php';");
        } else {
            die("Error");
        }
        break;

    case 'getLogs':
        //print_r($_POST);
        $dat = $smspi->logs($_POST['filter']);
        echo json_encode($dat);
        exit;
        break;
   
    case 'services':// list of registered services
        $dat = $smspi->serviceList();
        echo json_encode($dat);
        break;

    case 'serviceCreate':
        //print_r($_POST);
        $id = $smspi->serviceRegister($_POST['name']);
        if ($id) { # Service created !
            die("document.location.href='?';");
        } else {
            die("error creating service");
        }
        break;

    case 'serviceSave':
        $_POST['id']*=1;
        $id = $smspi->serviceSave($_POST['id'], $_POST['name'], $_POST['url'], $_POST['comment']);
        if ($id) {
            die("document.location.href='?id=".$_POST['id']."';");
        }
        break;



    default:
        die("Error:" . $_POST['do']);
}
