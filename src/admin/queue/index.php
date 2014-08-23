<?php
/**
 * SMS Admin :: sent
 */
header('Content-Type: text/html; charset=utf-8');

require __DIR__."/../../../vendor/autoload.php";

use ConstructionsIncongrues\Sms\SmsAdmin;
use ConstructionsIncongrues\Sms\SmsPi;

$config = json_decode(file_get_contents(__DIR__.'/../../config.json'));
$smspi = new SmsPi($config);

$admin=new SmsAdmin($config);
$admin->title("Queue");
$admin->printPublic();

echo "<h1><i class='glyphicon glyphicon-export'></i> Queue</h1>";
echo "<a href=# class='btn btn-default pull-right' id='btnclear'>Clear</a>";
//echo "---------------------------------------------------------\n";

//$msgs = $smspi->msgQueue();
function msgQueue()
{
    global $smspi;
    $sql = "SELECT * FROM msg_queue WHERE 1 ORDER BY q_id DESC LIMIT 30;";
    $q = $smspi->db->query($sql) or die( $smspi->db->error );
    $dat=[];
    while ($r=$q->fetch_assoc()) {
        $dat[]=$r;
    }
    return $dat;
}

$q=msgQueue();

echo "<table class='table table-condensed table-striped'>";
echo "<thead>";
//echo "<th>id</th>";
echo "<th>number</th>";
echo "<th>name</th>";
echo "<th>message</th>";
echo "<th width='150px'>sent</th>";
echo "</thead>";

echo "<tbody>";
//while ($r = $q->fetch_assoc()) {
foreach ($q as $k => $r) {
    //print_r( $r );
    echo "<tr id=" . $r['q_id'] . ">";
    $number_id=$smspi->numberId($r['q_number']);
    echo "<td><a href='../phonenumber/?id=$number_id'>" . $r['q_number'] . "</a>";
    echo "<td><a href='../phonenumber/?id=$number_id'>" . $smspi->numberName($number_id) . "</a>";
    echo "<td>" . $r['q_body'];
    $r['q_sendtime'] = str_replace(date('Y-m-d'), '', $r['q_sendtime']);
    echo "<td width=150>" . $r['q_sendtime'];
    echo "<td><a href='#' onclick='trashIt(".$r['q_id'].")'><i class='fa fa-trash-o'></i>trash</a>";
    echo "</tr>\n";
}
echo "</tbody>";
echo "</table>";

echo "<i class=muted>".count($q)." messages in the queue</i>";
?>
<div id='more'></div>
<script>
function trashIt(id){
    $('#more').html('deleting..');
    $('#more').load('ctrl.php',{'do':'delQ','id':id},function(x){
        try{eval(x);}
        catch(e){
            alert(x);
        }
    });
}

$(function(){
    $('#btnclear').click(function(){
        if(!confirm("Clear Queue ?"))return false;
        $('#more').load('ctrl.php',{'do':'clearAll'},function(x){
            try{eval(x);}
            catch(e){alert(x);}
        });
    });
});
</script>
