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

// Page to show the register of a class
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
include $_SERVER['DOCUMENT_ROOT']."/libraries/lib_reg.php";
include $_SERVER['DOCUMENT_ROOT']."/libraries/lib_stat.php";
chk_access(PROFESSOR);
connect();

$cl = get_class_info($_GET['id']);
chk_prof($cl['user_fk']);

show_premain("Registro ".$cl['class'].htmlentities($cl['section'])." ".$cl['class_year']."/".($cl['class_year'] + 1));
?>

<h2>
	Registro della classe <?=$cl['class'].htmlentities($cl['section'])?> - Anno <?=$cl['class_year']."/".($cl['class_year'] + 1)?> 
	<a href="./class_modify.php?id=<?=$_GET['id']?>" class="btn btn-warning">Modifica</a>
</h2>

<div>
	<a href="class_show_stat.php?id=<?=$_GET['id']?>" class="btn btn-primary marginunder">Elaborazione dati della classe</a> 
</div>

<form action="result_insert.php?cl=<?=$_GET['id']?>" id="frm" method="POST">
	<button type="button" id="btnadd" class="btn btn-warning btnmenu">Aggiungi test</button>
	<button type="button" id="btncan" class="btn btn-danger btnmenu jQhidden">Annulla</button>
	<input type="submit" id="btncar" class="btn btn-warning btnmenu jQhidden" value="Salva">
	 
	<div class="tdiv">
  		<div id="tos" class="inner">
			<table id="tts" class="table table-light table-striped">
	  			<tr id="thr" class="dat">
					<td class="topleft topfix leftfix">
						<button type="button" id="btnstat" class="btn btn-secondary overpad">Medie e mediane</button>
					</td>
<?php
$rstud = col_stud();

// Construction of table body
$result_st = prepare_stmt("SELECT * FROM results JOIN instance ON instance_fk=instance_id
	JOIN test ON test_fk=test_id
	JOIN unit ON unit_fk=unit_id
	WHERE class_fk=?");
$result_st->bind_param("i", $_GET['id']);
$retprove = execute_stmt($result_st);

while($row = $retprove->fetch_assoc())
{
	$vals[$row['test_fk']][] = $row['value'];
	// The date is added if it is not a placeholder
	$rstud[$row['instance_id']][$row['test_fk']] = ($row['date'] != "0000-00-00" ? " title='".$row['date']."'" : "").">"
		.$row['value']." ".htmlentities($row['symbol']);
}
$result_st->close();

$ret = get_test($_GET['id']);

// Output of rows for test, average and median
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

// Printing student rows 
foreach($rstud as $idist => $tds)
{
	echo $tds['strow'];
	foreach($idtest as $idt)
   	{
		echo "<td id='$idist"."_$idt' class='jdat r_$idist c_$idt'";
   		
		if(isset($tds[$idt]))
	   		echo $tds[$idt];
		else 
			echo ">";
		
		echo "</td>";
   	}
	echo "</tr>\n";
}
?>
			</table>
		</div>
	</div>
</form>

<script>
	var id = <?=$_GET['id']?>;
</script>
<script src="/register/js/common_register.js"></script>
<script src="/register/js/class_show.js"></script>

<?php show_postmain(); ?>
