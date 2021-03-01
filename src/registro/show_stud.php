<?php 
include $_SERVER['DOCUMENT_ROOT']."/librerie/general.php";
include $_SERVER['DOCUMENT_ROOT']."/librerie/lib_reg.php";
include $_SERVER['DOCUMENT_ROOT']."/librerie/lib_stat.php";
chk_access(2);
connect();

$ret=query("SELECT * FROM STUDENTI WHERE id_stud=".$_GET['id']);
$stud=$ret->fetch_assoc();

$ret=query("SELECT id_cl,classe,sez,anno,fk_prof,COUNT(id_prova) AS n 
	FROM PROVE RIGHT JOIN ISTANZE ON fk_ist=id_ist,CLASSI 
	WHERE fk_stud=".$stud['id_stud']." AND fk_cl=id_cl 
	GROUP BY id_cl ORDER BY anno");
$tot=0;
$f=0;
while($row=$ret->fetch_assoc())
{
	if($row['fk_prof']==$_SESSION['id'] or $_SESSION['id']==0)
	{
		$slnk="<a href='./show_classe.php?id=".$row['id_cl']."'>";
		$elnk="</a>";
		$f=1;
	}
	$tcl.="<tr><td>$slnk".$row['classe'].$row['sez']." ".$row['anno']."/".($row['anno']+1)."$elnk</td><td>".$row['n']."</td></tr>";
	$tot+=$row['n'];
}
if(!$f)
{
	$_SESSION['alert']="Permessi insufficienti per visualizzare le informazioni";
	header("Location: /registro/registro.php");
	exit;
}

show_premain("Dati di ".$stud['cogs']." ".$stud['noms']." (".$stud['sesso'].")");
?>
<h2>Dati studente</h2>
<table class="table table-striped" style="max-width:500px">
	<tr>
		<th>Cognome:</td>
		<td><?=$stud['cogs']?></td>
	</tr>
	<tr>
		<th>Nome:</td>
		<td><?=$stud['noms']?></td>
	</tr>
	<tr>
		<th>Sesso:</td>
		<td><?=$stud['sesso']?></td>
	</tr>
</table>
<div>
	<a href="/registro/mod_stud.php?id=<?=$stud['id_stud']?>" class="btn btn-warning btnmenu">Modifica</a>
</div>
<h3>Classi frequentate</h3>
<table class="table table-striped" style="max-width:500px">
	<tr><th>Classe</th><th>Prove effettuate</th></tr>
<?=$tcl?>
	<tr><th>Totale:</th><td><?=$tot?></td></tr>
</table>

<?php show_postmain(); ?>