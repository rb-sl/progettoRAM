<?php
// Pagina per l'aggiornamento dei test nel sistema (solo amministartore)
include $_SERVER['DOCUMENT_ROOT']."/librerie/general.php";
chk_access(1);
connect();

// Controllo nome test -> bloccato dal sistema se uguale a già presente
$nome=query("SELECT id_test FROM TEST WHERE nometest='".$_POST['nometest']."'");
$n=$nome->fetch_assoc();
if($nome->num_rows==1 and $n['id_test']!=$_GET['id'])
{
	$_SESSION['alert']=">>>Errore:\\nIl test ".$_POST['nometest']." è già presente nel sistema";
	header("Location: ".$_SERVER['HTTP_REFERER']);
	exit;
}

// Modifica del post per permettere l'inserimento di valori NULL
foreach($_POST as $k => $v)
{
	if(!$v)
    	$_POST[$k]="NULL";
	else
    	$_POST[$k]="'".$_POST[$k]."'";
}

query("UPDATE TEST SET nometest=".$_POST['nometest'].",
fk_cltest=".$_POST['classe'].",
fk_udm=".$_POST['unita'].",
pos=".$_POST['pos'].",
fk_tipot=".$_POST['tipo'].",
posiz=".$_POST['posiz'].",
equip=".$_POST['equip'].",
esec=".$_POST['esec'].",
cons=".$_POST['cons'].",
limite=".$_POST['limite'].",
valut=".$_POST['valut']."
WHERE id_test=".$_GET['id']);

writelog("[->test] ".$_GET['id']."->".$_POST['nometest']);

header("Location: /test/show_test.php?id=".$_GET['id']);
exit;
?>