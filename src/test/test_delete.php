<?php
// Script to delete a test
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
chk_access(1);
connect();

$del_st = prepare_stmt("DELETE FROM TEST WHERE id_test=?");
$del_st->bind_param("i", $_GET['id']);
execute_stmt($del_st);
$del_st->close();

writelog("Test ".$_GET['id']." cancellato");

$_SESSION['alert'] = "Test eliminato correttamente";
header("Location: /test/test.php");
exit;
?>