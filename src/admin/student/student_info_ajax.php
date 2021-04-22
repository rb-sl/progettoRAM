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

// Backend page to answer the ajax request for a student
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
if(!chk_access(ADMINISTRATOR, false))
{
	echo "null";
	exit;
}
connect();

if($_GET['key'] == "id")
{
	$src_st = prepare_stmt("SELECT * FROM STUDENTI
		JOIN ISTANZE ON fk_stud=id_stud
		JOIN CLASSI ON fk_cl=id_cl
		WHERE id_stud=?");
	$src_st->bind_param("i", $id);
	$set = "id";
}
else
{
	$src_st = prepare_stmt("SELECT * FROM STUDENTI
		JOIN ISTANZE ON fk_stud=id_stud
		JOIN CLASSI ON fk_cl=id_cl
		WHERE noms=?
		AND cogs=?");
	$src_st->bind_param("ss", $firstname, $lastname);
	$set = "surname";
}

$num = 1;
while(isset($_GET[$set.$num]))
{
	$id = $_GET['id'.$num];
	$firstname = $_GET['name'.$num];
	$lastname = $_GET['surname'.$num];

	$ret = execute_stmt($src_st);
	
	if($ret->num_rows != 0)
	{
		$row = $ret->fetch_assoc();

		while($row)
		{
			$stud = [];
			$stud['id'] = $row['id_stud'];
			$stud['name'] = $row['noms'];
			$stud['surname'] = $row['cogs'];
			$stud['gender'] = $row['sesso'];

			while($row and $row['id_stud'] == $stud['id'])
			{
				$stud['classlist'][$row['id_cl']] = $row['classe'].$row['sez']." "
					.$row['anno']."/".($row['anno'] + 1);
				$row = $ret->fetch_assoc();
			}

			$info['stud'.$num][] = $stud;
		}
	}
	else
		$info['stud'.$num] = null;
	$num++;
}

$src_st->close();

echo json_encode($info);
?>
