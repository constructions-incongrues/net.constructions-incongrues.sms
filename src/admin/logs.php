<?php
/**
 * logs
 */
header('Content-Type: text/html; charset=utf-8');

require __DIR__."/../../vendor/autoload.php";

//use ConstructionsIncongrues\Curl;
use ConstructionsIncongrues\Sms\Gammu;
use ConstructionsIncongrues\Sms\SmsPi;

$config = json_decode(file_get_contents(__DIR__.'/../config.json'));
$smspi = new SmsPi($config);

include "menu.html";

echo "<h1><i class='glyphicon glyphicon-list'></i> Logs</h1>";
//echo "---------------------------------------------------------\n";

$logs = $smspi->logs();

echo "<table class='table table-condensed table-striped'>";
echo "<thead>";
//echo "<th>id</th>";  
echo "<th>status</th>";
echo "<th>error</th>";
echo "<th>time</th>";
echo "</thead>";

echo "<tbody>";
foreach ($logs as $k => $r) {
    //print_r( $r );
    echo "<tr id=" . $r['id'] . ">";
    echo "<td>" . $r['status'];
    echo "<td>" . $r['error'];
    $r['time'] = str_replace(date('Y-m-d'), '', $r['time']);
    echo "<td>" . $r['time'];
}
echo "</tbody>";
echo "</table>";
