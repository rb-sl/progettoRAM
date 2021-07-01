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

// Backend script to add a new unit
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
chk_access(ADMINISTRATOR);
connect();

// New unit insert
if(isset($_POST['newrow1']) and $_POST['newrow1'] != "")
{
	$in_st = prepare_stmt("INSERT INTO unit(unit_name, symbol) 
		VALUES (?, ?)");
	$in_st->bind_param("ss", $_POST['newrow1'], $_POST['newrow2']);
	execute_stmt($in_st);
	$in_st->close();

	writelog("Nuova unità: ".$_POST['newrow1']." [".$_POST['newrow2']."] inserita");
}

// Update of old units 
if(isset($_POST['col1']))
{
	$up_st = prepare_stmt("UPDATE unit SET unit_name=?, symbol=? 
	WHERE unit_id=?");
	$up_st->bind_param("ssi", $name, $symbol, $id);

	foreach($_POST['col1'] as $id => $name)
	{
		$symbol = $_POST['col2'][$id];
		execute_stmt($up_st);
	}    
	
	writelog("Aggiornamento unità");
}

set_alert("Aggiornamento completato");
header("Location: /admin/test/unit.php");
?>
