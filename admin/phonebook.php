<?php
/**
 * Browse the phonebook
 */
header('Content-Type: text/html; charset=ISO-8859-1');


require "../class.smspi.php";
$config = json_decode( file_get_contents( __DIR__ . '/../config.json') );

$smspi = new smspi( $config );


$sql = "SELECT * FROM phonebook WHERE 1;";

echo "<h1><i class='glyphicon glyphicon-book'></i> Phonebook</h1>";

$q = $smspi->db->query($sql) or die( $smspi->db->error );

echo "<table class='table table-striped table-condensed'>";
echo "<thead>";  
//echo "<th>id</th>";  
echo "<th>number</th>";  
echo "<th>name</th>";  
echo "<th>calls</th>";  
echo "<th>lastcall</th>";  
echo "</thead>";  
echo "<tbody>";  
while( $r = $q->fetch_assoc() )
{
	echo "<tr id=".$r['id'].">";
	//echo "<td>" . $r['id'];
	echo "<td>" . $r['phonenumber'];
	echo "<td>" . $r['name'];	
	echo "<td>" . $r['calls'];
	if(preg_match("/0000/", $r['lastcall']))$r['lastcall']='';
	echo "<td>" . $r['lastcall'];
	//echo "\n";
} 

echo "</tbody>";
echo "</table>";

?>
<a href='#' class='btn btn-default' onclick='addNumber()'> New phone number</a>
<script>
function addNumber(){
	var nn = prompt("Enter new number");
	if(!nn)return false;
	$("#main").load("controller.php",{'do':'addNumber','number':nn});
}
</script>

