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

// Script to insert a new class
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
include $_SERVER['DOCUMENT_ROOT']."/libraries/lib_reg.php";
include $_SERVER['DOCUMENT_ROOT']."/libraries/lib_stat.php";
chk_access(PROFESSOR);
connect();

$section = strtoupper($_POST['section']);

// Check of class uniqueness per year and school
$chk_st = prepare_stmt("SELECT * FROM class 
	JOIN school ON school_fk=school_id 
	WHERE class=? AND section=? AND class_year=? AND school_fk=?");
$chk_st->bind_param("isii", $_POST['cl'], $section, $_POST['class_year'], $_SESSION['school']);
$chk = execute_stmt($chk_st);
$chk_st->close();

if($chk->num_rows > 0)
{
	$row = $chk->fetch_assoc();
	$_SESSION['alert'] = "Errore: la classe ".$_POST['cl'].$section." ".$_POST['class_year']
		." / ".($_POST['class_year'] + 1)." è già registrata (".$row['school_name'].").";
	header("Location: /register/register.php");
	exit;
}

// New class creation
$in_st = prepare_stmt("INSERT INTO class(class, section, class_year, user_fk, school_fk) 
	VALUES(?, ?, ?, ?, ?)");
$in_st->bind_param("isiii", $_POST['cl'], $section, $_POST['class_year'], $_SESSION['id'], $_SESSION['school']);
execute_stmt($in_st);
$in_st->close();

$class = $mysqli->insert_id;

writelog("Creazione classe $class: ".$_POST['cl'].$section." ".$_POST['class_year']);

class_students(false, $class, 
	isset($_POST['pr']) ? $_POST['pr'] : null,
	isset($_POST['cst']) ? $_POST['cst'] : null,
	isset($_POST['nst']) ? $_POST['nst'] : null,
	isset($_POST['gender']) ? $_POST['gender'] : null,
	isset($_POST['ext']) ? $_POST['ext'] : null);

header("Location: /register/class_show.php?id=$class");
?>
