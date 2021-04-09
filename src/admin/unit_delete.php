<?php
// Script to delete a unit
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
chk_access(ADMINISTRATOR);
connect();

$chk_st = prepare_stmt("SELECT COUNT(*) AS n FROM TEST WHERE fk_udm=?");
$chk_st->bind_param("i", $_GET['id']);
$ret = execute_stmt($chk_st);
$chk_st->close();

$r = $ret->fetch_assoc();

// If some tests are present for the unit the deletion is blocked
if($r['n'] === 0)
{
    $del_st = prepare_stmt("DELETE FROM UNITA WHERE id_udm=?");
    $del_st->bind_param("i", $_GET['id']);
    execute_stmt($del_st);
    $del_st->close();

    writelog("Unità ".$_GET['id']." cancellata");

    $_SESSION['alert'] = "Unità di misura eliminata correttamente";
}
else
{
    writelog("Tentativo cancellazione unità ".$_GET['id']." bloccato; esistono ".$r['n']." test associati");

    $_SESSION['alert'] = "Impossibile eliminare l'unità: esistono ".$r['n']." test associati";
}

header("Location: /admin/unit.php");
exit;
?>
