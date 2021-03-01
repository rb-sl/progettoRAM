<?php 
include $_SERVER['DOCUMENT_ROOT']."/librerie/general.php";
include $_SERVER['DOCUMENT_ROOT']."/librerie/lib_reg.php";
include $_SERVER['DOCUMENT_ROOT']."/librerie/lib_stat.php";
chk_access(2);
connect();

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

query("UPDATE STUDENTI SET cogs='".maiuscolo($_POST['cogs'])."',noms='".maiuscolo($_POST['noms'])."',sesso='".$_POST['sesso']."' WHERE id_stud=".$_GET['id']);
writelog("[up_stud] ".$_GET['id']);
$_SESSION['alert']="Aggiornamento effettuato";

header("Location: ".$_SERVER['HTTP_REFERER']);
?>