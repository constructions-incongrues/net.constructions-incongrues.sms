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
//print_r($r);

if (!$r) {
    echo "<div class='alert alert-error'>";
    echo "Phone Number not found.";
    echo "</div>";
    die();
}

echo "<h1><i class='glyphicon glyphicon-book'></i> $number</h1>";
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
<!--
<a href='#' onclick='conv()' class='btn btn-default'><i class='glyphicon glyphicon-comment'></i> Read conversation</a>
-->
<a href='#' onclick='trash()' class='btn btn-danger pull-right' title=''><i class='glyphicon glyphicon-trash'></i> Del.</a>

</form>

<div id='more'></div>


<?php
// get conversation
$conv=$smspi->conversation($number);


if (count($conv)) {
    echo "<hr />";
    echo "<h3><i class='glyphicon glyphicon-comment'></i> <a href=#>Conversation</a></h3>";
    echo "<div id='conv'><div class='alert'>" . conversationHtml($conv) . "</div>";
}
?>
</div>
<script>
function sav(){

    var p ={
        'do':'numberSave',
        'id':$('#id').val(),
        'name':$('#name').val(),
        'comment':$('#comment').val()
    };
    $('#more').html("Saving...");
    $('#more').load('controller.php',p,function(x){
        //try{ eval(x); }
        //catch(e){ alert(x); }
    });
}

function trash()
{
    if(!confirm("Delete this phone number ?"))return false;
    var p={
        'do':'numberDelete',
        'id':$('#id').val()
    }
    $('#more').html("Deleting...");
    $('#more').load('controller.php',p,function(x){
        try{ eval(x); }
        catch(e){ alert(x); }
    });
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
<?php
/**
 * [conversationHtml description]
 * @param  array  $conv [description]
 * @return string       [description]
 */
function conversationHtml(array $conv)
{
    if (count($conv)<1) {
        return "<div class='alert alert-info'>No conversation with xxx</div>";
    }

    $html=[];
    foreach ($conv as $t => $v) {
        //echo $t;
        if (@$v['in']) {
            //$html[]=date("d/m/Y H:i", $t);
            $message = "<i class='glyphicon glyphicon-user'></i> " . $v['in'];
            $html[]="<h3>";
            $html[]="<span class='label label-primary pad4'>$message</span>";
            $html[]="<span class='pull-right small'>".date("d/m/Y H:i", $t)."</span>";
            $html[]="</h3>";
        }
        if (@$v['out']) {

            $message = "<i class='glyphicon glyphicon-cog'></i> " . $v['out'];
            $html[]="<h3>";
            $html[]="<span class='label label-default'>$message</span>";
            //$html[]="<span class='pull-right muted'>xx-xx-xxx</span>";
            $html[]="</h3>";
        }
    }

    return implode("", $html);
}

/*
function conversationHtml(array $conv)
{
    if (count($conv)<1) {
        return "<div class='alert alert-info'>No conversation with xxx</div>";
    }

    $html=[];
    foreach ($conv as $t => $v) {
        //echo $t;
        if (@$v['in']) {
            $html[]=date("d/m/Y H:i", $t);
            $message = "<i class='glyphicon glyphicon-user'></i> " . $v['in'];
            $html[]="<div class='alert alert-success'>$message</div>";
        }
        if (@$v['out']) {
            $message = "<i class='glyphicon glyphicon-hand-right'></i> " . $v['out'];
            $html[]="<div class='alert alert-info'>$message</div>";
        }
    }

    return implode("", $html);
}
*/