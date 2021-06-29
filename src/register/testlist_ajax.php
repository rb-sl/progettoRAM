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

// Ajax script to retrieve the tests yet to do of a given class
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
if(!chk_access(PROFESSOR, false))
{
	echo "null";
	exit;
}
connect();

// Statement to find tests in the favourite list of the user
// not yet done by the class
$test_st = prepare_stmt("SELECT test_id, test_name FROM test
	JOIN favourites ON test_fk=test_id
	WHERE test_id NOT IN (
		SELECT DISTINCT(test_fk) FROM results JOIN instance ON instance_fk=instance_id WHERE class_fk=?
	) AND user_fk=? 
	ORDER BY test_name");
$test_st->bind_param("ii", $_GET['id'], $_SESSION['id']);
$ret = execute_stmt($test_st);
$test_st->close();

$data = [];
while($row = $ret->fetch_assoc())
{
	$buff['id'] = $row['test_id'];
	$buff['name'] = htmlentities($row['test_name']);
	$data[] = $buff;
}

header("Content-Type: application/json");
echo json_encode($data);
?>
