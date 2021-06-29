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

// Page to show statistical data about a class
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
include $_SERVER['DOCUMENT_ROOT']."/libraries/lib_stat.php";
include $_SERVER['DOCUMENT_ROOT']."/libraries/lib_reg.php";
chk_access(PROFESSOR);
connect();

$cl = get_class_info($_GET['id']);
chk_prof($cl['user_fk']);

show_premain("Registro ".$cl['class'].htmlentities($cl['section'])." ".$cl['class_year']."/".($cl['class_year'] + 1), true);

// The page opens on the percentile option, so the relative colors are loaded
$color = get_color_prc();
?>

<h2>Registro della classe <?=$cl['class'].htmlentities($cl['section'])?> - Anno <?=$cl['class_year']."/".($cl['class_year'] + 1)?></h2>

<div>
	<a href="class_show.php?id=<?=$_GET['id']?>" class="btn btn-primary marginunder">Registro della classe</a>
	<h3>
		Visualizzazione classe:
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
// Header construction
$ret = get_test($_GET['id']);

$test['row'] = "";
$test['id'] = [];
while($row = $ret->fetch_assoc())
{
	$test['id'][] = $row['test_id'];
	$test['positive_values'][$row['test_id']] = $row['positive_values'];

	$test['row'] .= "<td id='c".$row['test_id']."' class='col topfix'>".htmlentities($row['test_name'])."</td>";
}

// Constuction of the table's body with percentile values
$rstud = get_perc($_GET['id']);

// Calculates and constructs the rows and columns related to average and median for both tests and students 
$am = get_avgmed($_GET['id'], $rstud['val'], true);

$rowavg = "";
foreach($am['avg'] as $idt => $avg)
	$rowavg .= "<td id='at$idt' class='jcol jtavg' vcolor='#".$avg['color']."'>".$avg['val']."</td>";

$rowmed = "";
foreach($am['med'] as $idt => $med)
	$rowmed .= "<td id='mt$idt' class='jcol jtmed borderunder' vcolor='#".$med['color']."'>".$med['val']."</td>";

// Prints the table header and information on average and median values
echo $test['row']."
		<td class='col topfix r_stat evenrow jQhidden'>Media totale</td>
		<td id='tavg' class='col topfix r_stat jcol evenrow jQhidden' vcolor='#"
			.(isset($am['tavg']['color']) ? $am['tavg']['color'] : "")."'>"
			.(isset($am['tavg']['val']) ? number_format($am['tavg']['val'], 5) : "-")."</td>
	</tr>
	<tr class='dat r_stat jQhidden'>
		<td class='leftfix evenrow'>Medie</td>$rowavg
		<td rowspan='2' id='med1' class='col r_stat oddrow jQhidden'>Medie<br>studenti</td>
		<td rowspan='2' id='med2' class='col r_stat oddrow jQhidden'>Mediane<br>studenti</td>
	</tr>
	<tr class='dat r_stat jQhidden'>
		<td class='leftfix oddrow'>Mediane</td>$rowmed
	</tr>";

$cstud = col_stud();
foreach($cstud as $idist => $stud)
{
	echo $stud['strow'];
	
	foreach($test['id'] as $idt)
	{
		echo "<td id='$idist"."_$idt' class='jdat jcol r_$idist c_$idt'";
		if(isset($rstud['val'][$idist][$idt]))
		{
			if(isset($rstud['date'][$idist][$idt]) and $rstud['date'][$idist][$idt] != "0000-00-00")
				echo " title='".$rstud['date'][$idist][$idt]."'";

			echo " vcolor='#".$rstud['color'][$idist][$idt]."'>"
				.$rstud['val'][$idist][$idt]."</td>";
		}
		else 
			echo ">-</td>";
	}

	if(isset($am['savg'][$idist]))
	{
		$avgcolor = $am['savg'][$idist]['color'];
		$avg = $am['savg'][$idist]['val'];
		$medcolor = $am['smed'][$idist]['color'];
		$med = $am['smed'][$idist]['val'];
	}
	else
	{
		$avgcolor = "";
		$avg = "-";
		$medcolor = "";
		$med = "-";
	}

	echo "<td id='a_$idist' class='r_$idist jsavg jcol borderleft r_stat jQhidden' vcolor='#$avgcolor'>$avg</td>
		  <td id='m_$idist' class='r_$idist jsmed jcol r_stat jQhidden' vcolor='#$medcolor'>$med</td>
		</tr>\n";
}
?>
		</table>
	</div>
</div>

<script>
	var id = <?=$_GET['id']?>;
	var forstud = false;
</script>
<script src="/register/js/common_register.js"></script>
<script src="/register/js/show_stat.js"></script>

<?php show_postmain(); ?>
