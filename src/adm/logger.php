<?php
include $_SERVER['DOCUMENT_ROOT']."/librerie/general.php";
chk_access(0);
connect();
show_premain();
?>
<script>
$(function(){
	var act;
    
	$(".splog").click(function(){
       	$.ajax({                                      
        	url: 'readlog.php',
        	data: "f="+$(this).text(),
        	dataType: 'json',                
        	success: function(data)
        	{
          		$("#txt").text(data);
        	} 
      	});
      
    	$("#"+act).css("color","black");
      	$(this).css("color","red");
      	act=$(this).attr("id");
      	$("#del").attr("disabled",false);
   });
    
   $("#del").click(function(){
		$.ajax({                                      
			url: 'dellog.php',
        	data: "f="+$("#"+act).text(),
        	dataType: 'json',                
        	success: function(data)
        	{
          		$("#"+act).remove();
          		$("#txt").text("");
          		$("#del").attr("disabled",true);
        	} ,
        	error: function()
        	{
          		alert("errore");
        	}
      	});
    });
});
</script>

<style>
#par{
  min-width:150px;
  overflow-x:scroll;
}
#lista {
  padding-right:5px;
  text-align:right;
  width:20%;
  min-width:130px;
  margin:auto;
  top:0;
  display:inline-block;
  height: 80vh;
  overflow-y:scroll;
}

#txtcont {
  vertical-align: top;
  width:70%;
  display:inline-block;
  height: 80vh;
}
 
#txt{
  width: 80%;
  height: 70vh;
}
  
#del{
  display:inline-block;
  width:80%;
}
</style>

<h2>Log di utilizzo</h2>

<div id="par">
	<div id="lista">
<?php
$cont=array_diff(scandir($_SERVER['DOCUMENT_ROOT']."/logs",SCANDIR_SORT_DESCENDING), array('..', '.'));
$i=0;
foreach($cont as $g)
{
  echo "<span id='sp$i' class='splog'>$g<br></span>";
  $i++;
}
?>
	</div>
  
	<div id="txtcont">
		<textarea id="txt"></textarea><br>
		<button id="del" class='btn btn-warning' disabled>Elimina</button>
	</div>
</div>

<?php show_postmain(); ?>