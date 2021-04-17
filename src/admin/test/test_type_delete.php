<?php
// Script to delete a test type
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
chk_access(ADMINISTRATOR);
connect();

$chk_st = prepare_stmt("SELECT COUNT(*) AS n FROM TEST WHERE fk_tipot=?");
$chk_st->bind_param("i", $_GET['id']);
$ret = execute_stmt($chk_st);
$chk_st->close();

$r = $ret->fetch_assoc();

// If some tests are present for the type the deletion is blocked
if($r['n'] === 0)
{
    $del_st = prepare_stmt("DELETE FROM TIPOTEST WHERE id_tipot=?");
    $del_st->bind_param("i", $_GET['id']);
    execute_stmt($del_st);
    $del_st->close();

    writelog("Tipo test ".$_GET['id']." cancellato");

    $_SESSION['alert'] = "Tipo di test eliminato correttamente";
}
else
{
    writelog("Tentativo cancellazione tipo test ".$_GET['id']." bloccato; esistono ".$r['n']." test associati");

    $_SESSION['alert'] = "Impossibile eliminare il tipo di test: esistono ".$r['n']." test associati";
}

header("Location: /admin/test/test_type.php");
exit;
?>
