<?php
// Pagina per l'aggiornamento dei test nel sistema (solo amministartore)
include $_SERVER['DOCUMENT_ROOT']."/librerie/general.php";
chk_access(1);
connect();

// Controllo nome test -> bloccato dal sistema se uguale a già presente
$nome=query("SELECT COUNT(*) AS n FROM TEST WHERE nometest='".$_POST['nometest']."'");
$n=$nome->fetch_assoc();
if($n['n']!=0)
{
	$_SESSION['alert']=">>>Errore:\\nIl test ".$_POST[nometest]." è già presente nel sistema";
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

$ret=query("INSERT INTO TEST (nometest,fk_cltest,fk_udm,pos,fk_tipot,posiz,equip,esec,cons,limite,valut) VALUES 
(".$_POST['nometest'].",
".$_POST['classe'].",
".$_POST['unita'].",
".$_POST['pos'].",
".$_POST['tipo'].",
".$_POST['posiz'].",
".$_POST['equip'].",
".$_POST['esec'].",
".$_POST['cons'].",
".$_POST['limite'].",
".$_POST['valut'].")");

writelog("[+test] ".$_SESSION['sql']->insert_id." ".$_POST['nometest']);

header("Location: /test/show_test.php?id=".$_SESSION['sql']->insert_id);
exit;
?>