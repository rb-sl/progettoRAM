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

$section = strtoupper($_POST['section']);

// Check of class uniqueness per year and school
$chk_st = prepare_stmt("SELECT * FROM class JOIN school ON school_fk=school_id 
	WHERE class=? AND section=? AND class_year=? AND school_fk=? AND class_id<>?");
$chk_st->bind_param("isiii", $_POST['cl'], $section, $_POST['class_year'], $_SESSION['school'], $_GET['id']);
$chk = execute_stmt($chk_st);
$chk_st->close();

if($chk->num_rows > 0)
{
	$row = $chk->fetch_assoc();
	$_SESSION['alert'] = "Errore: un'altra classe ".$_POST['cl'].$section." ".$_POST['class_year']
		." / ".($_POST['class_year'] + 1)." è già registrata (".$row['school_name'].").";
	header("Location: /register/class_modify.php?id=".$_GET['id']);
	exit;
}

$up_st = prepare_stmt("UPDATE class SET class=?, section=?, class_year=? WHERE class_id=?");
$up_st->bind_param("isii", $_POST['cl'], $section, $_POST['class_year'], $_GET['id']);
$ret = execute_stmt($up_st);
$up_st->close();

writelog("[Modifica classe] ".$_GET['id']);

$idlist = class_students(true, $_GET['id'], 
	isset($_POST['pr']) ? $_POST['pr'] : null,
	isset($_POST['cst']) ? $_POST['cst'] : null,
	isset($_POST['nst']) ? $_POST['nst'] : null,
	isset($_POST['gender']) ? $_POST['gender'] : null,
	isset($_POST['ext']) ? $_POST['ext'] : null);

// Deletion of instances not in the class anymore. The deletion of students without
// instances is handled with a trigger
$del_st = prepare_stmt("DELETE FROM instance WHERE class_fk=? AND student_fk NOT IN ($idlist)");
$del_st->bind_param("i", $_GET['id']);
execute_stmt($del_st);

$_SESSION['alert'] = "Aggiornamento effettuato con successo";

header("Location: /register/class_show.php?id=".$_GET['id']);
?>
