<?php
// Script to log in a user
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
connect();
$ref=explode("?", $_SERVER['HTTP_REFERER']);

$prof_st = prepare_stmt("SELECT * FROM PROFESSORI WHERE BINARY user=? AND psw=MD5(?)");
$prof_st->bind_param("ss", $_POST['usr'], $_POST['psw']);

$ret = execute_stmt($prof_st);
if($ret->num_rows != 0)
{
	$row = $ret->fetch_assoc();
	$_SESSION['user'] = $row['user'];
	$_SESSION['id'] = $row['id_prof'];
	$_SESSION['priv'] = $row['priv'];
	$_SESSION['scuola'] = $row['fk_scuola'];

	$up_st = prepare_stmt("UPDATE PROFESSORI SET last_login=NOW() WHERE id_prof=?");
	$up_st->bind_param("i", $row['id_prof']);
	execute_stmt($up_st);
	
	writelog("Accesso");
	
	// Redirects based on the user's staus
	if($row['priv'] > 2)
		header('Location: /');
	else
		header('Location: /registro/registro.php');
  	exit;
}
// If no user is found an error is shown
$_SESSION['err'] = 2;
header('Location: /');
exit;
?>