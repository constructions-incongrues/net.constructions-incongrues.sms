<?php
/**
 * SMS Admin :: inbox
 */
header('Content-Type: text/html; charset=utf-8');

require __DIR__."/../../../vendor/autoload.php";

use ConstructionsIncongrues\Sms\SmsAdmin;
use ConstructionsIncongrues\Sms\SmsPi;

$config = json_decode(file_get_contents(__DIR__.'/../../config.json'));
$smspi = new SmsPi($config);

$admin=new SmsAdmin($config);
$admin->title('Inbox');
$admin->printPublic();

echo "<h1><i class='glyphicon glyphicon-import'></i> Inbox</h1>";
//echo "---------------------------------------------------------\n";

//$msgs = $smspi->inbox();
$sql = "SELECT * FROM msg_in WHERE 1 ORDER BY i DESC LIMIT 30;";
$q = $smspi->db->query($sql) or die( $smspi->db->error );

echo "<table class='table table-condensed table-striped'>";
echo "<thead>";
//echo "<th>id</th>";
echo "<th>From</th>";
echo "<th>Message</th>";
echo "<th width=150>sent</th>";
echo "</thead>";

echo "<tbody>";
while ($r = $q->fetch_assoc()) {
    //print_r( $r );
    echo "<tr id=" . $r['i'] . ">";
    $number_id=$smspi->numberId($r['remote_number']);
    $name = $smspi->numberName($number_id);
    if ($name) {
        echo "<td><a href='../phonenumber/?id=$number_id'>".$name."</a></td>";
    } else {
        echo "<td><a href='../phonenumber/?id=$number_id'>".$r['remote_number']."</a></td>";
    }
    
    
    // message
    echo "<td>" . $r['body'];
    $r['sent'] = str_replace(date('Y-m-d'), '', $r['sent']);
    echo "<td width=150>" . substr($r['sent'], 0, 16);
    echo "</tr>\n";
}
echo "</tbody>";
echo "</table>";
