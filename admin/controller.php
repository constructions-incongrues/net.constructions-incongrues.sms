<?php
/**
 * Admin controller
 */
header('Content-Type: text/html; charset=utf-8');

require "../class.smspi.php";
$config = json_decode( file_get_contents( __DIR__ . '/../config.json') );

$smspi = new smspi( $config );



switch( $_POST['do'] ){

	case 'numberAdd':
		//print_r( $_POST );
		if(!preg_match("/^\+33[0-9]{9}$/",$_POST['number'] ) ){
			die("Le format doit etre : +33xxxxxxxxx");
		}
		if( $smspi->numberAdd( $_POST['number'] ) ){
			die("Ok : Number added !");
		}
		break;

	case 'numberTest':
		
		//print_r($_POST);
		
		$id = $smspi->queue_add( $_POST['number'], $_POST['body'] );
		if($id)die("In queue : msg #$id");
		else die("Error");
		break;

	default:die("Error:" . $_POST['do'] );
}

exit;