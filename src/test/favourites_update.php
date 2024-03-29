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

// Backend page to update a user's favourite tests
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
chk_access(PROFESSOR);
connect();

// Statement to delete a favourite association
$del_st = prepare_stmt("DELETE FROM favourites WHERE user_fk=? AND test_fk=?");
$del_st->bind_param("ii", $_SESSION['id'], $test);

// Statement to insert a new association
$in_st = prepare_stmt("INSERT INTO favourites(user_fk, test_fk) VALUES(?, ?)");
$in_st->bind_param("ii", $_SESSION['id'], $test);

// Statement to get all associations for the current user
$chk_st = prepare_stmt("SELECT test_fk FROM favourites WHERE user_fk=?");
$chk_st->bind_param("i", $_SESSION['id']);
$ret = execute_stmt($chk_st);
$chk_st->close();

// Each favourite in the db is checked against the user's submission
$in_db = [];
while($row = $ret->fetch_assoc())
{
	$test = $row['test_fk'];
	if(in_array($test, $_POST['fav']))
		$in_db[] = $test;
	else
		execute_stmt($del_st);
}

// The tests than weren't in the DB but were in the POST are inserted
$diff = array_diff($_POST['fav'], $in_db);
foreach($diff as $toinsert)
{
	$test = $toinsert;
	execute_stmt($in_st);
}

set_alert("Preferiti aggiornati correttamente");
writelog("Aggiornamento test preferiti");
header("Location: /test/test.php");
?>
