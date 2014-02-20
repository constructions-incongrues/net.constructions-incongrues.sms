<?php
/**
 * SMS Admin :: sent
 */
header('Content-Type: text/html; charset=utf-8');

require "../class.smspi.php";
$config = json_decode( file_get_contents( __DIR__ . '/../config.json') );

$smspi = new smspi( $config );

include "menu.html";

echo "<h1><i class='glyphicon glyphicon-export'></i> Sent</h1>";
//echo "---------------------------------------------------------\n";

//$msgs = $smspi->inbox();
$sql = "SELECT * FROM log_sent WHERE 1 ORDER BY id DESC LIMIT 30;";
$q = $smspi->db->query( $sql ) or die( $smspi->db->error );

echo "<table class='table table-condensed table-striped'>";
echo "<thead>";  
//echo "<th>id</th>";  
echo "<th>number</th>";  
echo "<th>message</th>";  
echo "<th width='130px'>sent</th>";  
echo "</thead>";  

echo "<tbody>";
while( $r = $q->fetch_assoc() )
{
	//print_r( $r );
	echo "<tr id=" . $r['id'] . ">";
	echo "<td title='" . $smspi->numberName($r['number']) . "'>" . $r['number'];
	echo "<td>" . $r['message'];
	$r['time'] = str_replace( date('Y-m-d'), '', $r['time'] );
	echo "<td width=150>" . $r['time'];
	echo "</tr>\n";
}
echo "</tbody>";
echo "</table>";
