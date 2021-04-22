<?php 
// Copyright 2021 Roberto Basla

// This file is part of progettoRAM.

// progettoRAM is free software: you can redistribute it and/or modify
// it under the terms of the GNU Affero General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

// progettoRAM is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU Affero General Public License for more details.

// You should have received a copy of the GNU Affero General Public License
// along with progettoRAM.  If not, see <http://www.gnu.org/licenses/>.

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
