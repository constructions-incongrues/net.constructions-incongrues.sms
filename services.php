<?php
/**
 * Browse list of sms services
 */
header('Content-Type: text/html; charset=ISO-8859-1');
?>
<html>
<head>
<title>SMS Services</title></head>
<body>
<pre>
<?php
echo "SMS Services\n";
echo "---------------------------------------------------------\n";

include __DIR__ . "/config.php";
$db = new mysqli( $dbhost, $dbuser, $dbpass , $dbname );

$sql = "SELECT * FROM services WHERE 1;";
$q=$db->query($sql) or die( $db->error );

while( $r=$q->fetch_assoc() )
{
	//print_r($r);
	echo $r['id'];
	echo "\t";
	echo $r['name'];
	echo "\t";
	echo $r['url'];	
	echo "\t";
	echo $r['comment'];
	echo "\n";
} 



