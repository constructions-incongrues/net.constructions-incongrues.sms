<?php
/**
 * Sms service details
 */
header('Content-Type: text/html; charset=utf-8');
require __DIR__."/../../../vendor/autoload.php";

use ConstructionsIncongrues\Sms\SmsAdmin;
use ConstructionsIncongrues\Sms\SmsPi;

$config = json_decode(file_get_contents(__DIR__.'/../../config.json'));

$admin=new SmsAdmin($config);
$admin->title("Service");
$admin->printPublic();


$smspi = new SmsPi($config);
$r=$smspi->service($_GET['id']);
?>

<h2><i class='glyphicon glyphicon-list'></i> Service details <small><?php echo $r['calls']?> calls</small></h2>
<hr />


<form role="form">

    <input type="hidden" id="id" value="<?php echo $r['id']?>">
    <input type="hidden" id="phonenumber" value="<?php echo $r['phonenumber']?>">

    <div class="row">
      <div class="col-md-6">
            <div class="form-group">
                <label for="name">Service Name</label>
                <input type="text" class="form-control" id="name" placeholder="Service name" value="<?php echo $r['name']?>">
            </div>
      </div>

      <div class="col-md-6">

        <div class="form-group">
            <label for="name">Service Url</label>
            <input type="text" class="form-control" id="url" placeholder="Service url" value="<?php echo $r['url']?>">
        </div>

      </div>

    </div>






  <div class="form-group">
    <label for="comment">Comment</label>
    <input type="text" class="form-control" id="comment" placeholder="Enter comment" value="<?php echo $r['comment']?>">
  </div>



  <hr />

  <a href='#' onclick='sav()' class='btn btn-primary'><i class='glyphicon glyphicon-ok'></i> Save service details</a>

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
    $('#more').load("../controller.php",p,function(x){
        try{
            eval(x);
            serviceTest();
        }
        catch(e){
            alert(e);
        }
    });
}

function serviceTest()
{
    if(!$('#url').val())return false;

    $('#more').html("<div class=jumbotron><h1>" + $('#url').val()+"</h1></div>" );

    //todo , use $.ajax, check errors, etc
    $('#more').load( $('#url').val(),{},function(x){
        $('#more').html("<div class='alert alert-info'><i class='glyphicon glyphicon-comment'></i> " + x + "</div>");
        $('#more').html("<div class=jumbotron onclick=serviceTest()><h1>" + x + "</h1></div>");
    } );
}

$( document ).ready(function() {
    serviceTest();
});
</script>