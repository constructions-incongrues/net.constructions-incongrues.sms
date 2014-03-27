<?php
/**
 * SMS::Phonenumber
 */
header('Content-Type: text/html; charset=utf-8');


require __DIR__."/../../vendor/autoload.php";

//use ConstructionsIncongrues\Curl;
use ConstructionsIncongrues\Sms\Gammu;
use ConstructionsIncongrues\Sms\SmsPi;

$config = json_decode(file_get_contents(__DIR__.'/../config.json'));
$smspi = new SmsPi($config);

include "menu.html";

$number = trim(@$_GET['number']);
$number = preg_replace("/^33/", '+33', $number);

if (!$number) {
    die("<div class='alert alert-danger'>Not a number</div>");
}
//print_r($_GET);
$r=$smspi->numberData($number);

echo "<h1><i class='glyphicon glyphicon-book'></i> $number</h1>";

//print_r($r);
?>

<form role="form">

    <input type="hidden" id="id" value="<?php echo $r['id']?>">
    <input type="hidden" id="phonenumber" value="<?php echo $r['phonenumber']?>">
 
    <div class="form-group">
    <label for="name">Name</label>
    <input type="text" class="form-control" id="name" placeholder="Enter name" value="<?php echo $r['name']?>">
  </div>

  <div class="form-group">
    <label for="comment">Comment</label>
    <input type="text" class="form-control" id="comment" placeholder="Enter comment" value="<?php echo $r['comment']?>">
  </div>

  <div class="form-group">
    <label for="lastcall">Last call</label>
    <input type="text" class="form-control" id="lastcall" placeholder="Last call" readonly value="<?php echo $r['lastcall']?>">
  </div>


  <div class="checkbox">
    <label>
      <input type="checkbox"> Blocked
    </label>
  </div>

  <hr />
  
  <a href='#' onclick='sav()' class='btn btn-primary'><i class='glyphicon glyphicon-ok'></i> Save number</a>
  <a href='#' onclick='sms()' class='btn btn-default'><i class='glyphicon glyphicon-envelope'></i> Send a message</a>
  <a href='#' onclick='conv()' class='btn btn-default'><i class='glyphicon glyphicon-comment'></i> Read conversation</a>

</form>

<div id='more'></div>

<script>
function sav(){
    
    var p ={
        'do':'numberSave',
        'id':$('#id').val(),
        'name':$('#name').val(),
        'comment':$('#comment').val()
    };
    
    $('#more').load('controller.php',p,function(x){
        //try{ eval(x); }
        //catch(e){ alert(x); }
    });
}

function conv(){
    //var num=$('#phonenumber').val();
    document.location.href='conversation.php?number='+$('#phonenumber').val();
}

function sms()
{
    var num=$('#phonenumber').val();
    var msg=prompt("Enter message for "+num);
    if(!msg)return false;
    $("#more").load("controller.php",{'do':'numberTest', 'number':num, 'body':msg});
}

$( document ).ready(function() {
    $('#name').focus();
});

</script>