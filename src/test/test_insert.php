<?php
// Insert of new tests in the system
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
chk_access(1);
connect();

// The system blocks the insert if a test with the given name is already present
$name_st = prepare_stmt("SELECT * FROM TEST WHERE nometest=?");
$name_st->bind_param("s", $_POST['testname']);

$name = execute_stmt($name_st);
$name_st->close();

if($name->num_rows > 0)
{
	$_SESSION['alert'] = "Errore: Un altro test nominato \"".$_POST['testname']."\" è già presente nel sistema";
	header("Location: ".$_SERVER['HTTP_REFERER']);
	exit;
}

$in_st = prepare_stmt("INSERT INTO TEST (nometest, fk_cltest, fk_udm, pos, fk_tipot, posiz, equip, esec, cons, limite, valut) VALUES 
	(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

$in_st->bind_param("siisissssss", $_POST['testname'], $_POST['class'], $_POST['unit'], $_POST['positive'], 
	$_POST['type'], $_POST['position'], $_POST['equipment'], $_POST['execution'], $_POST['suggestions'], $_POST['limit'], $_POST['grading']);

execute_stmt($in_st);
$in_st->close();

writelog("[+test] ".$mysqli->insert_id." ".$_POST['testname']);

header("Location: /test/test_show.php?id=".$mysqli->insert_id);
exit;
?>