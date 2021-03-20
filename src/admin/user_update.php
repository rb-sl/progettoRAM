<?php
// Backend script to update a user's permissions
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
chk_access(0);
connect();

$up_st = prepare_stmt("UPDATE PROFESSORI SET priv=? WHERE id_prof=?");
$up_st->bind_param("ii", $_POST['priv'], $_GET['id']);
execute_stmt($up_st);
$up_st->close();

$_SESSION['alert'] = "Privilegi aggiornati correttamente";
header("Location: /admin/users.php");
?>
