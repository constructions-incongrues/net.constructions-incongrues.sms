<?php
/**
 * SMS Admin :: Status
 * Check system requirements and status
 */
header('Content-Type: text/html; charset=utf-8');

require __DIR__."/../../vendor/autoload.php";

//use ConstructionsIncongrues\Curl;
use ConstructionsIncongrues\Sms\Gammu;
use ConstructionsIncongrues\Sms\SmsPi;

$config = json_decode(file_get_contents(__DIR__.'/../config.json'));

include "menu.html";

$ICO_OK="<i class='glyphicon glyphicon-thumbs-up'></i>";
$ICO_NOK="<i class='glyphicon glyphicon-hand-right'></i>";

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
    echo "<div class='alert alert-success'>$ICO_OK Gammu detected in " . $smspi->config->gammu . "</div>";
    $version = trim($gammu->Version());
    echo "<pre>$version</pre>";
} else {
    echo "<div class='alert alert-danger'>$ICO_NOK Error : gammu not found</div>\n";
}

echo "<h2>Modem detection:</h2>";

if ($smspi->modemWritable()) {
    echo "<div class='alert alert-success'>$ICO_OK Modem '".$smspi->config->modem."' is writeable</div>";
} else {
    if (!is_file($smspi->config->modem)) {
        echo "<div class='alert alert-danger'>$ICO_NOK Error : Modem '" . $smspi->config->modem . "' not found</div>";
    } else {
        //Modem found, but not writeable
        echo "<div class='alert alert-danger'>$ICO_NOK Error : Modem '" . $smspi->config->modem . "' not writable\n";
        echo "try : gammu identify</div>\n";
    }

}


// CURL FOR PHP //
echo "<h2>CURL Extension:</h2>";


if ($smspi->isCurlInstalled()) {
    echo "<div class='alert alert-success'>$ICO_OK cURL is installed on this server</div>";
} else {
    echo "<div class='alert alert-danger'>$ICO_NOK cURL is NOT installed on this server</div>";
}


//Database
echo "<h2>DB connection:</h2>";

//echo $smspi->db->error;
if ($smspi->db->connect_errno) {
    echo "<div class='alert alert-danger'>Failed to connect: (".$smspi->db->connect_errno.") ".$smspi->db->connect_error."</div>";
} else {
    echo "<div class='alert alert-success'>$ICO_OK DB Connection ok</div>";

    //Check tables//

    $sql = "SHOW TABLES LIKE 'inbox';";
    //$
}

//Tables
$tables = array( 'inbox' , 'phonebook' , 'log_errors', 'log_sent' ,'services', 'queue' );

echo "<h2>Tables</h2>";

print_r($tables);
