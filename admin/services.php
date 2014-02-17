<?php
/**
 * Browse list of sms services
 */
header('Content-Type: text/html; charset=ISO-8859-1');

require "../class.smspi.php";
$config = json_decode( file_get_contents( __DIR__ . '/../config.json') );

$smspi = new smspi( $config );

echo "<h1><i class='glyphicon glyphicon-list'></i> Services</h1>";
//echo "---------------------------------------------------------\n";

$services = $smspi->serviceList();

echo "<table class='table table-condensed table-striped'>";
echo "<thead>";  
//echo "<th>id</th>";  
echo "<th>name</th>";  
echo "<th>url</th>";  
echo "<th>comment</th>";
echo "<th>calls</th>";
echo "</thead>";  

echo "<tbody>";  
foreach( $services as $k=>$r )
{
	//print_r( $r );
	echo "<tr id=" . $r['id'] . ">";
	echo "<td><a href=#>" . $r['name'] . "</a>";
	echo "<td>" . $r['url'];
	echo "<td>" . $r['comment'];
	if(!$r['calls'])$r['calls']='';
	echo "<td>" . $r['calls'];
}
echo "</tbody>";
echo "</table>";
?>

<a href=# class='btn btn-default' onclick='newService()'> New service</a>
<script>
//
function newService()
{
	var ns = prompt("Enter service name");
	if(!ns)return false;
	/*
	$('#main').load("controller.php", {}, function(x){
		try{eval(x);}
		catch{alert(x);}
	});
	*/
}

</script>