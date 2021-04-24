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

// Backend page to modify the application's description
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
include $_SERVER['DOCUMENT_ROOT']."/libraries/lib_admin.php";
chk_access(ADMINISTRATOR);
connect();

$compiled = compile_text($_POST['project']);

// If there are syntax errors the text is saved in session
// to be displayed to the user
if(is_array($compiled))
{
	$_SESSION['syntax_error'] = $compiled[1]." alla riga ".$compiled[0];
	$_SESSION['project_text'] = $_POST['project'];
	header("Location: /admin/project_modify.php");
	exit;
}

unset($_SESSION['project_text']);

$up_st = prepare_stmt("UPDATE admindata SET project_text=?, project_compiled=?");
$up_st->bind_param("ss", $_POST['project'], $compiled);
execute_stmt($up_st);
$up_st->close();

writelog("Modifica descrizione del progetto");
$_SESSION['alert'] = "Descrizione modificata correttamente";
header("Location: /admin/project_modify.php");
?>
