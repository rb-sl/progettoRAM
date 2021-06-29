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

// Retrieves grade levels for the given user from the database on an ajax request
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
chk_access(PROFESSOR);
connect();

$gr_st = prepare_stmt("SELECT * FROM grading JOIN grade ON grade_fk=grade_id WHERE user_fk=?");
$gr_st->bind_param("i", $_GET['idprof']);

$ret = execute_stmt($gr_st);
$gr_st->close();

$data = [];
while($row = $ret->fetch_assoc())
	$data[$row['grade'] * 10] = $row['percentile'];

header("Content-Type: application/json");
echo json_encode($data);
?>
