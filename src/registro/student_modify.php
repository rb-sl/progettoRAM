<?php 
include $_SERVER['DOCUMENT_ROOT']."/librerie/general.php";
include $_SERVER['DOCUMENT_ROOT']."/librerie/lib_reg.php";
include $_SERVER['DOCUMENT_ROOT']."/librerie/lib_stat.php";
chk_access(2);
connect();
show_premain("Modifica Studente");

if($_SESSION['priv']>0)
	$prof=" AND fk_prof=".$_SESSION['id'];

$ret=query("SELECT * FROM STUDENTI,ISTANZE,CLASSI WHERE fk_cl=id_cl AND fk_stud=id_stud AND id_stud=".$_GET['id'].$prof);
$stud=$ret->fetch_assoc();

if(!$stud)
{
	$_SESSION['alert']="Permessi insufficienti per visualizzare le informazioni";
	header("Location: /registro/registro.php");
	exit;
}

if($stud['sesso']=="m")
	$m="checked";
else
	$f="checked";
?>
<h2>Modifica studente <a href="./show_stud.php?id=<?=$_GET['id']?>" class="btn btn-primary btnmenu">Fine</a></h2>
<form action="up_stud.php?id=<?=$_GET['id']?>" method="POST">
	<table class="table table-striped" style="max-width:500px">
		<tr><th>Cognome:</th><td><input type="text" name="cogs" value="<?=$stud['cogs']?>" required></td></tr>
		<tr><th>Nome:</th><td><input type="text" name="noms" value="<?=$stud['noms']?>" required></td></tr> 
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