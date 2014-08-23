<?php
/**
 * SMS Admin :: sent
 */
header('Content-Type: text/html; charset=utf-8');
require __DIR__."/../../../vendor/autoload.php";

use ConstructionsIncongrues\Sms\SmsAdmin;
use ConstructionsIncongrues\Sms\SmsPi;

$config = json_decode(file_get_contents(__DIR__.'/../../config.json'));
$smspi = new SmsPi($config);

$admin=new SmsAdmin($config);
$admin->title("Sent");
$admin->printPublic();

echo "<h1><i class='glyphicon glyphicon-export'></i> Sent</h1>";
//echo "---------------------------------------------------------\n";

//$msgs = $smspi->inbox();
$sql = "SELECT * FROM msg_out WHERE time > NOW() - INTERVAL 1 DAY ORDER BY id DESC LIMIT 30;";
$q = $smspi->db->query($sql) or die( $smspi->db->error . "<pre>$sql</pre>" );

echo "<table class='table table-condensed table-striped'>";
echo "<thead>";
//echo "<th>id</th>";
echo "<th>Sent to</th>";
//echo "<th>name</th>";
echo "<th>message</th>";
echo "<th width='100px'>sent</th>";
echo "</thead>";

echo "<tbody>";
while ($r = $q->fetch_assoc()) {
    //print_r( $r );
    echo "<tr id=" . $r['id'] . ">";
    $number_id=$smspi->numberId($r['number']);
    $name = $smspi->numberName($number_id);
    if ($name) {
        echo "<td><a href='../phonenumber/?id=$number_id'>".$name."</a>";
    } else {
        echo "<td><a href='../phonenumber/?id=$number_id'>".$r['number']."</a>";
    }
    
    echo "<td>" . $r['message'];
    
    if (preg_match("/".date('Y-m-d')."/", $r['time'])) {
        $r['time'] = "<span class='label label-success'>".str_replace(date('Y-m-d'), '', $r['time']);
    } else {
        $r['time'] = "<span class='label label-default'>" . substr($r['time'], 0, 10);
    }
    echo "<td>" . $r['time'];
    echo "</tr>\n";
}
echo "</tbody>";
echo "</table>";
