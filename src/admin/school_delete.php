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

// Script to delete a school
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
chk_access(ADMINISTRATOR);
connect();

$chk_st = prepare_stmt("SELECT COUNT(*) AS n FROM class WHERE school_fk=?");
$chk_st->bind_param("i", $_GET['id']);
$ret = execute_stmt($chk_st);
$chk_st->close();

$r = $ret->fetch_assoc();

// If some classes are present for the school the deletion is blocked
if($r['n'] === 0)
{
	$del_st = prepare_stmt("DELETE FROM school WHERE school_id=?");
	$del_st->bind_param("i", $_GET['id']);
	execute_stmt($del_st);
	$del_st->close();

	writelog("Scuola ".$_GET['id']." cancellata");

	set_alert("Scuola eliminata correttamente");
}
else
{
	writelog("Tentativo cancellazione scuola ".$_GET['id']." bloccato; esistono ".$r['n']." classi associate");

	set_alert("Impossibile eliminare la scuola: esistono ".$r['n']." classi associate");
}

header("Location: /admin/school.php");
exit;
?>
