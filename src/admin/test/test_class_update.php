<?php
// Backend script to add a new test type
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
chk_access(ADMINISTRATOR);
connect();

// New class insert
if(isset($_POST['newrow1']) and $_POST['newrow1'] != "")
{
    $in_st = prepare_stmt("INSERT INTO CLTEST(nomec) 
        VALUES (?)");
    $in_st->bind_param("s", $_POST['newrow1']);
    execute_stmt($in_st);
    $in_st->close();

    writelog("Nuova classe di test: ".$_POST['newrow1']." inserita");
}

// Update of old test classes 
if(isset($_POST['col1']))
{
    $up_st = prepare_stmt("UPDATE CLTEST SET nomec=? WHERE id_cltest=?");
    $up_st->bind_param("si", $name, $id);

    foreach($_POST['col1'] as $id => $name)
        execute_stmt($up_st);
    
    writelog("Aggiornamento classi test");
}

$_SESSION['alert'] = "Aggiornamento completato";
header("Location: /admin/test/test_class.php");
?>
