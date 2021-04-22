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

// Script to check if a student with the same data of those sent
// in the ajax request already exists
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
if(!chk_access(PROFESSOR, false))
{
	echo "null";
	exit;
}
connect();

$st = json_decode($_GET['st']);
$cl = json_decode($_GET['cl']);

$year = $cl->anno - 1;
$class = $cl->classe;

// Statement to search for previous years' students with same data 
$dup_st = prepare_stmt("SELECT id_stud, cogs, noms, id_ist, classe, sez, anno FROM STUDENTI
	JOIN ISTANZE ON fk_stud=id_stud 
	JOIN CLASSI ON fk_cl=id_cl
	WHERE cogs=? AND (noms=? OR noms='') AND sesso=?
	AND anno=? AND classe<=? AND fk_scuola=? 
	GROUP BY id_stud 
	HAVING(anno=MAX(anno))");
$dup_st->bind_param("sssiii", $lastname, $firstname, $gender, $year, $class, $_SESSION['school']);

$data = [];
foreach($st as $k => $stud)
{
	$lastname = $stud->cogs;
	$firstname = $stud->noms;
	$gender = $stud->sesso;

	$ret = execute_stmt($dup_st);

	if($ret->num_rows != 0)
	{
		$data[$k]['idel'] = $k;
		$data[$k]['cogs'] = $lastname;
		$data[$k]['noms'] = $firstname;
		$data[$k]['sesso'] = $gender;

		while($row = $ret->fetch_assoc())
			$data[$k]['list'][] = "<div class='form-check'>
				<input type='radio' id='ext".$row['id_stud']."' class='form-check-input' name='ext[".$stud->cogs."_"
				.$stud->noms."_".$stud->sesso."]' value='".$row['id_stud']."'>
				<label class='form-check-label' for='ext".$row['id_stud']."'>"
				.$row['classe'].$row['sez']." ".$row['anno']."/".($row['anno'] + 1)
				."</label>
			</div>";
	}
}
$dup_st->close();

echo json_encode($data);
?>
