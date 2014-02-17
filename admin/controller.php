<?php
/**
 * Admin controller
 */
header('Content-Type: text/html; charset=ISO-8859-1');

require "../class.smspi.php";
$config = json_decode( file_get_contents( __DIR__ . '/../config.json') );

$smspi = new smspi( $config );


print_r($_POST);

switch( $_POST['do'] ){

	case 'numberAdd':
		print_r($_POST);
		break;

	default:die("Error:" . $_POST['do'] );
}

exit;