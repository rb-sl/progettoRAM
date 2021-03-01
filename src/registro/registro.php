<?php 
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
chk_access(2);
connect();
show_premain("Registro");

$prof_st = prepare_stmt("SELECT nomp, cogp FROM PROFESSORI WHERE id_prof=?");
$prof_st->bind_param("i", $_SESSION['id']);

$ret = execute_stmt($prof_st);
$prof = $ret->fetch_assoc();
$prof_st->close();
?>

<h2>Registro <?=$prof['cogp'] ? "di ".$prof['nomp']." ".$prof['cogp'] : "" ?></h2>

<div>
	<a href="/registro/add_classe.php" class="btn btn-primary btnmenu">Aggiungi classe</a>

<?php
if($_SESSION['priv'] != 0)
{
	$class_st = prepare_stmt("SELECT * FROM CLASSI WHERE fk_prof=? ORDER BY anno DESC, classe, sez");
	$class_st->bind_param("i", $_SESSION['id']);
}
else
{
	$class_st = prepare_stmt("SELECT * FROM CLASSI JOIN PROFESSORI ON fk_prof=id_prof
		JOIN SCUOLE ON CLASSI.fk_scuola=id_scuola
		ORDER BY anno DESC, classe, sez");
  	$sp="\n";
}

$ret = execute_stmt($class_st);
$class_st->close();

$anno = -1;
$classe = 0;
while($row = $ret->fetch_assoc())
{
	if($row['anno'] != $anno)
    {
    	echo "</div>
			<h3>".$row['anno']."/".($row['anno'] + 1)."</h3>
			<div>";
		$anno=$row['anno'];
    }
	else if($classe != $row['classe'])
    	echo "</div><br><div>";

	echo "<a href='/registro/show_classe.php?id=".$row['id_cl']."' class='btn btn-warning btncl' 
		title='".$row['cogp'].$sp.$row['nomescuola'].$sp.$row['citta']."'>".$row['classe'].$row['sez']."</a> ";
	
	$classe = $row['classe'];
}

show_postmain();
?>