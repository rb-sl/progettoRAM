<?php
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
			<td><input type="text" class="form-control" name="usr" required></td>
		</tr>
    	<tr>
			<td>Password default:</td>
			<td><input type="text" class="form-control" name="psw" required></td>
		</tr>
    	<tr>
			<td>Nome:</td>
			<td><input type="text" class="form-control" name="nom" required></td>
		</tr>
    	<tr>
			<td>Cognome:</td>
			<td><input type="text" class="form-control" name="cog" required></td>
		</tr>
    	<tr>
			<td>E-mail:</td>
			<td><input type="mail" class="form-control" name="mail" required></td>
		</tr>
    	<tr>
			<td>Scuola:</td>
			<td>
				<select name="sc" class="form-control" required>
        			<option></option>
<?php
$school_st = prepare_stmt("SELECT * FROM SCUOLE ORDER BY nomescuola");
$ret = execute_stmt($school_st);
$school_st->close();
while($row = $ret->fetch_assoc())
	echo "<option value='".$row['id_scuola']."'>".$row['nomescuola']."</option>";
?>
      			</select>
      		</td>
		</tr>
		<tr>
			<td>Privilegi di accesso</td>
			<td>
				<select name="priv" class="form-control" required>
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

<?php show_postmain(); ?>