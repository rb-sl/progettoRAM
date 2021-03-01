<?php 
// Pagina chiamata da test.php per l'aggiornamento dei voti dell'utente prof
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
chk_access(2);
connect();

if($_SESSION['priv'] == 0 and isset($_POST['slp']))
	$prof = $_POST['slp'];
else
	$prof = $_SESSION['id'];

$up_st = prepare_stmt("UPDATE VALUTAZIONI SET perc=? WHERE fk_voto=? AND fk_prof=?");
$up_st->bind_param("iii", $newperc, $id, $prof);

$tot = 0;
foreach($_POST['perc'] as $id => $perc)
{
	$newperc = $perc + $tot;
	execute_stmt($up_st);
	$tot += $perc;
}

$_SESSION['alert'] = "Voti aggiornati correttamente";
writelog("Voti di $prof modificati");

header("Location: /test/test.php#voti"); 
exit; 
?>