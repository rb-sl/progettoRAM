<?php 
// Form page to change a student's information
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
chk_access(PROFESSOR);
connect();
show_premain("Modifica Studente");

if(!chk_auth(ADMINISTRATOR))
{
	$stud_st = prepare_stmt("SELECT * FROM STUDENTI 
		JOIN ISTANZE ON fk_stud=id_stud
		JOIN CLASSI ON fk_cl=id_cl 
		WHERE id_stud=?
		AND fk_prof=?");
	$stud_st->bind_param("ii", $_GET['id'], $_SESSION['id']);
}
else
{
	$stud_st = prepare_stmt("SELECT * FROM STUDENTI 
		JOIN ISTANZE ON fk_stud=id_stud
		JOIN CLASSI ON fk_cl=id_cl 
		WHERE id_stud=?");
	$stud_st->bind_param("i", $_GET['id']);
}

$ret = execute_stmt($stud_st);
$stud_st->close();

$stud = $ret->fetch_assoc();

if(!$stud)
{
	$_SESSION['alert']="Permessi insufficienti per visualizzare le informazioni";
	header("Location: /register/register.php");
	exit;
}

if($stud['sesso']=="m")
{
	$m = "checked";
	$f = "";
}
else
{
	$m = "";
	$f = "checked";
}
?>

<h2>Modifica studente <a href="./student_show.php?id=<?=$_GET['id']?>" class="btn btn-warning">Indietro</a></h2>

<form action="student_update.php?id=<?=$_GET['id']?>" method="POST">
	<table class="table table-light table-striped studtable marginunder">
		<tr>
			<th>Cognome:</th>
			<td><input type="text" name="cogs" value="<?=$stud['cogs']?>" required></td>
		</tr>
		<tr>
			<th>Nome:</th>
			<td><input type="text" name="noms" value="<?=$stud['noms']?>" required></td>
		</tr> 
		<tr>
			<th>Sesso:</th>
			<td class="containerflex">
				<div class="form-check">
					<input type="radio" id="radiom" class="form-check-input" name="sesso" value="m" <?=$m?> required>
					<label class="form-check-label" for="radiom">M</label>
				</div>
				<div class="form-check">
					<input type="radio" id="radiof" class="form-check-input" name="sesso" value="f" <?=$f?> required>
					<label class="form-check-label" for="radiof">F</label>
				</div>
			</td>
		</tr>
	</table>

	<input type="submit" class="btn btn-warning" value="Aggiorna dati studente">
</form>

<?php show_postmain(); ?>
