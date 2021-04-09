<?php
// Backend script to add a new unit
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
chk_access(ADMINISTRATOR);
connect();

// New unit insert
if(isset($_POST['newunit']) and $_POST['newunit'] != "")
{
    $in_st = prepare_stmt("INSERT INTO UNITA(udm, simbolo) 
        VALUES (?, ?)");
    $in_st->bind_param("ss", $_POST['newunit'], $_POST['symbol']);
    execute_stmt($in_st);
    $in_st->close();

    writelog("Nuova unità: ".$_POST['newunit']." [".$_POST['symbol']."] inserita");
}

// Update of old units 
if(isset($_POST['unit']))
{
    $up_st = prepare_stmt("UPDATE UNITA SET udm=?, simbolo=? 
    WHERE id_udm=?");
    $up_st->bind_param("ssi", $name, $symbol, $id);

    foreach($_POST['unit'] as $id => $name)
    {
        $symbol = $_POST['symbol'][$id];
        execute_stmt($up_st);
    }    
    
    writelog("Aggiornamento unità");
}

$_SESSION['alert'] = "Aggiornamento completato";
header("Location: /admin/unit.php");
?>
