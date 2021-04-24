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

// Script to validate values inserted in show_class.php
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
include $_SERVER['DOCUMENT_ROOT']."/libraries/lib_reg.php";
if(!chk_access(PROFESSOR, false))
{
	echo "null";
	exit;
}
connect();

// Acceptable is set to 0 if some value is found non-valid
$acceptable = true;

// New test check
if(isset($_POST['ntest']))
	foreach($_POST['ntest'] as $student_fk => $val)
		if($val and !is_accettable($_POST['test'], $val))
		{
	  		$acceptable = false;

			// Adds the current element to the array of non-valid elements
			$err['ntest'][] = $student_fk;
		}

// Update check
if(isset($_POST['pr']))
	foreach($_POST['pr'] as $idtest => $s)
		foreach($s as $idist => $val)
			if($val and !is_accettable($idtest, $val)) 
			{
				$acceptable = false;

				$err['pr'][$idist] = $idtest;
			}

// No data is sent if correct
if($acceptable)
	echo json_encode(true);
else
{
	$json = json_encode($err);
	writelog("Errore di inserimento:\n>>$json");
	echo $json;
}
?>
