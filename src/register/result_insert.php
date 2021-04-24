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

// Script to insert test results
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
include $_SERVER['DOCUMENT_ROOT']."/libraries/lib_reg.php";
chk_access(PROFESSOR);
connect();

// Results insert or update
$in_st = prepare_stmt("INSERT INTO results(test_fk, instance_fk, value, date) 
	VALUES(?, ?, ?, CURDATE()) ON DUPLICATE KEY UPDATE value=?, date=CURDATE()");
$in_st->bind_param("iidd", $test, $instance, $value, $value);

// Deletion of empty results
$del_st = prepare_stmt("DELETE FROM results WHERE test_fk=? AND instance_fk=?");
$del_st->bind_param("ii", $test, $instance);

$insert = 0;
$modify = 0;
$delete = 0;

// New test's results insert
if(isset($_POST['ntest']))
{
	$test = $_POST['test'];
	foreach($_POST['ntest'] as $instance => $value)
		if($value)
		{
			execute_stmt($in_st);
			$insert++;
		}
}

// Old results updates - if a value is empty it is deleted
if(isset($_POST['pr']))
	foreach($_POST['pr'] as $test => $s)
		foreach($s as $instance => $value)
			if(is_numeric($value))
			{
				execute_stmt($in_st);
				$modify++;
			}
			else
			{
				execute_stmt($del_st);
				$delete++;
			}

$in_st->close();
$del_st->close();

writelog("[prove] [classe: ".$_GET['cl']."] Prove da nuovo test: $insert; Prove modificate: $modify; Prove cancellate: $delete");

header("Location: ".$_SERVER['HTTP_REFERER']);
?>
