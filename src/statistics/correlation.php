<?php
// Frontend page to display ststistical correlation between tests
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
include $_SERVER['DOCUMENT_ROOT']."/libraries/lib_stat.php";
chk_access(3);
connect();
show_premain("Correlazione dei test", true, true);

// Gets tests with at least one result
$threshold = CORRELATION_TRESH;
$test_st = prepare_stmt("SELECT id_test, nometest, pos FROM TEST 
	WHERE id_test IN (SELECT fk_test FROM PROVE GROUP BY fk_test HAVING COUNT(*) > ?) 
	ORDER BY nometest");
$test_st->bind_param("i", $threshold);
$ret = execute_stmt($test_st);
$test_st->close();

$testlist = "-1";
while($row = $ret->fetch_assoc())
{
	$test[$row['id_test']] = $row['nometest'];
	$positive[$row['id_test']] = $row['pos'];
	$stats[$row['id_test']] = get_stats($row['id_test']);
	$testlist .= ", ".$row['id_test'];
}
?>

<h2>Statistiche di correlazione campionaria dei test</h2>

<h3>Matrice di correlazione campionaria</h3>
<div class="tdiv">
  	<div class="inner">
		<table class="table table-striped">
			<tr id="thr">
				<th class="topleft leftfix topfix">
					<button id="btncol" class="btn wtot overpad">Colori</button>
				</th>

<?php
$start = microtime(true);
$i = 0; 
open_rvals_stmt();

$splom_st = prepare_stmt("SELECT nometest, fk_ist, valore, sesso 
	FROM PROVE JOIN TEST ON fk_test=id_test 
	JOIN ISTANZE ON fk_ist=id_ist
	JOIN STUDENTI ON fk_stud=id_stud
	WHERE fk_test IN ($testlist) ORDER BY fk_ist, nometest");
$splomret = execute_stmt($splom_st);
$previnst = -1;
$instances = [];
while($splomrow = $splomret->fetch_assoc())
{
	// build a table such as
	// id_ist | id_test | val
	// with empty val entries if needed
	$splom[$splomrow['nometest']][$splomrow['fk_ist']] = $splomrow['valore'];

	if($previnst != $splomrow['fk_ist'])
	{
		$previnst = $splomrow['fk_ist'];
		$instances[$splomrow['fk_ist']] = $splomrow['sesso'];
	}
}

foreach($test as $idcol => $namecol)
{
	echo "<th id='c$idcol' pos='".$positive[$idcol]."' class='col topfix'>$namecol</th>\n";

	if($i % 2 == 0)
		$color = "evenrow";
	else
		$color = "oddrow";
	$tab[$idcol]['st'] = "<tr><th id='r$idcol' class='leftfix dat2 col $color'>$namecol</th>";
	
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
        	$tab[$idrow][$idcol] = "<td id='m$idrow"."_$idcol' class='dat2 r_$idcol $cl gr' title='n=".$r['n']."'>".$r['r']."</td>";
        	$tab[$idcol][$idrow] = "<td id='m$idcol"."_$idrow' class='dat2 r_$idrow $cl gr' title='n=".$r['n']."'>".$r['r']."</td>";
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
echo microtime(true) - $start;
?>	
		</table>
	</div>
</div>

<div id="cnv"></div>

<div id="splom"></div>

<script>
var splomDimensions = [
<?php
foreach($splom as $test => $list)
{	
	$vals = "";
	foreach($instances as $i => $gnd)
	{
		if(isset($list[$i]))
			$vals .= $list[$i].", ";
		else
			$vals .= ", ";
	}

	echo "{
		label: '".(str_replace(" ", "<br>", $test))."',
		values: [$vals]
	},";
}		
?>	
];
var splomText = []
</script>
<script src="js/correlation.js"></script>

<?php show_postmain(); ?>
