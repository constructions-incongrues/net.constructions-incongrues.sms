<?php
/**
 * Browse list of sms services
 */
header('Content-Type: text/html; charset=utf-8');

//require __DIR__."/../../vendor/autoload.php";
//use ConstructionsIncongrues\Sms\SmsPi;
//$config = json_decode(file_get_contents(__DIR__.'/../config.json'));
//$smspi = new SmsPi($config);
include "menu.html";
?>

<h2><i class='glyphicon glyphicon-list'></i> Services</h2>

<div id='more'></div>

<a href=# class='btn btn-default' onclick='newService()'><i class="glyphicon glyphicon-plus"></i> New service</a>
<script>
function newService()
{
    var ns = prompt("Enter service name");
    if(!ns)return false;
    var p={
        'do':'serviceCreate',
        'name':ns
    };
    $('#more').load("controller.php", p, function(x){
        try{eval(x);}
        catch(e){alert(x);}
    });
}

function getList(){
    var p={
        'do':'services',
        'filter':$('#searchstr').val()
    };
    $('#more').html("Loading...");
    $('#more').load('controller.php', p, function(x){
        try{
            o=eval(x);
            display(o);
        }
        catch(e){alert(x);}
    });
}

function display(r){
    var tab=[];
    tab.push("<table class='table table-condensed table-striped'>");
    tab.push("<thead>");
    tab.push("<th>#</th>");
    tab.push("<th>name</th>");
    tab.push("<th>comment</th>");
    tab.push("<th>calls</th>");
    tab.push("</thead>");
    tab.push("<tbody>");
    for(var i=0;i<r.length;i++){
        tab.push("<tr>");
        tab.push("<td>"+r[i].id);
        tab.push("<td><a href='service.php?id="+r[i].id+"'>"+r[i].name);
        tab.push("<td>"+r[i].comment);
        tab.push("<td>"+r[i].calls);
        tab.push("</tr>");
    }
    tab.push("</tbody>");
    tab.push("</table>");
    $('#more').html(tab.join(''));
    $('table').tablesorter();
}

$( document ).ready(function(){
    getList();
});
</script>