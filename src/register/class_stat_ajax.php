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

// Script to send statistical data about a class on an ajax request
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
include $_SERVER['DOCUMENT_ROOT']."/libraries/lib_reg.php";
include $_SERVER['DOCUMENT_ROOT']."/libraries/lib_stat.php";
if(!chk_access(PROFESSOR, false))
{
	echo "null";
	exit;
}
connect();

$cond = cond_builder(); 

if(isset($_GET['forstud']) && $_GET['forstud'] == "true")
	$forstud = $_GET['forstud'];
else
	$forstud = false;

// Builds the response based on the type of data requested
switch($_GET['vis'])
{
	case "prc":
		if($rstud = get_perc($_GET['id'], $cond, $forstud))
			$am = get_avgmed($_GET['id'], $rstud['val'], true, $forstud);
		break;
	case "std":
		if($rstud = get_std($_GET['id'], $cond, $forstud))	
			$am = get_avgmed($_GET['id'], $rstud['val'], false, $forstud);
		break;
	case "gr":
		if($rstud = get_grades($_GET['id'], $cond, $forstud))
			$am = get_avgmed_grades($_GET['id'], $rstud, $forstud);
		break;
}

echo json_encode(array_merge($rstud, $am));
?>
