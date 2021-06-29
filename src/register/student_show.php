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

// Script to show a student's information
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
include $_SERVER['DOCUMENT_ROOT']."/libraries/lib_reg.php";
include $_SERVER['DOCUMENT_ROOT']."/libraries/lib_stat.php";
chk_access(PROFESSOR);
connect();

// Gets the student's information
$stud_st = prepare_stmt("SELECT * FROM student WHERE student_id=?");
$stud_st->bind_param("i", $_GET['id']);
$ret = execute_stmt($stud_st);
$stud_st->close();

$stud = $ret->fetch_assoc();

$rclass = col_class($stud['student_id']);

// The loading is interrupted if the user does not own at least one class with the student
if($rclass === null)
{
	$_SESSION['alert'] = "Permessi insufficienti per visualizzare le informazioni";
	header("Location: /register/register.php");
	exit;
}

$lastname = htmlentities($stud['lastname']);
$firstname = htmlentities($stud['firstname']);
$gender = $stud['gender'];

show_premain("Dati di $lastname $firstname ($gender)");
?>
<h2>Prove di <?=$lastname?> <?=$firstname?> (<?=$gender?>)</h2>

<div>
	<a href="/register/student_modify.php?id=<?=$stud['student_id']?>" class="btn btn-warning marginunder">Modifica</a>
	<br>
	<a href="student_show_stat.php?id=<?=$_GET['id']?>" class="btn btn-primary marginunder">Elaborazione dati</a> 
</div>

<div class="tdiv">
	<div id="tos" class="inner">
		<table id="tts" class="table table-light table-striped">
			<tr id="thr" class="dat">
				<td class="topleft topfix leftfix">
					<button type="button" id="btnstat" class="btn btn-secondary btnmenu overpad">Medie e mediane</button>
				</td>
<?php
// Construction of table body
$result_st = prepare_stmt("SELECT * FROM results JOIN instance ON instance_fk=instance_id
	JOIN test ON test_fk=test_id
	JOIN unit ON unit_fk=unit_id
	WHERE student_fk=?");
$result_st->bind_param("i", $_GET['id']);
$retprove = execute_stmt($result_st);

while($row = $retprove->fetch_assoc())
{
	$vals[$row['test_fk']][] = $row['value'];
	// The date is added if it is not a placeholder
	$rclass[$row['class_fk']][$row['test_fk']] = ($row['date'] != "0000-00-00" ? " title='".$row['date']."'" : "").">"
		.$row['value']." ".$row['symbol']."</td";
}
$result_st->close();

$ret = get_test($_GET['id'], true);

$ravg = "";
$rmed = "";
$idtest = [];
while($row = $ret->fetch_assoc())
{
  	echo "<td id='c".$row['test_id']."' class='col topfix'>".htmlentities($row['test_name'])."</td>";
	$idtest[] = $row['test_id'];

	$ravg .= "<td id='r".$row['test_id']."'>".$row['avg']." ".htmlentities($row['symbol'])."</td>";	
	$rmed .= "<td id='r".$row['test_id']."' class='borderunder'>"
		.arr_med($vals[$row['test_id']], 2)." ".htmlentities($row['symbol'])."</td>";
}
echo "</tr>
	<tr class='dat r_stat jQhidden'>
		<td class='leftfix evenrow'>Medie:</td>
		$ravg
	</tr>
	<tr class='dat r_stat jQhidden'>
		<td class='leftfix oddrow'>Mediane:</td>
		$rmed
	</tr>";

foreach($rclass as $id => $cl)
{
	echo $cl['clrow'];
	foreach($idtest as $idt)
   	{
		echo "<td id='$id"."_$idt' class='jdat r_$id c_$idt'";
   		if(isset($cl[$idt]))
	   		echo $cl[$idt];
		echo "></td>";
   	}
	echo "</tr>\n";
}
?>
		</table>
	</div>
</div>

<script src="/register/js/common_register.js"></script>

<?php show_postmain(); ?>
