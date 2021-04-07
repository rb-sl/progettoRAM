<?php
// Script to delete a test
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
chk_access(PROFESSOR_GRANTS);
connect();

$chk_st = prepare_stmt("SELECT COUNT(*) AS n FROM PROVE WHERE fk_test=?");
$chk_st->bind_param("i", $_GET['id']);
$ret = execute_stmt($chk_st);
$chk_st->close();

$r = $ret->fetch_assoc();

// If some results are present the deletion is blocked
if($r['n'] === 0)
{
    $del_st = prepare_stmt("DELETE FROM TEST WHERE id_test=?");
    $del_st->bind_param("i", $_GET['id']);
    execute_stmt($del_st);
    $del_st->close();

    writelog("Test ".$_GET['id']." cancellato");

    $_SESSION['alert'] = "Test eliminato correttamente";
    header("Location: /test/test.php");
}
else
{
    writelog("Tentativo cancellazione test ".$_GET['id']." bloccato; esistono ".$r['n']." prove");

    $_SESSION['alert'] = "Impossibile eliminare il test: esistono ".$r['n']." prove associate";
    header("Location: /test/test_modify.php?id=".$_GET['id']);
}

exit;
?>
