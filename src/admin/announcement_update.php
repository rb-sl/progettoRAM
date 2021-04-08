<?php
// Backend page to modify the index's announcement
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
include $_SERVER['DOCUMENT_ROOT']."/libraries/lib_admin.php";
chk_access(ADMINISTRATOR);
connect();

$compiled = compile_text($_POST['announcement']);

// If there are syntax errors the text is saved in session
// to be displayed to the user
if(is_array($compiled))
{
    $_SESSION['syntax_error'] = $compiled[1]." alla riga ".$compiled[0];
    $_SESSION['index_text'] = $_POST['announcement'];
    $_SESSION['important'] = isset($_POST['important']);
    header("Location: /admin/announcement_modify.php");
    exit;
}

unset($_SESSION['index_text']);
unset($_SESSION['important']);

if($compiled != "")
    if(isset($_POST['important']))
        $compiled = "<h2 class='dangercolor'>>> Attenzione <<\n$compiled\n</h2>";
    else
        $compiled = "<h3 class='primarycolor'>$compiled</h3>";
else
    $compiled = null;

$up_st = prepare_stmt("UPDATE ADMINDATA SET index_text=?, index_compiled=?");
$up_st->bind_param("ss", $_POST['announcement'], $compiled);
execute_stmt($up_st);
$up_st->close();

writelog("Modifica messaggio in home");
$_SESSION['alert'] = "Annuncio modificato correttamente";
header("Location: /admin/announcement_modify.php");
?>
