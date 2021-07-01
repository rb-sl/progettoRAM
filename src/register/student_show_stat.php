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

// Page to show statistical data about a student
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
include $_SERVER['DOCUMENT_ROOT']."/libraries/lib_stat.php";
include $_SERVER['DOCUMENT_ROOT']."/libraries/lib_reg.php";
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
	set_alert("Permessi insufficienti per visualizzare le informazioni");
	header("Location: /register/register.php");
	exit;
}

$lastname = htmlentities($stud['lastname']);
$firstname = htmlentities($stud['firstname']);
$gender = $stud['gender'];

show_premain("Dati di $lastname $firstname ($gender)", true);

// The page opens on the percentile option, so the relative colors are loaded
$color = get_color_prc();
?>

<h2>Elaborazione dati di <span id="student"><?=$lastname?> <?=$firstname?> (<?=$gender?>)</span></h2>

<div>
	<a href="student_show.php?id=<?=$_GET['id']?>" class="btn btn-primary marginunder">Mostra valori</a> 
	<h3>
		Visualizzazione dati:
		<select id="vis" class="form-control trigger">
			<option value="prc">Valori percentili</option>
			<option value="std">Valori standard</option>
   	 		<option value="gr">Voti</option>
		</select>
	</h3>
</div>

<div class="tdiv">
	<div class="inner">
		<table class="table table-light table-striped">
	  		<tr id="thr" class="dat">
				<td class="topleft leftfix topfix">
					<button type="button" id="btnstat" class="btn btn-secondary btnmenu overpad">Medie e Mediane</button>
					<br>
					<button type="button" id="btncol" class="btn btn-secondary btnmenu overpad">Colori</button>
				</td>
<?php
$ret = get_test($_GET['id'], true);

$test['row'] = "";
$test['id'] = [];
while($row = $ret->fetch_assoc())
{
	$test['id'][] = $row['test_id'];
	$test['positive_values'][$row['test_id']] = $row['positive_values'];
	$test['name'][$row['test_id']] = $row['test_name'];

	$test['row'] .= "<td id='test".$row['test_id']."' class='col topfix'>".htmlentities($row['test_name'])."</td>";
}

// Constuction of the table's body with percentile values
$rstud = get_perc($_GET['id'], null, true);

$am = get_avgmed($_GET['id'], $rstud['val'], true, true);

$rowavg = "";
foreach($am['avg'] as $idt => $avg)
	$rowavg .= "<td id='at$idt' class='jcol jtavg' vcolor='#".$avg['color']."'>".$avg['val']."</td>";

$rowmed = "";
foreach($am['med'] as $idt => $med)
	$rowmed .= "<td id='mt$idt' class='jcol jtmed borderunder' vcolor='#".$med['color']."'>".$med['val']."</td>";

echo $test['row']."
	<td class='col topfix r_stat evenrow jQhidden'>Media totale</td>
	<td id='tavg' class='col topfix r_stat jcol evenrow jQhidden' vcolor='#".(isset($am['tavg']['color']) ? $am['tavg']['color'] : "")."'>"
		.(isset($am['tavg']['val']) ? number_format($am['tavg']['val'], 5) : "-")."</td>
</tr>
<tr class='dat r_stat jQhidden'>
	<td class='leftfix evenrow'>Medie</td>$rowavg
	<td rowspan='2' id='med1' class='col r_stat oddrow jQhidden'>Medie<br>classi</td>
	<td rowspan='2' id='med2' class='col r_stat oddrow jQhidden'>Mediane<br>classi</td>
</tr>
<tr class='dat r_stat jQhidden'>
	<td class='leftfix oddrow'>Mediane</td>$rowmed
</tr>";

$options = "";
foreach($rclass as $idcl => $class)
{
	echo $class['clrow'];
	$options = "<option value='$idcl'>".$class['name']."</option>".$options;

	foreach($test['id'] as $idt)
	{
		echo "<td id='$idcl"."_$idt' class='jdat jcol r_$idcl c_$idt dat$idcl'";
		if(isset($rstud['val'][$idcl][$idt]))
		{
			if(isset($rstud['date'][$idcl][$idt]) and $rstud['date'][$idcl][$idt] != "0000-00-00")
				echo " title='".$rstud['date'][$idcl][$idt]."'";

			echo " vcolor='#".$rstud['color'][$idcl][$idt]."'>"
				.$rstud['val'][$idcl][$idt]."</td>";
		}
		else 
			echo ">-</td>";
	}

	if(isset($am['savg'][$idcl]))
	{
		$avgcolor = $am['savg'][$idcl]['color'];
		$avg = $am['savg'][$idcl]['val'];
		$medcolor = $am['smed'][$idcl]['color'];
		$med = $am['smed'][$idcl]['val'];
	}
	else
	{
		$avgcolor = "";
		$avg = "-";
		$medcolor = "";
		$med = "-";
	}

	echo "<td id='a_$idcl' class='r_$idcl jsavg jcol borderleft r_stat jQhidden' vcolor='#$avgcolor'>$avg</td>
		  <td id='m_$idcl' class='r_$idcl jsmed jcol r_stat jQhidden' vcolor='#$medcolor'>$med</td>
		</tr>\n";
}
?>
		</table>
	</div>
</div>

<h3>
	Grafico della classe
	<select id="class" class="form-control trigger">
		<?=$options?>
	</select>
</h3>

<div id="cnv">
</div>

<script>
	var id = <?=$_GET['id']?>;
	var forstud = true;
</script>
<script src="/register/js/common_register.js"></script>
<script src="/register/js/student_show_stat.js"></script>
<script src="/register/js/show_stat.js"></script>

<?php show_postmain(); ?>
