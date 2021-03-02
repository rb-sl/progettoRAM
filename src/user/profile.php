<?php 
// Front end page to let users modify their information
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
connect();
chk_access();
show_premain("Profilo");

$prof_st = prepare_stmt("SELECT * FROM PROFESSORI JOIN SCUOLE ON fk_scuola=id_scuola WHERE id_prof=?");
$prof_st->bind_param("i", $_SESSION['id']);

$school_st = prepare_stmt("SELECT * FROM SCUOLE");

$ret = execute_stmt($prof_st);
$prof = $ret->fetch_assoc();
$prof_st->close();
?>
<h2>Profilo di <?=$_SESSION['user']?></h2>

<form method="POST" action="/user/profile_update.php">
	<table class="marginunder">
		<tr>
			<td class="textright">Utente:&nbsp;</td>
			<td><input class="form-control" type="text" name="usr" value="<?=$prof['user']?>"></td>
		</tr>
		<tr>
			<td class="textright">Nome:&nbsp;</td>
			<td><input class="form-control" type="text" name="nomp" value="<?=$prof['nomp']?>"></td>
		</tr>
		<tr>
			<td class="textright">Cognome:&nbsp;</td>
			<td><input class="form-control" type="text" name="cogp" value="<?=$prof['cogp']?>"></td>
		</tr>
		<tr>
			<td class="textright">E-mail:&nbsp;</td>
			<td><input class="form-control" type="text" name="email" value="<?=$prof['email']?>"></td>
		</tr>
		<tr>
			<td class="textright">Scuola:&nbsp;</td>
			<td>
				<select class="form-control" name="school">
<?php
// Shows the select with all available schools
$ret = execute_stmt($school_st);
$school_st->close();
while($row = $ret->fetch_assoc())
{
	echo "<option value='".$row['id_scuola']."'";
	if($row['id_scuola'] == $prof['fk_scuola'])
		echo " selected";
	echo ">".$row['nomescuola']."</option>";
}
?> 
				</select>
			</td>
		</tr>
	</table> 

	<button type="button" id="btnpass" class="btn btn-warning marginunder">Modifica password</button><br>
	<span id="pass" class="jQhidden">
    	<table class="marginunder">
			<tr>
				<td class="textright">Nuova password:&nbsp;</td>
				<td><input class="form-control psw" type="password" id="psw" name="psw"></td>
			</tr>
      		<tr>
				<td class="textright">Conferma password:&nbsp;</td>
				<td><input class="form-control psw" type="password" id="cpsw"></td>
			</tr>
    	</table>
    	<span id="err" class="dangercolor"></span>
  	</span>
	
  	<input type="submit" id="submit" class="btn btn-primary" id="submit" value="Aggiorna"><br>
</form>

<script>
$(function(){    
	// Checks for password equality and shows an error message if needed
	$(".psw").keyup(function(){
    	if($("#psw").val() == $("#cpsw").val())
    	{
    		$("#submit").removeAttr("disabled");
      		$("#err").text("");
    	}
    	else
    	{
      		$("#submit").attr("disabled", true);
   		   	$("#err").html("Le password inserite non coincidono!<br>");
    	}
	});
  
	// Shows or hides the password fields
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
      		$(".psw").attr("required", true);
      		$("#btnpass").html("Annulla modifica password");
    	}
  	}); 
});
</script>

<?php show_postmain(); ?>