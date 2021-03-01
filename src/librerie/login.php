<?php
include $_SERVER['DOCUMENT_ROOT']."/librerie/general.php";
connect();
$ref=explode("?",$_SERVER['HTTP_REFERER']);

$ret=query("SELECT * FROM PROFESSORI WHERE BINARY user='".$_POST['usr']."' AND psw='".md5($_POST['psw'])."'");
if($ret->num_rows!=0)
{
	$row=$ret->fetch_assoc();
	$_SESSION['usr']=$row['user'];
	$_SESSION['id']=$row['id_prof'];
	$_SESSION['priv']=$row['priv'];
	$_SESSION['scuola']=$row['fk_scuola'];
	
	writelog("Accesso");
	
	if($row['priv']>2)
		header('Location: /');
	else
		header('Location: /registro/registro.php');
  	exit;
}
$_SESSION['err']=2;
header('Location: /');
exit;
?>