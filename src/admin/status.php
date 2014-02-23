<?php
/**
 * SMS Admin :: Status
 */
header('Content-Type: text/html; charset=utf-8');

require __DIR__."/../../vendor/autoload.php";

//use ConstructionsIncongrues\Curl;
use ConstructionsIncongrues\Sms\Gammu;
use ConstructionsIncongrues\Sms\SmsPi;

$config = json_decode(file_get_contents(__DIR__.'/../config.json'));

include "menu.html";

//Detect config file
if (!is_file(__DIR__."/../config.json") || !$config) {
    die("<div class='alert alert-danger'>Error : config.json not found or empty</div>");
} else {
    //check config file
    //invar_dump( $config );
    //if(!$config)
    //exit;
}
//echo "<pre>" . print_r( $config , true ) . "</pre>";

echo "<h1><i class='glyphicon glyphicon-info-sign'></i> Status</h1>";


$smspi = new SmsPi($config);
$gammu = new Gammu();


if ($smspi->gammuDetect()) {
    echo "<div class='alert alert-success'>Gammu detected in " . $smspi->config->gammu . "</div>";
    $version = trim($gammu->Version());
    echo "<pre>$version</pre>";
} else {
    echo "Error : gammu not found\n";
}

echo "<h2>Modem detection:</h2>";

if ($smspi->modemWritable()) {
    echo "<div class='alert alert-success'>Modem '".$smspi->config->modem."' is writeable</div>";
} else {
    if (!is_file($smspi->config->modem)) {
        die("<div class='alert alert-danger'>Error : Modem '" . $smspi->config->modem . "' not found</div>");
    }
    echo "Error : Modem '" . $smspi->config->modem . "' not writable\n";
    echo "try : gammu identify\n";
    exit;
}


echo "<h2>DB connection:</h2>";


//var_dump($config->db);

//echo $smspi->db->error;
if ($smspi->db->connect_errno) {
    echo "<div class='alert alert-danger'>Failed to connect to MySQL: (" . $smspi->db->connect_errno . ") " . $smspi->db->connect_error . "</div>";
} else {
    echo "<div class='alert alert-success'>DB Connection ok</div>";

    //Check tables//

    $sql = "SHOW TABLES LIKE 'inbox';";
    //$
}



//Tables
$tables = array( 'inbox' , 'phonebook' , 'log_errors', 'log_sent' ,'services', 'queue' );

echo "<h2>Tables</h2>";

print_r($tables);
