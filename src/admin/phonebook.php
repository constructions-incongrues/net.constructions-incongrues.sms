<?php
/**
 * Browse the phonebook
 */
header('Content-Type: text/html; charset=utf-8');


//require __DIR__."/../../vendor/autoload.php";

//use ConstructionsIncongrues\Curl;
//use ConstructionsIncongrues\Sms\Gammu;
//use ConstructionsIncongrues\Sms\SmsPi;

//$config = json_decode(file_get_contents(__DIR__.'/../config.json'));
//$smspi = new SmsPi($config);

include "menu.html";
?>

<h1><i class='glyphicon glyphicon-book'></i> Phonebook</h1>


<div class='form-inline'>

 <div class="form-group">
 <h2><i class='glyphicon glyphicon-book'></i> Phonebook</h2>
</div>

 <div class="form-group">
    <label class="sr-only" for="searchstr">Search</label>
    <input type="text" class="form-control" id="searchstr" placeholder="Search">
  </div>


<div class="btn-group pull-right">
  <button type="button" class="btn btn-default"><i class='glyphicon glyphicon-list'></i> All</button>
    <button type="button" class="btn btn-default">Errors</button>
  <button type="button" class="btn btn-default">Warning</button>
  <button type="button" class="btn btn-default">Notice</button>
</div>

</div>


<?php
/*
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
*/

?>

<div id='logs'></div>
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

function getNums(){
    var p={
        'do':'phonebook',
        'filter':$('#searchstr').val()
    };
    $('#logs').html("Loading...");
    $('#logs').load('controller.php', p, function(x){
        try{
            o=eval(x);
            display(o);
        }
        catch(e){
            alert(x);
        }
    });
}


function display(r){
    //console.log('dispLog()',json);
    
    var tab=[];
    tab.push("<table class='table table-condensed table-striped'>");
    tab.push("<thead>");
    tab.push("<th>name</th>");
    tab.push("<th width=150>number</th>");
    tab.push("<th>calls</th>");
    tab.push("<th width=140>last call</th>");
    //tab.push("<th>calls</th>");
    tab.push("</thead>");
    tab.push("<tbody>");
    for(var i=0;i<r.length;i++){
        tab.push("<tr>");
        tab.push("<td>"+r[i].name);
        tab.push("<td>"+r[i].phonenumber);
        tab.push("<td>"+r[i].calls);
        tab.push("<td>"+r[i].lastcall);
        tab.push("</tr>");
    }
    tab.push("</tbody>");
    tab.push("</table>");
    $('#logs').html(tab.join(''));
    $('table').tablesorter();
}

$( document ).ready(function() {
    $('#searchstr').change(function(){
        console.log("changed"); 
        getNums();
    });
    getNums();
});

</script>