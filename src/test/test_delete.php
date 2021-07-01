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

// Script to delete a test
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
chk_access(PROFESSOR_GRANTS);
connect();

// If the test does not exist an error is shown to the user
$test_st = prepare_stmt("SELECT * FROM test WHERE test_id=?");
$test_st->bind_param("i", $_GET['id']);

$rettest = execute_stmt($test_st);
$test_st->close();

if($rettest->num_rows == 0)
{
	set_alert("Errore: Test inesistente");
	header("Location: /test/test.php");
	exit;
}

// If some results are present the deletion is blocked
$chk_st = prepare_stmt("SELECT COUNT(*) AS n FROM results WHERE test_fk=?");
$chk_st->bind_param("i", $_GET['id']);
$ret = execute_stmt($chk_st);
$chk_st->close();

$r = $ret->fetch_assoc();

if($r['n'] === 0)
{
	$del_st = prepare_stmt("DELETE FROM test WHERE test_id=?");
	$del_st->bind_param("i", $_GET['id']);
	execute_stmt($del_st);
	$del_st->close();

	writelog("Test ".$_GET['id']." cancellato");

	set_alert("Test eliminato correttamente");
	header("Location: /test/test.php");
}
else
{
	writelog("Tentativo cancellazione test ".$_GET['id']." bloccato; esistono ".$r['n']." prove");

	set_alert("Impossibile eliminare il test: esistono ".$r['n']." prove associate");
	header("Location: /test/test_modify.php?id=".$_GET['id']);
}

exit;
?>
