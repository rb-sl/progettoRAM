<?php 
include $_SERVER['DOCUMENT_ROOT']."/librerie/general.php";
chk_access(2);
connect();

query("DELETE FROM CLASSI WHERE id_cl=".$_GET[id]);
query("DELETE FROM STUDENTI WHERE id_stud NOT IN (SELECT DISTINCT(fk_stud) FROM ISTANZE)");

writelog("[-cl] ".$_GET['id']);
$_SESSION['alert']="Classe eliminata correttamente";
header("Location: /registro/registro.php");
?>