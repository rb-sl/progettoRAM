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

// Insert of new tests in the system
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
chk_access(PROFESSOR_GRANTS);
connect();

// The system blocks the insert if a test with the given name is already present
$name_st = prepare_stmt("SELECT * FROM TEST WHERE nometest=?");
$name_st->bind_param("s", $_POST['testname']);

$name = execute_stmt($name_st);
$name_st->close();

if($name->num_rows > 0)
{
	$_SESSION['alert'] = "Errore: Un altro test nominato \"".$_POST['testname']."\" è già presente nel sistema";
	header("Location: ".$_SERVER['HTTP_REFERER']);
	exit;
}

$in_st = prepare_stmt("INSERT INTO TEST (nometest, fk_cltest, fk_udm, pos, fk_tipot, posiz, equip, esec, cons, limite, valut) 
	VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

$in_st->bind_param("siisissssss", $_POST['testname'], $_POST['class'], $_POST['unit'], $_POST['positive'], 
	$_POST['type'], $_POST['position'], $_POST['equipment'], $_POST['execution'], $_POST['suggestions'], 
	$_POST['limit'], $_POST['grading']);

execute_stmt($in_st);
$in_st->close();

writelog("[+test] ".$mysqli->insert_id." ".$_POST['testname']);

header("Location: /test/test_show.php?id=".$mysqli->insert_id);
exit;
?>
