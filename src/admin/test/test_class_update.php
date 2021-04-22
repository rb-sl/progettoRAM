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

// Backend script to add a new test type
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
chk_access(ADMINISTRATOR);
connect();

// New class insert
if(isset($_POST['newrow1']) and $_POST['newrow1'] != "")
{
	$in_st = prepare_stmt("INSERT INTO CLTEST(nomec) 
		VALUES (?)");
	$in_st->bind_param("s", $_POST['newrow1']);
	execute_stmt($in_st);
	$in_st->close();

	writelog("Nuova classe di test: ".$_POST['newrow1']." inserita");
}

// Update of old test classes 
if(isset($_POST['col1']))
{
	$up_st = prepare_stmt("UPDATE CLTEST SET nomec=? WHERE id_cltest=?");
	$up_st->bind_param("si", $name, $id);

	foreach($_POST['col1'] as $id => $name)
		execute_stmt($up_st);
	
	writelog("Aggiornamento classi test");
}

$_SESSION['alert'] = "Aggiornamento completato";
header("Location: /admin/test/test_class.php");
?>
