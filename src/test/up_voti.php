<?php 
// Pagina chiamata da test.php per l'aggiornamento dei voti dell'utente prof
include $_SERVER['DOCUMENT_ROOT']."/librerie/general.php";
chk_access(2);
connect();

if($_POST['slp'])
	$prof=$_POST['slp'];
else
	$prof=$_SESSION['id'];

$tot=0;
foreach($_POST['r'] as $i => $perc)
{
	query("UPDATE VALUTAZIONI SET perc=".($perc+$tot)." WHERE fk_voto=(SELECT id_voto FROM VOTI WHERE voto=".($i/10).") AND fk_prof=$prof");
	$tot+=$perc;
}
$_SESSION['alert']="Voti aggiornati correttamente";
writelog("[->voti]");
header("Location: /test/test.php#voti"); 
exit; 
?>