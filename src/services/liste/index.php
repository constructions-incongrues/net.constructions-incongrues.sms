<?php
/**
 * SMS script, 
 * return the list of commands
 */
header('Content-Type: text/html; charset=utf-8');

include __DIR__ . "/../../class.smspi.php";

//require "../class.smspi.php";

$config = json_decode( file_get_contents( __DIR__ . '/../../config.json') );
$smspi = new smspi( $config );

// Get the list of services //
$sql = "SELECT name FROM services WHERE 1 order by name;";
$q = $smspi->db->query( $sql ) or die( $sql );

$cmds = Array();

while( $r = $q->fetch_assoc() ) {
	$cmds[] = $r['name'];
}


die( implode(" " , $cmds ) );

