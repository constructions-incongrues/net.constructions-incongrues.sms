<?php
/**
 * SMS Admin :: Status
 */
require "../class.smspi.php";
require "../class.gammu.php";

$config = json_decode( file_get_contents( __DIR__ . '/../config.json') );
$smspi = new smspi( $config );
$gammu = new gammu();

//echo "<h1>Status</h1>";

//Detect config file
if(!is_file( __DIR__."/../config.json")){
	die("<div class='alert alert-danger'>Error : config.json not found</div>");
}
//echo "<pre>" . print_r( $config , true ) . "</pre>";

echo "<h1><i class='glyphicon glyphicon-info-sign'></i> Status</h1>";

if( $smspi->gammuDetect() )
{
	echo "<div class='alert alert-success'>Gammu detected in " . $smspi->config->gammu . "</div>";
	$version = trim( $gammu->Version() );
	echo "<pre>$version</pre>";
}else{
	echo "Error : gammu not found\n";
}

echo "<h2>Modem detection:</h2>";

if( $smspi->modemWritable() )
{
	echo "<div class='alert alert-success'>Modem '".$smspi->config->modem."' is writeable</div>";
}else
{
	if(!is_file( $smspi->config->modem ) ){
		die("<div class='alert alert-danger'>Error : Modem '" . $smspi->config->modem . "' not found</div>");
	}
	echo "Error : Modem '" . $smspi->config->modem . "' not writable\n";
	echo "try : gammu identify\n";
	exit;
}




