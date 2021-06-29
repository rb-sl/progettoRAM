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

// Script to check if a student with the same data of those sent
// in the ajax request already exists
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
if(!chk_access(PROFESSOR, false))
{
	echo "null";
	exit;
}
connect();

$st = json_decode($_GET['st']);
$cl = json_decode($_GET['cl']);

$year = $cl->class_year - 1;
$class = $cl->class;

// Statement to search for previous years' students with same data 
$dup_st = prepare_stmt("SELECT student_id, lastname, firstname, instance_id, class, section, class_year FROM student
	JOIN instance ON student_fk=student_id 
	JOIN class ON class_fk=class_id
	WHERE lastname=? AND (firstname=? OR firstname='') AND gender=?
	AND class_year=? AND class<=? AND school_fk=? 
	GROUP BY student_id 
	HAVING(class_year=MAX(class_year))");
$dup_st->bind_param("sssiii", $lastname, $firstname, $gender, $year, $class, $_SESSION['school']);

$data = [];
foreach($st as $k => $stud)
{
	$lastname = $stud->lastname;
	$firstname = $stud->firstname;
	$gender = $stud->gender;

	$ret = execute_stmt($dup_st);

	if($ret->num_rows != 0)
	{
		$data[$k]['idel'] = $k;
		$data[$k]['lastname'] = htmlentities($lastname);
		$data[$k]['firstname'] = htmlentities($firstname);
		$data[$k]['gender'] = htmlentities($gender);

		while($row = $ret->fetch_assoc())
			$data[$k]['list'][] = "<div class='form-check'>
				<input type='radio' id='ext".$row['student_id']."' class='form-check-input' name='ext["
				.htmlentities($stud->lastname)."_".htmlentities($stud->firstname)."_"
				.htmlentities($stud->gender)."]' value='".$row['student_id']."'>
				<label class='form-check-label' for='ext".$row['student_id']."'>"
				.$row['class'].$row['section']." ".$row['class_year']."/".($row['class_year'] + 1)
				."</label>
			</div>";
	}
}
$dup_st->close();

header("Content-Type: application/json");
echo json_encode($data);
?>
