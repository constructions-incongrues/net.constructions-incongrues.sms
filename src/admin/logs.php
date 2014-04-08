<?php
/**
 * logs
 */
header('Content-Type: text/html; charset=utf-8');

require __DIR__."/../../vendor/autoload.php";

//use ConstructionsIncongrues\Curl;
//use ConstructionsIncongrues\Sms\Gammu;
//use ConstructionsIncongrues\Sms\SmsPi;

//$config = json_decode(file_get_contents(__DIR__.'/../config.json'));
//$smspi = new SmsPi($config);

include "menu.html";

echo "<h2><i class='glyphicon glyphicon-list'></i> Logs</h2>";
//echo "---------------------------------------------------------\n";
?>

<div class='form-inline'>

 <div class="form-group">
    <label class="sr-only" for="searchstr">Search</label>
    <input type="text" class="form-control" id="searchstr" placeholder="Search">
  </div>


<div class="btn-group">
  <button type="button" class="btn btn-default"><i class='glyphicon glyphicon-list'></i> All</button>
    <button type="button" class="btn btn-default">Errors</button>
  <button type="button" class="btn btn-default">Warning</button>
  <button type="button" class="btn btn-default">Notice</button>
</div>

</div>



<div id='logs'></div>

<script>

function getLogs(){
	var p={
		'do':'getLogs',
		'filter':$('#searchstr').val()
	};
	$('#logs').html("Loading...");
	$('#logs').load('controller.php', p, function(x){
		try{
			o=eval(x);
			dispLog(o);
		}
		catch(e){
			alert(x);
		}
	});
}

function dispLog(r){
	//console.log('dispLog()',json);
	
	var tab=[];
	tab.push("<table class='table table-condensed table-striped'>");
	tab.push("<thead>");
	tab.push("<th>status</th>");
	tab.push("<th>error</th>");
	tab.push("<th>time</th>");
	tab.push("</thead>");
	tab.push("<tbody>");
	for(var i=0;i<r.length;i++){
		tab.push("<tr>");
		tab.push("<td>"+r[i].status);
		tab.push("<td>"+r[i].error);
		tab.push("<td>"+r[i].time);
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
		getLogs();
	});
    getLogs();
});
</script>
