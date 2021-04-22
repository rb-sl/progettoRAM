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

// Back end page to merge student instances
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
chk_access(ADMINISTRATOR);
connect();

if($_POST['keep'] == 1)
{
	$keepid = $_POST['stud1'];
	$delid = $_POST['stud2'];
}
else
{
	$keepid = $_POST['stud2'];
	$delid = $_POST['stud1'];
}

// Instances are moved to the student to be kept, while the other
// will be deleted by the trigger
$up_st = prepare_stmt("UPDATE ISTANZE SET fk_stud=? WHERE fk_stud=?");
$up_st->bind_param("ii", $keepid, $delid);
execute_stmt($up_st);

// If different classes for the same year are specified an error
// is returned by a trigger
if($up_st->error)
	$_SESSION['alert'] = $up_st->error;
else
{
	writelog("Studenti $keepid e $delid uniti in $keepid");
	$_SESSION['alert'] = "Studenti $keepid e $delid uniti in $keepid";
}

$up_st->close();

header("Location: /admin/student/student_correction.php");
?>
