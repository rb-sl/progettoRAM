<?php
// Insert of new tests in the system
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
chk_access(1);
connect();

// The system blocks the insert if a test with the given name is already present
$name_st = prepare_stmt("SELECT * FROM TEST WHERE nometest=?");
$name_st->bind_param("s", $_POST['nometest']);

$nome = execute_stmt($name_st);
if($nome->num_rows > 0)
{
	$_SESSION['alert'] = "Errore:\\nIl test \"".$_POST['nometest']."\" è già presente nel sistema";
	header("Location: ".$_SERVER['HTTP_REFERER']);
	exit;
}

$in_st = prepare_stmt("INSERT INTO TEST (nometest, fk_cltest, fk_udm, pos, fk_tipot, posiz, equip, esec, cons, limite, valut) VALUES 
	(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

$in_st->bind_param("siisissssss", $_POST['nometest'], $_POST['classe'], $_POST['unita'], $_POST['pos'], 
	$_POST['tipo'], $_POST['posiz'], $_POST['equip'], $_POST['esec'], $_POST['cons'], $_POST['limite'], $_POST['valut']);

execute_stmt($in_st);
writelog("[+test] ".$mysqli->insert_id." ".$_POST['nometest']);

header("Location: /test/show_test.php?id=".$mysqli->insert_id);
exit;
?>