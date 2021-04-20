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
<?php
if(chk_auth(ADMINISTRATOR))
{
	if($prof['show_email'])
		$chk = " checked";
	else
		$chk = "";
?>
	<div>
		Ulteriori informazioni di contatto:<br>
		<textarea name="contact"><?=$prof['contact_info']?></textarea>
	</div>
	
	<div class="form-check flexrow">
		<input id="showmail" class="form-check-input" type="checkbox" name="showmail" value="1"<?=$chk?>>
		<label class="form-check-label" for="showmail">Mostra email nella guida</label>
	</div>
<?php
}
?>
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

<script src="/user/profile.js"></script>

<?php show_postmain(); ?>
