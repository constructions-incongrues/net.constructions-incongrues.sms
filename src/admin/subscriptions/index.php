<?php
/**
 * Subscriptions
 */
header('Content-Type: text/html; charset=utf-8');
require __DIR__."/../../../vendor/autoload.php";

use ConstructionsIncongrues\Sms\SmsPi;
use ConstructionsIncongrues\Sms\SmsAdmin;

$config = json_decode(file_get_contents(__DIR__.'/../../config.json'));
$admin=new SmsAdmin($config);
$smspi=new SmsPi($config);
$admin->title("Subscriptions");
$admin->printPublic();
?>

<h2><i class='glyphicon glyphicon-retweet'></i> Subscriptions <small></small>
<a href=# class='btn btn-primary pull-right' onclick='pop()'><i class="glyphicon glyphicon-plus"></i> New subscription</a>
</h2>

<?php
$db=$admin->db;
$sql = "SELECT * FROM sms.subscriptions WHERE 1;";
$q=$db->query($sql) or die("<pre>$sql</pre>");

$serviceNames=$smspi->serviceNames();

echo "<hr />";

echo "<table class='table table-condensed table-striped'>";
echo "<thead>";
echo "<th width=120>Number</th>";
echo "<th>Name</th>";
echo "<th>Service</th>";
echo "<th width=100>Freq.</th>";
echo "<th width=180>Last Call</th>";
echo "<th width=30>x</th>";
echo "</thead>";
echo "<tbody>";

while ($r=$q->fetch_assoc()) {

    $n=$smspi->numberData($r['phonenumber']);
    //print_r($n);
    $class='';
    if (!in_array($r['service'], $serviceNames)) {
        $class='text-muted';
    }
    
    echo "<tr class=$class>";
    //echo "<td>".$r['id'];
    echo "<td width=120><a href=../phonenumber/?id=".$r['phonenumber'].">".$n['phonenumber'];
    echo "<td>".$n['name'];
    echo "<td>".ucfirst($r['service']);
    echo "<td width=100>".$r['frequency'];
    echo "<td width=180>".$r['last_call'];
    echo "<td width=30>"."<i class='glyphicon glyphicon-trash' onclick=unsubscribe(".$r['id'].") title='Unsubscribe'></i>";
}

echo "</tbody>";
echo "</table>";

include "modalwindow.php";
?>

<div id='more'></div>

<script>
function unsubscribe(id){
    console.log('unsubscribe()');
    if(!confirm("Unsubscribe ?"))return false;
    $('#more').html("unsubscribe...");
    $('#more').load("ctrl.php",{'do':'unsubscribe', 'id':id},function(x){
        try{eval(x);}
        catch(e){alert(x);}
    });
}

$( document ).ready(function(){
    //getList();
});
</script>