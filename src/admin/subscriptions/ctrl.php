<?php
/**
 * Admin subscriptions controller
 */
header('Content-Type: text/html; charset=utf-8');

require __DIR__."/../../../vendor/autoload.php";

use ConstructionsIncongrues\Sms\SmsPi;

$config = json_decode(file_get_contents(__DIR__.'/../../config.json'));
$smspi = new SmsPi($config);



switch($_POST['do']) {


    case 'subscribe':
        //print_r($_POST);
        $check=$smspi->numberCheck($_POST['phonenumber']);


        $number_id=$smspi->numberId($check);
        if (!$number_id) {
            die("Error : phone number not found");
            exit;
        }

        $id=$smspi->subscribe($number_id, $_POST['service_id']);
        if ($id) {
            die("document.location.href='?';");
        }
        
        die("error adding number : ".$_POST['phonenumber']);
        break;

    case 'unsubscribe':
        //print_r($_POST);
        $id=$smspi->unsubscribe($_POST['id']);
        die("document.location.href='?';");
        break;

    case 'numberCheck':
        //print_r($_POST);
        if ($check=$smspi->numberCheck($_POST['number'])) {
            $id=$smspi->numberId($_POST['number']);
            if ($name=$smspi->numberName($id)) {
                //echo "ok $check";
                //print_r($dat);
                echo "<a href='../phonenumber/?id=$id' class='btn btn-primary'>$name</a>";
                exit;
            } else {
                die("<a href=# class='btn btn-danger'>Unknow phone number $check</a>");
            }
        }
        die("invalid number ".$_POST['number']);
        break;

    default:
        die("Unknow action:" . $_POST['do']);
}
