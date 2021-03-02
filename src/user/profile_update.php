<?php
// Script to update a user's profile
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
chk_access();
connect();

// Check on username uniqueness; if the username is already taken,
// the update fails and the user is asked to repeat the procedure
$chk_st = prepare_stmt("SELECT * FROM PROFESSORI WHERE user=? AND id_prof<>?");
$chk_st->bind_param("si", $_POST['usr'], $_SESSION['id']);
$chk = execute_stmt($chk_st);
$chk_st->close();

if($chk->num_rows > 0)
{
	$_SESSION['alert'] = "Username già in uso! Modifiche non effettuate";
	header("Location: /user/profile.php");
	exit;
}

// The query is built considering if the user wishes to update their password
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
$ret = execute_stmt($up_st);
$up_st->close();

// Update of the active username
$_SESSION['user'] = $_POST['usr'];  
           
writelog("[Modifica profilo] ".$_SESSION['id']);
$_SESSION['alert'] = "Aggiornamento avvenuto con successo";

header("Location: /user/profile.php");
?>