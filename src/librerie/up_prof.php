<?php
include $_SERVER['DOCUMENT_ROOT']."/librerie/general.php";
chk_access();
connect();

if(!empty($_POST['psw']))
{
	$psw=",psw='".md5($_POST['psw'])."',lastpsw=CURDATE()";
  	$_SESSION['scad']=false;
}

$ret=query("UPDATE PROFESSORI SET user='".$_POST['usr']."',nomp='".$_POST['nomp']."',cogp='".$_POST['cogp']."',email='".$_POST['email']."' WHERE id_prof='".$_SESSION['id']."'");

if($_SESSION['sql']->errno==1062)
{
	$_SESSION['alert']="Username già in uso!";
	header("Location: /librerie/profilo.php");
	exit;
}
$_SESSION['usr']=stripslashes($_POST['usr']);  
           
writelog("[->usr] ".$_SESSION['id']);
$_SESSION['alert']="Aggiornamento avvenuto con successo";

header("Location: /librerie/profilo.php");
?>