<?php
// Backend page to modify the application's description
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
include $_SERVER['DOCUMENT_ROOT']."/libraries/lib_admin.php";
chk_access(ADMINISTRATOR);
connect();

$compiled = compile_text($_POST['project']);

// If there are syntax errors the text is saved in session
// to be displayed to the user
if(is_array($compiled))
{
    $_SESSION['syntax_error'] = $compiled[1]." alla riga ".$compiled[0];
    $_SESSION['project_text'] = $_POST['project'];
    header("Location: /admin/project_modify.php");
    exit;
}

unset($_SESSION['project_text']);

$up_st = prepare_stmt("UPDATE ADMINDATA SET project_text=?, project_compiled=?");
$up_st->bind_param("ss", $_POST['project'], $compiled);
execute_stmt($up_st);
$up_st->close();

writelog("Modifica descrizione del progetto");
$_SESSION['alert'] = "Descrizione modificata correttamente";
header("Location: /admin/project_modify.php");
?>
