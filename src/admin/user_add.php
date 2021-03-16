<?php
include $_SERVER['DOCUMENT_ROOT']."/librerie/general.php";
chk_access(0);
connect();
show_premain();
?>
<h2>Aggiungi utente</h2>

<form method="POST" action="in_prof.php">
	<table>
		<tr><td>Nome utente:</td><td><input type="text" name="usr" required></td></tr>
    	<tr><td>Password default:</td><td><input type="text" name="pwd" required></td></tr>
    	<tr><td>Nome:</td><td><input type="text" name="nom" required></td></tr>
    	<tr><td>Cognome:</td><td><input type="text" name="cog" required></td></tr>
    	<tr><td>E-mail:</td><td><input type="mail" name="mail" required></td></tr>
    	<tr><td>Scuola:</td><td>
      		<select name="sc" required>
        		<option></option>
<?php
$ret=query("SELECT * FROM SCUOLE ORDER BY nomescuola");
while($row=$ret->fetch_assoc())
	echo "<option value='$row[idscuola]'>$row[nomescuola]</option>";
?>
      		</select>
      	</td></tr>
  	</table>
 	<input type="submit" class="btn btn-warning">
</form>

<?php show_postmain(); ?>