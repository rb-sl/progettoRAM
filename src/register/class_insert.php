<?php 
// Script to insert a new class
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
include $_SERVER['DOCUMENT_ROOT']."/libraries/lib_reg.php";
include $_SERVER['DOCUMENT_ROOT']."/libraries/lib_stat.php";
chk_access(PROFESSOR);
connect();

$section = strtoupper($_POST['sez']);

// Check of class uniqueness per year and school
$chk_st = prepare_stmt("SELECT * FROM CLASSI 
	JOIN SCUOLE ON fk_scuola=id_scuola 
	WHERE classe=? AND sez=? AND anno=? AND fk_scuola=?");
$chk_st->bind_param("isii", $_POST['cl'], $section, $_POST['anno'], $_SESSION['scuola']);
$chk = execute_stmt($chk_st);
$chk_st->close();

if($chk->num_rows > 0)
{
	$row = $chk->fetch_assoc();
	$_SESSION['alert'] = "Errore: la classe ".$_POST['cl'].$section." ".$_POST['anno']
		." / ".($_POST['anno'] + 1)." è già registrata (".$row['nomescuola'].").";
	header("Location: /register/register.php");
	exit;
}

// New class creation
$in_st = prepare_stmt("INSERT INTO CLASSI(classe, sez, anno, fk_prof, fk_scuola) 
	VALUES(?, ?, ?, ?, ?)");
$in_st->bind_param("isiii", $_POST['cl'], $section, $_POST['anno'], $_SESSION['id'], $_SESSION['scuola']);
execute_stmt($in_st);
$in_st->close();

$class = $mysqli->insert_id;

writelog("Creazione classe $class: ".$_POST['cl'].$section." ".$_POST['anno']);

class_students(false, $class, 
	isset($_POST['pr']) ? $_POST['pr'] : null,
	isset($_POST['cst']) ? $_POST['cst'] : null,
	isset($_POST['nst']) ? $_POST['nst'] : null,
	isset($_POST['sesso']) ? $_POST['sesso'] : null,
	isset($_POST['ext']) ? $_POST['ext'] : null);

header("Location: /register/class_show.php?id=$class");
?>
