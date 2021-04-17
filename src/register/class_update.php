<?php 
// Backend script to update a modified class
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
include $_SERVER['DOCUMENT_ROOT']."/libraries/lib_reg.php";
chk_access(PROFESSOR);
connect();

$section = strtoupper($_POST['sez']);

// Check of class uniqueness per year and school
$chk_st = prepare_stmt("SELECT * FROM CLASSI JOIN SCUOLE ON fk_scuola=id_scuola 
	WHERE classe=? AND sez=? AND anno=? AND fk_scuola=? AND id_cl<>?");
$chk_st->bind_param("isiii", $_POST['cl'], $section, $_POST['anno'], $_SESSION['school'], $_GET['id']);
$chk = execute_stmt($chk_st);
$chk_st->close();

if($chk->num_rows > 0)
{
	$row = $chk->fetch_assoc();
	$_SESSION['alert'] = "Errore: un'altra classe ".$_POST['cl'].$section." ".$_POST['anno']
		." / ".($_POST['anno'] + 1)." è già registrata (".$row['nomescuola'].").";
	header("Location: /register/class_modify.php?id=".$_GET['id']);
	exit;
}

$up_st = prepare_stmt("UPDATE CLASSI SET classe=?, sez=?, anno=? WHERE id_cl=?");
$up_st->bind_param("isii", $_POST['cl'], $section, $_POST['anno'], $_GET['id']);
$ret = execute_stmt($up_st);
$up_st->close();

writelog("[Modifica classe] ".$_GET['id']);

$idlist = class_students(true, $_GET['id'], 
	isset($_POST['pr']) ? $_POST['pr'] : null,
	isset($_POST['cst']) ? $_POST['cst'] : null,
	isset($_POST['nst']) ? $_POST['nst'] : null,
	isset($_POST['sesso']) ? $_POST['sesso'] : null,
	isset($_POST['ext']) ? $_POST['ext'] : null);

// Deletion of instances not in the class anymore. The deletion of students without
// instances is handled with a trigger
$del_st = prepare_stmt("DELETE FROM ISTANZE WHERE fk_cl=? AND fk_stud NOT IN ($idlist)");
$del_st->bind_param("i", $_GET['id']);
execute_stmt($del_st);

$_SESSION['alert'] = "Aggiornamento effettuato con successo";

header("Location: /register/class_show.php?id=".$_GET['id']);
?>
