<?php
/**
 * Browse the phonebook
 */
header('Content-Type: text/html; charset=utf-8');


require __DIR__."/../../vendor/autoload.php";

//use ConstructionsIncongrues\Curl;
use ConstructionsIncongrues\Sms\Gammu;
use ConstructionsIncongrues\Sms\SmsPi;

$config = json_decode(file_get_contents(__DIR__.'/../config.json'));
$smspi = new SmsPi($config);

include "menu.html";

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

while ($r = $q->fetch_assoc()) {
    echo "<tr id=".$r['id'].">";
    //echo "<td>" . $r['id'];
    echo "<td><a href='phonenumber.php?number=" . $r['phonenumber'] . "'>" . $r['phonenumber'] . "</a>";
    echo "<td>" . $r['name'];
    if (!$r['calls']) {
        $r['calls']='';
    }
    echo "<td>" . $r['calls'];
    if (preg_match("/0000/", $r['lastcall'])) {
        $r['lastcall']='';
    }
    echo "<td>" . $r['lastcall'];
    //echo "<td><a href=# onclick=test('".$r['phonenumber']."')><i class='glyphicon glyphicon-envelope'></i></a></td>";
    //echo "<td><a href='conversation.php?number=".$r['phonenumber']."'><i class='glyphicon glyphicon-retweet'></i></a></td>";
    echo "</tr>\n";
}

echo "</tbody>";
echo "</table>";
?>
<hr />
<div id='more'></div>

<a href='#' class='btn btn-default' onclick='addNumber()'><i class='glyphicon glyphicon-plus-sign'></i> New phone number</a>
<script>
function addNumber()
{
    var nn = prompt("Enter new number");
    if(!nn)return false;
    $("#more").html("Saving new number...");
    $("#more").load("controller.php",{'do':'numberAdd','number':nn},function(x){
        try{eval(x);}
        catch(e){alert(x);}
    });
}


</script>