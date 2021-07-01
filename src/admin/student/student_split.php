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

// Back end page to split student instances
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
chk_access(ADMINISTRATOR);
connect();

$stud1 = $_POST['stud0'];

// Creation of the new student
$in_st = prepare_stmt("INSERT INTO student(firstname, lastname, gender)  
	SELECT firstname, lastname, gender FROM student WHERE student_id=?");
$in_st->bind_param("i", $stud1);
execute_stmt($in_st);
$in_st->close();

$stud2 = $mysqli->insert_id;

// Update for instances to be moved to the new student
$up_st = prepare_stmt("UPDATE instance SET student_fk=? WHERE student_fk=? AND class_fk=?");
$up_st->bind_param("iii", $stud2, $stud1, $class);

foreach($_POST['split'] as $class => $val)
	if($val == 2)
		execute_stmt($up_st);

$up_st->close();
		
writelog("Studente $stud1 separato in $stud1 e $stud2");
set_alert("Studente $stud1 separato in $stud1 e $stud2");
header("Location: /admin/student/student_correction.php");
?>
