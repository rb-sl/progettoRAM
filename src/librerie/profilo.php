<?php 
include $_SERVER['DOCUMENT_ROOT']."/librerie/general.php";
connect();
chk_access();
show_premain("Profilo");

$ret=query("SELECT * FROM PROFESSORI,SCUOLE WHERE id_prof=".$_SESSION['id']." AND fk_scuola=id_scuola");
$prof=$ret->fetch_assoc();
?>
<h2>Profilo di <?=$_SESSION['usr']?></h2>

<form method="POST" action="up_prof.php">
	<table>
		<tr><td>Utente:</td><td><input style="margin-bottom: 5px;" class='form-control' type="text" name="usr" value="<?=$prof['user']?>"></td></tr>
		<tr><td>Nome:</td><td><input  style="margin-bottom: 5px;" class='form-control' type="text" name="nomp" value="<?=$prof['nomp']?>"></td></tr>
		<tr><td>Cognome:</td><td><input  style="margin-bottom: 5px;" class='form-control' type="text" name="cogp" value="<?=$prof['cogp']?>"></td></tr>
		<tr><td>E-mail:</td><td><input  style="margin-bottom: 5px;" class='form-control' type="text" name="email" value="<?=$prof['email']?>"></td></tr>
		<tr><td>Scuola:</td><td><select class="form-control">
<?php
$ret=query("SELECT * FROM SCUOLE");
while($row=$ret->fetch_assoc())
{
	echo "<option value='".$row['id_scuola']."'";
	if($row['id_scuola']==$prof['fk_scuola'])
		echo " selected";
	echo ">".$row['nomescuola']."</option>";
}
?> 
		</select></td></tr>
	</table> 

	<button  style="margin-bottom: 5px;" type="button" id="btnpass" class="btn btn-warning">Modifica password</button><br>
	<span id="pass">
    	<table>
			<tr><td>Nuova password:</td><td><input  style="margin-bottom: 5px;" class='form-control psw' type="password" id="psw" name="psw"></td></tr>
      		<tr><td>Conferma password:</td><td><input  style="margin-bottom: 5px;" class='form-control psw' type="password" id="cpsw"></td></tr>
    	</table>
    	<span id="err" style="color:red;"></span>
    	<br>
  	</span>
  	<input  style="margin-bottom: 5px;" type="submit" id="submit" class="btn btn-warning" id="submit" value="Aggiorna"><br>
</form>
<script>
$(function(){
	$("#pass").hide();
    
	$(".psw").change(function(){
    	if($("#psw").val()==$("#cpsw").val())
    	{
    		$("#submit").removeAttr("disabled");
      		$("#err").text("");
    	}
    	else
    	{
      		$("#submit").attr("disabled",true);
   		   	$("#err").text("Le password inserite non coincidono!");
    	}
	});
  
	$("#btnpass").click(function(){
		if($("#pass").is(":visible"))
    	{
      		$("#pass").hide();
      		$(".psw").removeAttr("required");
      		$(".psw").val("");
      		$("#submit").removeAttr("disabled");
      		$("#err").text("");
      		$("#btnpass").html("Modifica password");
    	}
    	else
    	{
      		$("#pass").show();
      		$(".psw").attr("required",true);
      		$("#btnpass").html("Annulla");
    	}
  	}); 
});
</script>
<?php show_postmain(); ?>