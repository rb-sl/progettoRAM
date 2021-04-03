<?php
// Frontend page to display ststistical correlation between tests
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
include $_SERVER['DOCUMENT_ROOT']."/libraries/lib_stat.php";
chk_access(3);
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
foreach($test as $idcol => $namecol)
{
	echo "<th id='test$idcol' pos='".$positive[$idcol]."' class='col topfix'>$namecol</th>\n";

	if($i % 2 == 0)
		$color = "evenrow";
	else
		$color = "oddrow";
	$tab[$idcol]['st'] = "<tr><th id='r$idcol' class='leftfix squaredat col $color'>$namecol</th>";
	
	foreach($test as $idrow => $namerow)
		// Due to the matrix's simmetry, only the lower half is calculated,
		// taking advantage of the lexicographical order in the query
    	if($namerow <= $namecol)
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

<script>
var splomWH = <?=($i * 130)?>;

var testInfo =
<?php
$tests = test_graph($testlist);
echo json_encode($tests);
?>;
</script>
<script src="/statistics/js/correlation.js"></script>

<?php show_postmain(); ?>
