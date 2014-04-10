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
    tab.push("<th width=140>last call</th>");
    tab.push("<th>calls</th>");
    tab.push("</thead>");
    tab.push("<tbody>");
    for(var i=0;i<r.length;i++){
        if(!r[i].name)r[i].name="?"
        tab.push("<tr>");
        tab.push("<td><a href='phonenumber.php?number="+r[i].phonenumber+"'>"+r[i].name);
        tab.push("<td><a href='phonenumber.php?number="+r[i].phonenumber+"'>"+r[i].phonenumber);
        tab.push("<td>"+r[i].lastcall);
        tab.push("<td>"+r[i].calls);
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