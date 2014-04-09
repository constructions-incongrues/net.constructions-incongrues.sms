<?php
/**
 * Sms service details
 */
header('Content-Type: text/html; charset=utf-8');
include "menu.html";

require __DIR__."/../../vendor/autoload.php";

use ConstructionsIncongrues\Sms\SmsPi;

$config = json_decode(file_get_contents(__DIR__.'/../config.json'));
$smspi = new SmsPi($config);
$r=$smspi->service($_GET['id']);
?>

<h2><i class='glyphicon glyphicon-list'></i> Service details</h2>
<hr />

<form role="form">

    <input type="hidden" id="id" value="<?php echo $r['id']?>">
    <input type="hidden" id="phonenumber" value="<?php echo $r['phonenumber']?>">

    <div class="form-group">
        <label for="name">Service Name</label>
        <input type="text" class="form-control" id="name" placeholder="Service name" value="<?php echo $r['name']?>">
    </div>

    <div class="form-group">
        <label for="name">Service Url</label>
        <input type="text" class="form-control" id="url" placeholder="Service url" value="<?php echo $r['url']?>">
    </div>


  <div class="form-group">
    <label for="comment">Comment</label>
    <input type="text" class="form-control" id="comment" placeholder="Enter comment" value="<?php echo $r['comment']?>">
  </div>
<!--
  <div class="form-group">
    <label for="lastcall">Last call</label>
    <input type="text" class="form-control" id="lastcall" placeholder="Last call" readonly value="<?php echo $r['lastcall']?>">
  </div>
-->
  <hr />

  <a href='#' onclick='sav()' class='btn btn-primary'><i class='glyphicon glyphicon-ok'></i> Save service details</a>
  <a href='#' onclick='serviceTest()' class='btn btn-default'><i class='glyphicon glyphicon-comment'></i> Test url</a>

</form>



<div id='more'></div>

<script>

function sav(){
    var p={
        'do':'serviceSave',
        'id':$('#id').val(),
        'name':$('#name').val(),
        'url':$('#url').val(),
        'comment':$('#comment').val()
    }
    $('#more').html("Saving...");
    $('#more').load("controller.php",p,function(x){
        try{
            eval(x);
        }
        catch(e){
            alert(e);
        }
    });
}

function serviceTest()
{
    $('#more').html("Testing " + $('#url').val() );
    $('#more').load( $('#url').val() );//todo : fixit
}


$( document ).ready(function() {
    //getList();
});

</script>