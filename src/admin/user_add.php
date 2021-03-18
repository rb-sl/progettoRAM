<?php
// Front end form to add a new user
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
chk_access(0);
connect();
show_premain();
?>

<h2>Aggiungi utente</h2>

<form method="POST" action="user_insert.php">
	<table>
		<tr>
			<td>Nome utente:</td>
			<td><input type="text" class="form-control marginunder" name="usr" required></td>
		</tr>
    	<tr>
			<td>Password default:</td>
			<td><input type="text" class="form-control marginunder" name="psw" required></td>
		</tr>
    	<tr>
			<td>Nome:</td>
			<td><input type="text" class="form-control marginunder" name="nom" required></td>
		</tr>
    	<tr>
			<td>Cognome:</td>
			<td><input type="text" class="form-control marginunder" name="cog" required></td>
		</tr>
    	<tr>
			<td>E-mail:</td>
			<td><input type="mail" class="form-control marginunder" name="mail" required></td>
		</tr>
    	<tr>
			<td>Scuola:</td>
			<td>
				<select name="sc" class="form-control marginunder" required>
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
				<select name="priv" class="form-control marginunder" required>
					<option selected disabled></option>
					<option value="0">Amministratore</option>
					<option value="1">Professore (modifica test)</option>
					<option value="2">Professore</option>
					<option value="3">Ricerca</option>
				</select>
			</td>			
  	</table>
 	<input type="submit" class="btn btn-warning" value="Aggiungi utente">
</form>

<?php show_postmain(); ?>
