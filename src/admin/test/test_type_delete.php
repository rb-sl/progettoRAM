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

// Script to delete a test type
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
chk_access(ADMINISTRATOR);
connect();

$chk_st = prepare_stmt("SELECT COUNT(*) AS n FROM TEST WHERE fk_tipot=?");
$chk_st->bind_param("i", $_GET['id']);
$ret = execute_stmt($chk_st);
$chk_st->close();

$r = $ret->fetch_assoc();

// If some tests are present for the type the deletion is blocked
if($r['n'] === 0)
{
	$del_st = prepare_stmt("DELETE FROM TIPOTEST WHERE id_tipot=?");
	$del_st->bind_param("i", $_GET['id']);
	execute_stmt($del_st);
	$del_st->close();

	writelog("Tipo test ".$_GET['id']." cancellato");

	$_SESSION['alert'] = "Tipo di test eliminato correttamente";
}
else
{
	writelog("Tentativo cancellazione tipo test ".$_GET['id']." bloccato; esistono ".$r['n']." test associati");

	$_SESSION['alert'] = "Impossibile eliminare il tipo di test: esistono ".$r['n']." test associati";
}

header("Location: /admin/test/test_type.php");
exit;
?>
