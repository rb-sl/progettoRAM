<?php 
// Backend script to delete a class
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
chk_access(2);
connect();

$del_st = prepare_stmt("DELETE FROM CLASSI WHERE id_cl=?");
$del_st->bind_param("i", $_GET['id']);
execute_stmt($del_st);
$del_st->close();

// The trigger to delete students without instances does not activate
// for cascade deletions, so this query is executed
$stud_st = prepare_stmt("DELETE FROM STUDENTI WHERE id_stud NOT IN (SELECT DISTINCT(fk_stud) FROM ISTANZE)");
execute_stmt($stud_st);
$stud_st->close();

writelog("[Classe eliminata] ".$_GET['id']);
$_SESSION['alert'] = "Classe eliminata correttamente";
header("Location: /register/register.php");
?>