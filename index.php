<?php
/**
 * Browse last received, sms and check service status
 */
header('Content-Type: text/html; charset=ISO-8859-1');
?>
<html>
<head>
<title>SMS Inbox</title></head>
<body>
<pre>
<?php
echo date('c') . "\n";
echo "---------------------------------------------------------\n";

include __DIR__ . "/config.php";
$db = new mysqli( $dbhost, $dbuser, $dbpass , $dbname );
//$db = new SQLite3( __DIR__ . '/smsdb.db');

$sql = "SELECT * FROM inbox WHERE 1 ORDER BY i DESC;";
$q=$db->query($sql) or die( $db->error );

while( $r=$q->fetch_assoc() )
{
	//print_r($r);
	echo $r['sent'];
	echo "\t";
	echo $r['remote_number'];
	echo "\t";
	echo $r['status'];	
	echo "\t";
	echo $r['body'];
	echo "\n";
} 

?>
<script>
setTimeout(function(){document.location.href='?';},10000);
</script>