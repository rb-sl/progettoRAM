<?php
// Pagina per la cancellazione di un test (solo amministratore/1)
include $_SERVER['DOCUMENT_ROOT']."/librerie/general.php";
chk_access(1);
connect();

query("DELETE FROM TEST WHERE id_test=".$_GET['id']);

writelog("[-test] ".$_GET['id']);

$_SESSION['alert']="Test eliminato correttamente";
header("Location: /test/test.php");
exit;
?>