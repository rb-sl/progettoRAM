<?php 
include $_SERVER['DOCUMENT_ROOT']."/librerie/general.php";
chk_access(2);
connect();
show_premain("Registro");

$ret=query("SELECT nomp,cogp FROM PROFESSORI WHERE id_prof=".$_SESSION['id']);
$prof=$ret->fetch_assoc();

?>
<h2>Registro <?=$prof['cogp'] ? "di ".$prof['nomp']." ".$prof['cogp'] : "" ?></h2>
<div>
	<a href="/registro/add_classe.php" class="btn btn-primary btnmenu">Aggiungi classe</a>

<?php
if($_SESSION['priv']!=0)
  	$p=" WHERE fk_prof=".$_SESSION['id'];
else
{
	$p=",PROFESSORI,SCUOLE WHERE fk_prof=id_prof AND CLASSI.fk_scuola=id_scuola";
  	$sp="\n";
}

$ret=query("SELECT * FROM CLASSI$p ORDER BY anno DESC,classe,sez");

$anno=-1;
$classe=0;
while($row=$ret->fetch_assoc())
{
	if($row['anno']!=$anno)
    {
    	echo "</div>\n<h3>".$row['anno']."/".($row['anno']+1)."</h3>\n<div>";
    	$classe=$row['classe'];
    }
	else if($classe!=$row['classe'])
    {
    	echo "</div><br><div>";
    	$classe=$row['classe'];
    }
	echo "<a href='/registro/show_classe.php?id=".$row['id_cl']."' class='btn btn-warning btncl' title='".$row['cogp'].$sp.$row['nomescuola'].$sp.$row['citta']."'>".$row['classe'].$row['sez']."</a> ";
	$classe=$row['classe'];
	$anno=$row['anno'];
}

show_postmain();
?>