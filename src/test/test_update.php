<?php
// Script to update tests
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
chk_access(PROFESSOR_GRANTS);
connect();

// If the new name corresponds to another test the update is blocked
$test_st = prepare_stmt("SELECT * FROM TEST WHERE nometest=? AND id_test<>?");
$test_st->bind_param("si", $_POST['nometest'], $_GET['id']);
$ret = execute_stmt($test_st);
$test_st->close();

if($ret->num_rows > 0)
{
	$_SESSION['alert'] = "Errore: Un test con nome '".$_POST['nometest']."' è già presente nel sistema. Modifiche non effettuate";
	header("Location: ".$_SERVER['HTTP_REFERER']);
	exit;
}

$up_st = prepare_stmt("UPDATE TEST SET nometest=?, fk_cltest=?, fk_udm=?, pos=?, fk_tipot=?, posiz=?, 
	equip=?, esec=?, cons=?, limite=?, valut=? 
	WHERE id_test=?");
$up_st->bind_param("siisissssssi", $_POST['testname'], $_POST['class'], $_POST['unit'], $_POST['positive'], $_POST['type'], 
	$_POST['position'], $_POST['equipment'], $_POST['execution'], $_POST['suggestions'], $_POST['limit'], $_POST['grading'], $_GET['id']);

execute_stmt($up_st);

writelog("[->test] ".$_GET['id']."->".$_POST['nometest']);

header("Location: /test/test_show.php?id=".$_GET['id']);
exit;
?>
