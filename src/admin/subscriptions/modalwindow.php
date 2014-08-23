<?php
$slist=$smspi->serviceList();
//print_r($slist);
?>
<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel"><i href=# class='glyphicon glyphicon-retweet'></i> New subscription</h4>
      </div>

      <div class="modal-body">
        
        <form role="form">
        
            <div class="row">
            
              <div class="col-md-6">
                <div class="form-group">
                    <label for="phonenumber">Phonenumber</label>
                    <input type="text" class="form-control" id="phonenumber" placeholder="Phone number">
                  </div>
              </div>
              
              <div class="col-md-6">
                <div class="form-group">
                    <label>Check</label>
                    <div id='numberCheck'><a href='../phonebook/' class='btn btn-primary'>Phonebook</a></div>
                  </div>
              </div>            
            </div>



          
          <div class="form-group">
            <label for="service">Service</label>
            <select id="service" class="form-control">
            <option value=''>Select a service</option>
            <?php
            foreach($slist as $service){
                echo "<option value='".$service['id']."'>".ucfirst($service['name'])."</option>";
            }
            ?>
            </select>
          </div>
          
        </form>

      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" onclick='saveSub()'>Save subscription</button>
      </div>

    </div>
  </div>
</div>

<div id='moreSub'></div>

<script>
function pop(){
    $("#myModal").modal();
    
    window.setTimeout(function(){$('#phonenumber').focus();}, 500);
}

function saveSub(){

    $('#moreSub').html("save");
    if(!$('#phonenumber').val()){$('#phonenumber').focus();return false};
    if(!$('#service').val()){$('#service').focus();return false};
    
    var p={
        'do':'subscribe',
        'phonenumber':$('#phonenumber').val(),
        'service_id':$('#service').val()
    };

    $('#moreSub').load("ctrl.php", p, function(x){
        try{eval(x);}
        catch(e){alert(x);}
    });
}

$(function(){
    console.log('ready');
    $('#phonenumber').change(function(o){
        console.log('phonenumber change',o);
        var p={'do':'numberCheck','number':$('#phonenumber').val()};
        $('#numberCheck').html("Checking...");
        $('#numberCheck').load("./ctrl.php", p, function(x){
            console.log(x);
        });
    });
});
</script>