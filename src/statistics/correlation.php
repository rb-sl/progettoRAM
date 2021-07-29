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

// Frontend page to display ststistical correlation between tests
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
include $_SERVER['DOCUMENT_ROOT']."/libraries/lib_stat.php";
chk_access(RESEARCH);
connect();
show_premain("Correlazione dei test", true, true);

$testinfo = get_test_correlation();
$test = $testinfo['names'];
$positive = $testinfo['positive'];
$stats = $testinfo['statistics'];
$testlist = $testinfo['list'];
?>

<h2>Statistiche di correlazione campionaria dei test</h2>

<h3>Matrice di correlazione campionaria</h3>
<div class="tdiv marginunder">
  	<div class="inner">
		<table class="table table-light table-striped">
			<tr id="thr">
				<th class="topleft leftfix topfix">
					<button id="btncol" class="btn overpad btnmenu btn-secondary">Colori</button>
				</th>
<?php
open_rvals_stmt();
$i = 0;
$tab = [];
foreach($test as $idcol => $namecol)
{
	echo "<th id='test$idcol' positive_values='".$positive[$idcol]."' class='col topfix'>$namecol</th>\n";

	if($i % 2 == 0)
		$color = "evenrow";
	else
		$color = "oddrow";
	$tab[$idcol]['st'] = "<tr><th id='r$idcol' class='leftfix squaredat col $color'>$namecol</th>";
	
	foreach($test as $idrow => $namerow)
		// Due to the matrix's simmetry, only the lower half is calculated,
		// taking advantage of the lexicographical order in the query
		if(strnatcmp($namerow, $namecol) <= 0)
		{
			$r = calc_r($idcol, $stats[$idcol], $idrow, $stats[$idrow]);
			
			if($r['r'] != "-")
				$cl = "point clcbl";
			else
				$cl = "";
		
			// The matrix is built simmetrically
			$tab[$idrow][$idcol] = "<td id='m$idrow"."_$idcol' class='squaredat r_$idcol $cl gr' vcolor='".$r['color']."' title='n=".$r['n']."'>".$r['r']."</td>";
			$tab[$idcol][$idrow] = "<td id='m$idcol"."_$idrow' class='squaredat r_$idrow $cl gr' vcolor='".$r['color']."' title='n=".$r['n']."'>".$r['r']."</td>";
		}

	$i++;
}
$rval_st->close();
echo "</tr>";

// Output of the table rows
foreach($tab as $id => $row)
{
	foreach($row as $x => $cell)
		echo $cell;
	echo "</tr>";
}
?>	
		</table>
	</div>
</div>

<div id="over" class="overlay containerflex jQhidden">
	<div id="cnv" class="overcanvas"></div>
</div>

<div class="containerflex">
	<div id="splom" class="inner"></div>
</div>

<script src="/statistics/js/correlation.js"></script>
<script>
	var splomWH = <?=max(500, $i * 130)?>;
	getData();
</script>

<?php show_postmain(); ?>
