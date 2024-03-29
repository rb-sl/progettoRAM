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

// Backend page to modify the index's announcement
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
include $_SERVER['DOCUMENT_ROOT']."/libraries/lib_admin.php";
chk_access(ADMINISTRATOR);
connect();

$compiled = compile_text($_POST['announcement']);

// If there are syntax errors the text is saved in session
// to be displayed to the user
if(is_array($compiled))
{
	$_SESSION['syntax_error'] = $compiled[1]." alla riga ".$compiled[0];
	$_SESSION['index_text'] = $_POST['announcement'];
	$_SESSION['important'] = isset($_POST['important']);
	header("Location: /admin/announcement_modify.php");
	exit;
}

unset($_SESSION['index_text']);
unset($_SESSION['important']);

if($compiled != "")
	if(isset($_POST['important']))
		$compiled = "<h2 class='dangercolor'>>> Attenzione <<\n$compiled\n</h2>";
	else
		$compiled = "<div class='announcement primarycolor'>$compiled</div>";
else
	$compiled = null;

$up_st = prepare_stmt("UPDATE admindata SET index_text=?, index_compiled=?");
$up_st->bind_param("ss", $_POST['announcement'], $compiled);
execute_stmt($up_st);
$up_st->close();

writelog("Modifica messaggio in home");
set_alert("Annuncio modificato correttamente");
header("Location: /admin/announcement_modify.php");
?>
