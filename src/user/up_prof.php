<?php
// Script to update a user's profile
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
chk_access();
connect();

if(!empty($_POST['psw']))
{
	$up_st = prepare_stmt("UPDATE PROFESSORI SET user=?, nomp=?, cogp=?, email=?, fk_scuola=?, psw=MD5(?), lastpsw=CURDATE() WHERE id_prof=?");
	$up_st->bind_param("ssssssi", $_POST['usr'], $_POST['nomp'], $_POST['cogp'], $_POST['email'], $_POST['school'], $_POST['psw'], $_SESSION['id']);
  	$_SESSION['scad'] = false;
}
else
{
	$up_st = prepare_stmt("UPDATE PROFESSORI SET user=?, nomp=?, cogp=?, email=?, fk_scuola=? WHERE id_prof=?");
	$up_st->bind_param("sssssi", $_POST['usr'], $_POST['nomp'], $_POST['cogp'], $_POST['email'], $_POST['school'], $_SESSION['id']);
}
$ret=execute_stmt($up_st);

if($_SESSION['sql']->errno == 1062)
{
	$_SESSION['alert'] = "Username già in uso!";
	header("Location: /librerie/profilo.php");
	exit;
}
$_SESSION['user'] = $_POST['usr'];  
           
writelog("[Modifica profilo] ".$_SESSION['id']);
$_SESSION['alert'] = "Aggiornamento avvenuto con successo";

header("Location: /user/profile.php");
?>