<?php
// Script to update tests
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
chk_access(1);
connect();

// If the new name corresponds to another test the update is blocked
$test_st = prepare_stmt("SELECT * FROM TEST WHERE nometest=? AND id_test<>?");
$test_st->bind_param("si", $_POST['nometest'], $_GET['id']);
$ret = execute_stmt($test_st);
$test_st->close();

if($ret->num_rows > 0)
{
	$_SESSION['alert'] = "Errore:\\nIl test ".$_POST['nometest']." è già presente nel sistema";
	header("Location: ".$_SERVER['HTTP_REFERER']);
	exit;
}

$up_st = prepare_stmt("UPDATE TEST SET nometest=?, fk_cltest=?, fk_udm=?, pos=?, fk_tipot=?, posiz=?, 
	equip=?, esec=?, cons=?, limite=?, valut=? 
	WHERE id_test=?");
$up_st->bind_param("siisissssssi", $_POST['nometest'], $_POST['classe'], $_POST['unita'], $_POST['pos'], $_POST['tipo'], 
	$_POST['posiz'], $_POST['equip'], $_POST['esec'], $_POST['cons'], $_POST['limite'], $_POST['valut'], $_GET['id']);

execute_stmt($up_st);

writelog("[->test] ".$_GET['id']."->".$_POST['nometest']);

header("Location: /test/show_test.php?id=".$_GET['id']);
exit;
?>