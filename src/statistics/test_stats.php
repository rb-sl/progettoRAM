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

// Front end page to display a test's statistics
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
include $_SERVER['DOCUMENT_ROOT']."/libraries/lib_stat.php";
chk_access(RESEARCH);
connect();

$test_st = prepare_stmt("SELECT * FROM test JOIN unit ON unit_fk=unit_id WHERE test_id=?");
$test_st->bind_param("i", $_GET['id']);
$ret = execute_stmt($test_st);
$test_st->close();

// If no test is returned the loading is blocked and
// an error is shown
if($ret->num_rows == 0)
{
	set_alert("Id test non valido");
	header("Location: /statistics/statistics.php");
	exit;
}

$test = $ret->fetch_assoc();
$testname = htmlentities($test['test_name']);
$symbol = htmlentities($test['symbol']);

show_premain("Statistiche $testname", true);

$data = get_stats($_GET['id']);
$records = get_records($_GET['id']);
$graph = graph_vals($_GET['id']);
?>

<h2>Statistiche <span id="test_name"><?=$testname?></span></h2>

<table class="table table-light table-striped marginunder">
   	<tr><td>Numero totale di prove: <span id="n"><?=$data['n']?></span></td>
   	<tr><td>Media: <span id="avg"><?=number_format($data['avg'], 2)?></span> <?=$symbol?></td></tr>
	<tr><td>Mediana: <span id="med"><?=number_format($data['med'], 2)?></span> <?=$symbol?></td></tr>
   	<tr><td>Deviazione Standard: <span id="std"><?=number_format($data['std'], 2)?></span> <?=$symbol?></td></tr>	
</table>

<h3 class="nomargin">
	Record positivo: 
	<span id="best"><?=$records['best']?></span> 
	<?=$symbol?>
</h3>

<?=$records['list']?>

<h3 class="section">
	Record negativo: 
	<span id="worst"><?=$records['worst']?></span>
	<?=$symbol?>
</h3>

<h3>
	Grafico: 
	<select id="graph" class="form-control trigger">
		<option value="val">Valori</option>
		<option value="trend">Andamento statistiche</option>
		<option value="prc">Valori percentili</option>
		<option value="box">Box plot</option>
		<option value="cbox">Box plot (Classi)</option>
		<option value="sbox">Box plot (Sesso)</option>
	</select>
</h3>

<!-- Plot -->
<div id="cnv">
</div>

<script src="/statistics/js/test_stats.js"></script>
<script>
var id  = <?=$_GET['id']?>;
var vals = <?=json_encode($graph['vals']);?>;
   			
draw_graph_val(vals);  
</script>

<?php show_postmain(); ?>
