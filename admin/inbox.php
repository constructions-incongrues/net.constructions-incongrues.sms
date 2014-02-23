<?php
/**
 * SMS Admin :: inbox
 */
header('Content-Type: text/html; charset=utf-8');

require "../class.smspi.php";
$config = json_decode( file_get_contents( __DIR__ . '/../config.json') );

$smspi = new smspi( $config );

include "menu.html";

echo "<h1><i class='glyphicon glyphicon-import'></i> Inbox</h1>";
//echo "---------------------------------------------------------\n";

//$msgs = $smspi->inbox();
$sql = "SELECT * FROM inbox WHERE 1 ORDER BY i DESC LIMIT 30;";
$q = $smspi->db->query( $sql ) or die( $smspi->db->error );

echo "<table class='table table-condensed table-striped'>";
echo "<thead>";  
//echo "<th>id</th>";  
echo "<th>number</th>";  
echo "<th>body</th>";  
echo "<th>sent</th>";  
echo "</thead>";  

echo "<tbody>";
while( $r = $q->fetch_assoc() )
{
	//print_r( $r );
	echo "<tr id=" . $r['i'] . ">";
	$name = $smspi->numberName( $r['remote_number'] );
	echo "<td title='$name'>" . $r['remote_number'];
	echo "<td>" . $r['body'];
	$r['sent'] = str_replace( date('Y-m-d'), '', $r['sent'] );
	echo "<td>" . $r['sent'];
	echo "</tr>\n";
}
echo "</tbody>";
echo "</table>";
