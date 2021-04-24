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

// Front end form to add a new user
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
include $_SERVER['DOCUMENT_ROOT']."/libraries/lib_admin.php";
chk_access(ADMINISTRATOR);
connect();
show_premain();
?>

<h2>Aggiungi utente</h2>

<form method="POST" action="user_insert.php">
	<table class="marginunder">
		<tr>
			<td>Nome utente:</td>
			<td><input type="text" class="form-control" name="user" required></td>
		</tr>
		<tr>
			<td>Password default:</td>
			<td><input type="text" class="form-control" name="password" required></td>
		</tr>
		<tr>
			<td>Nome:</td>
			<td><input type="text" class="form-control" name="firstname" required></td>
		</tr>
		<tr>
			<td>Cognome:</td>
			<td><input type="text" class="form-control" name="lastname" required></td>
		</tr>
		<tr>
			<td>E-mail:</td>
			<td><input type="mail" class="form-control" name="mail" required></td>
		</tr>
		<tr>
			<td>Scuola:</td>
			<td>
				<select id="school" name="school" class="form-control">
					<option></option>
<?php
$school_st = prepare_stmt("SELECT * FROM school ORDER BY school_name");
$ret = execute_stmt($school_st);
$school_st->close();
while($row = $ret->fetch_assoc())
	echo "<option value='".$row['school_id']."'>".$row['school_name']."</option>";
?>
	  			</select>
	  		</td>
		</tr>
		<tr>
			<td>Privilegi di accesso</td>
			<td>
				<select id="privileges" name="privileges" class="form-control" required>
					<option selected disabled></option>
					<option value="<?=ADMINISTRATOR?>">Amministratore</option>
					<option value="<?=PROFESSOR_GRANTS?>">Professore (modifica test)</option>
					<option value="<?=PROFESSOR?>">Professore</option>
					<option value="<?=RESEARCH?>">Ricerca</option>
					<option value="<?=NONE?>">Nessuno</option>
				</select>
			</td>			
  	</table>
 	<input type="submit" class="btn btn-warning" value="Aggiungi utente">
</form>

<script>
$("#privileges").change(function() {
	if($(this).val() <= <?=PROFESSOR?>) {
		$("#school").attr("required", true);
	}
	else {
		$("#school").removeAttr("required");
	}
});
</script>

<?php show_postmain(); ?>
