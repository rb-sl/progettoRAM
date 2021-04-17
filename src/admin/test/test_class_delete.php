<?php
// Script to delete a test class
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
chk_access(ADMINISTRATOR);
connect();

$chk_st = prepare_stmt("SELECT COUNT(*) AS n FROM TEST WHERE fk_cltest=?");
$chk_st->bind_param("i", $_GET['id']);
$ret = execute_stmt($chk_st);
$chk_st->close();

$r = $ret->fetch_assoc();

// If some tests are present for the type the deletion is blocked
if($r['n'] === 0)
{
    $del_st = prepare_stmt("DELETE FROM CLTEST WHERE id_cltest=?");
    $del_st->bind_param("i", $_GET['id']);
    execute_stmt($del_st);
    $del_st->close();

    writelog("Classe test ".$_GET['id']." cancellata");

    $_SESSION['alert'] = "Classe di test eliminata correttamente";
}
else
{
    writelog("Tentativo cancellazione classe test ".$_GET['id']." bloccato; esistono ".$r['n']." test associati");

    $_SESSION['alert'] = "Impossibile eliminare la classe di test: esistono ".$r['n']." test associati";
}

header("Location: /admin/test/test_class.php");
exit;
?>
