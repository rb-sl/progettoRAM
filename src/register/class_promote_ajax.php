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

// Script to handle ajax calls about students to be promoted
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
include $_SERVER['DOCUMENT_ROOT']."/libraries/lib_reg.php";
if(!chk_access(PROFESSOR, false))
{
	echo "null";
	exit;
}
connect();

$prom_st = prepare_stmt("SELECT class, section, class_year FROM class WHERE class_id=?");
$prom_st->bind_param("i", $_GET['toprom']);
$ret = execute_stmt($prom_st);
$prom_st->close();

$newclass = $ret->fetch_assoc();
$data['cl'] = $newclass['class'] + 1;
$data['section'] = $newclass['section'];
$data['class_year'] = $newclass['class_year'] + 1;
$data['list'] = build_chk_table($_GET['toprom'], true);

header('Content-Type: application/json');
echo json_encode($data);
?>
