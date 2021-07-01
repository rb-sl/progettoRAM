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

// Form page to change a student's information
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
chk_access(PROFESSOR);
connect();
show_premain("Modifica Studente");

if(!chk_auth(ADMINISTRATOR))
{
	$stud_st = prepare_stmt("SELECT * FROM student 
		JOIN instance ON student_fk=student_id
		JOIN class ON class_fk=class_id 
		WHERE student_id=?
		AND user_fk=?");
	$stud_st->bind_param("ii", $_GET['id'], $_SESSION['id']);
}
else
{
	$stud_st = prepare_stmt("SELECT * FROM student 
		JOIN instance ON student_fk=student_id
		JOIN class ON class_fk=class_id 
		WHERE student_id=?");
	$stud_st->bind_param("i", $_GET['id']);
}

$ret = execute_stmt($stud_st);
$stud_st->close();

$stud = $ret->fetch_assoc();

if(!$stud)
{
	set_alert("Permessi insufficienti per visualizzare le informazioni");
	header("Location: /register/register.php");
	exit;
}

if($stud['gender'] == "m")
{
	$m = "checked";
	$f = "";
}
else
{
	$m = "";
	$f = "checked";
}
?>

<h2>Modifica studente <a href="./student_show.php?id=<?=$_GET['id']?>" class="btn btn-warning">Indietro</a></h2>

<form action="student_update.php?id=<?=$_GET['id']?>" method="POST">
	<table class="table table-light table-striped studtable marginunder">
		<tr>
			<th>Cognome:</th>
			<td><input type="text" name="lastname" value="<?=htmlentities($stud['lastname'])?>" required></td>
		</tr>
		<tr>
			<th>Nome:</th>
			<td><input type="text" name="firstname" value="<?=htmlentities($stud['firstname'])?>" required></td>
		</tr> 
		<tr>
			<th>Sesso:</th>
			<td class="containerflex">
				<div class="form-check">
					<input type="radio" id="radiom" class="form-check-input" name="gender" value="m" <?=$m?> required>
					<label class="form-check-label" for="radiom">M</label>
				</div>
				<div class="form-check">
					<input type="radio" id="radiof" class="form-check-input" name="gender" value="f" <?=$f?> required>
					<label class="form-check-label" for="radiof">F</label>
				</div>
			</td>
		</tr>
	</table>

	<input type="submit" class="btn btn-warning" value="Aggiorna dati studente">
</form>

<?php show_postmain(); ?>
