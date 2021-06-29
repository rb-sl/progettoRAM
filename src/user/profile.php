<?php 
// Copyright 2021 Roberto Basla

// This file is part of progettoRAM.

// progettoRAM is free software: you can redistribute it and/or modify
// it under the terms of the GNU Affero General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

// progettoRAM is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU Affero General Public License for more details.

// You should have received a copy of the GNU Affero General Public License
// along with progettoRAM.  If not, see <http://www.gnu.org/licenses/>.

// Front end page to let users modify their information
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
connect();
chk_access();
show_premain("Profilo");

$prof_st = prepare_stmt("SELECT * FROM user LEFT JOIN school ON school_fk=school_id WHERE user_id=?");
$prof_st->bind_param("i", $_SESSION['id']);

$school_st = prepare_stmt("SELECT * FROM school");

$ret = execute_stmt($prof_st);
$user = $ret->fetch_assoc();
$prof_st->close();
?>
<h2>Profilo di <?=htmlentities($_SESSION['username'])?></h2>

<form method="POST" action="/user/profile_update.php">
	<table class="marginunder">
		<tr>
			<td class="textright">Utente:&nbsp;</td>
			<td><input class="form-control" type="text" name="user" value="<?=htmlentities($user['username'])?>"></td>
		</tr>
		<tr>
			<td class="textright">Nome:&nbsp;</td>
			<td><input class="form-control" type="text" name="firstname" value="<?=htmlentities($user['firstname'])?>"></td>
		</tr>
		<tr>
			<td class="textright">Cognome:&nbsp;</td>
			<td><input class="form-control" type="text" name="lastname" value="<?=htmlentities($user['lastname'])?>"></td>
		</tr>
		<tr>
			<td class="textright">E-mail:&nbsp;</td>
			<td><input class="form-control" type="text" name="email" value="<?=htmlentities($user['email'])?>"></td>
		</tr>
		<tr>
			<td class="textright">Scuola:&nbsp;</td>
			<td>
				<select class="form-control" name="school">
					<option value=""></option>
<?php
// Shows the select with all available schools
$ret = execute_stmt($school_st);
$school_st->close();
while($row = $ret->fetch_assoc())
{
	echo "<option value='".$row['school_id']."'";
	if($row['school_id'] == $user['school_fk'])
		echo " selected";
	echo ">".htmlentities($row['school_name'])."</option>";
}
?> 
				</select>
			</td>
		</tr>
	</table>
<?php
if(chk_auth(ADMINISTRATOR))
{
	if($user['show_email'])
		$chk = " checked";
	else
		$chk = "";
?>
	<div>
		Ulteriori informazioni di contatto:<br>
		<textarea name="contact"><?=$user['contact_info']?></textarea>
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
				<td><input class="form-control password" type="password" id="password" name="password"></td>
			</tr>
	  		<tr>
				<td class="textright">Conferma password:&nbsp;</td>
				<td><input class="form-control password" type="password" id="cpsw"></td>
			</tr>
		</table>
		<span id="err" class="dangercolor"></span>
  	</span>
	
  	<input type="submit" id="submit" class="btn btn-primary" id="submit" value="Aggiorna"><br>
</form>

<script src="/user/profile.js"></script>

<?php show_postmain(); ?>
