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

// Backend script to update a student's information
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
chk_access(PROFESSOR);
connect();

// Checks if the user owns at least one class with the student 
// (or is an administrator)
if(!chk_auth(ADMINISTRATOR))
{
	$chk_st = prepare_stmt("SELECT * FROM student
		JOIN instance ON student_fk=student_id
		JOIN class ON class_fk=class_id 
		WHERE student_id=?
		AND user_fk=?");
	$chk_st->bind_param("ii", $_GET['id'], $_SESSION['id']);

	$ret = execute_stmt($chk_st);
	$chk_st->close();
	if($ret->num_rows == 0)
	{
		set_alert("Permessi insufficienti per modificare le informazioni");
		header("Location: /register/register.php");
		exit;
	}
}

$lastname = maiuscolo($_POST['lastname']);
$firstname = maiuscolo($_POST['firstname']);

$up_st = prepare_stmt("UPDATE student SET lastname=?, firstname=?, gender=? WHERE student_id=?");
$up_st->bind_param("sssi", $lastname, $firstname, $_POST['gender'], $_GET['id']);
execute_stmt($up_st);
$up_st->close();

writelog("Aggiornamento informazioni studente ".$_GET['id']);
set_alert("Aggiornamento effettuato con successo");

header("Location: /register/student_show.php?id=".$_GET['id']);
?>
