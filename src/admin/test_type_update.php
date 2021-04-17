<?php
// Backend script to add a new test type
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
chk_access(ADMINISTRATOR);
connect();

// New type insert
if(isset($_POST['newrow1']) and $_POST['newrow1'] != "")
{
    $in_st = prepare_stmt("INSERT INTO TIPOTEST(nomet, passo) 
        VALUES (?, ?)");
    $in_st->bind_param("ss", $_POST['newrow1'], $_POST['newrow2']);
    execute_stmt($in_st);
    $in_st->close();

    writelog("Nuovo tipo di test: ".$_POST['newrow1']." passo ".$_POST['newrow2']." inserito");
}

// Update of old test types 
if(isset($_POST['col1']))
{
    $up_st = prepare_stmt("UPDATE TIPOTEST SET nomet=?, passo=? 
    WHERE id_tipot=?");
    $up_st->bind_param("ssi", $name, $step, $id);

    foreach($_POST['col1'] as $id => $name)
    {
        $step = $_POST['col2'][$id];
        execute_stmt($up_st);
    }    
    
    writelog("Aggiornamento tipi test");
}

$_SESSION['alert'] = "Aggiornamento completato";
header("Location: /admin/test_type.php");
?>
