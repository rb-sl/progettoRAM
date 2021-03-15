<?php 
// Form page to change a student's information
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
include $_SERVER['DOCUMENT_ROOT']."/libraries/lib_reg.php";
include $_SERVER['DOCUMENT_ROOT']."/libraries/lib_stat.php";
chk_access(2);
connect();
show_premain("Modifica Studente");

if($_SESSION['priv'] > 0)
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
	<table class="table table-striped studtable marginunder">
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
			<td>
				<label><input type="radio" name="sesso" value="m" <?=$m?> required>M</label>
				<label><input type="radio" name="sesso" value="f" <?=$f?> required>F</label>
			</td>
		</tr>
	</table>

	<input type="submit" class="btn btn-warning btnmenu" value="Aggiorna dati studente">
</form>

<?php show_postmain(); ?>