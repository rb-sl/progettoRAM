<?php
// Backend script to update a user's permissions
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
include $_SERVER['DOCUMENT_ROOT']."/libraries/lib_admin.php";
chk_access(0);
connect();

$chk_st = prepare_stmt("SELECT priv FROM PROFESSORI WHERE id_prof=?");
$chk_st->bind_param("i", $_GET['id']);
$ret = execute_stmt($chk_st);
$chk_st->close();

$user = $ret->fetch_assoc();

// Blocks updates that would remove admin privileges to an admin higher in
// the hierarchy wrt the current user
if(!can_downgrade($_GET['id']) and $_POST['priv'] < $user['priv'])
{
    $_SESSION['alert'] = "Modifica dei privilegi dell'utente non autorizzata";
    header("Location: /admin/users.php");
}

// If the privilege is not modified the updated is not carried out (in order to
// not modify the granter)
if($_POST['priv'] != $user['priv'])
{
    $up_st = prepare_stmt("UPDATE PROFESSORI SET priv=?, granted_by=? WHERE id_prof=?");
    $up_st->bind_param("iii", $_POST['priv'], $_SESSION['id'], $_GET['id']);
    execute_stmt($up_st);
    $up_st->close();
}

$_SESSION['alert'] = "Privilegi aggiornati correttamente";
header("Location: /admin/users.php");
?>
