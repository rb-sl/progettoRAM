<?php
// Backend script to add a new unit
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
chk_access(ADMINISTRATOR);
connect();

// New unit insert
if(isset($_POST['newrow1']) and $_POST['newrow1'] != "")
{
    $in_st = prepare_stmt("INSERT INTO UNITA(udm, simbolo) 
        VALUES (?, ?)");
    $in_st->bind_param("ss", $_POST['newrow1'], $_POST['newrow2']);
    execute_stmt($in_st);
    $in_st->close();

    writelog("Nuova unità: ".$_POST['newrow1']." [".$_POST['newrow2']."] inserita");
}

// Update of old units 
if(isset($_POST['col1']))
{
    $up_st = prepare_stmt("UPDATE UNITA SET udm=?, simbolo=? 
    WHERE id_udm=?");
    $up_st->bind_param("ssi", $name, $symbol, $id);

    foreach($_POST['col1'] as $id => $name)
    {
        $symbol = $_POST['col2'][$id];
        execute_stmt($up_st);
    }    
    
    writelog("Aggiornamento unità");
}

$_SESSION['alert'] = "Aggiornamento completato";
header("Location: /admin/test/unit.php");
?>
