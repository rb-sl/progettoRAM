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
	$chk_st = prepare_stmt("SELECT * FROM STUDENTI
		JOIN ISTANZE ON fk_stud=id_stud
		JOIN CLASSI ON fk_cl=id_cl 
		WHERE id_stud=?
		AND fk_prof=?");
	$chk_st->bind_param("ii", $_GET['id'], $_SESSION['id']);

	$ret = execute_stmt($chk_st);
	$chk_st->close();
	if($ret->num_rows == 0)
	{
		$_SESSION['alert'] = "Permessi insufficienti per modificare le informazioni";
		header("Location: /register/register.php");
		exit;
	}
}

$lastname = maiuscolo($_POST['cogs']);
$firstname = maiuscolo($_POST['noms']);

$up_st = prepare_stmt("UPDATE STUDENTI SET cogs=?, noms=?, sesso=? WHERE id_stud=?");
$up_st->bind_param("sssi", $lastname, $firstname, $_POST['sesso'], $_GET['id']);
execute_stmt($up_st);
$up_st->close();

writelog("[Aggiornamento informazioni studente] ".$_GET['id']);
$_SESSION['alert'] = "Aggiornamento effettuato con successo";

header("Location: /register/student_show.php?id=".$_GET['id']);
?>
