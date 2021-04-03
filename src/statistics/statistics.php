<?php 
// Initial page of the statistical section; shows some general statistics
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
include $_SERVER['DOCUMENT_ROOT']."/libraries/lib_stat.php";
chk_access(3);
connect();
show_premain("Statistica", true);

$stats = get_general_stats();
?>

<h2>Statistiche globali</h2>

<h3>
	Studenti nel sistema: 
	<span id="stud_tot"><?=$stats['stud_tot']?></span>
</h3>

<h4 id="stud_sel">
	Selezione corrente: <span id="stud_num"><?=$stats['stud_tot']?></span> 
	(<span id="stud_perc">100</span>%)
</h4>

<h3>
	Numero totale di prove: 
	<span id="res_tot"><?=$stats['res_tot']?></span>
</h3>

<h4 id="res_sel">
	Selezione corrente: <span id="res_num"><?=$stats['res_tot']?></span> 
	(<span id="res_perc">100</span>%)
</h4>

<!-- Plot div  -->
<div id="cnv">
</div>

<h2>Statistiche per test</h2>

<table class="table table-light table-striped">
<?php
$test_st = prepare_stmt("SELECT id_test, nometest, COUNT(*) AS n 
	FROM TEST JOIN PROVE ON fk_test=id_test 
	GROUP BY nometest 
	HAVING(COUNT(*)>0) 
	ORDER BY nometest");
$test_r = execute_stmt($test_st);
$test_st->close();

while($row = $test_r->fetch_assoc())
	echo "<tr><td><a href='test_stats.php?id=".$row['id_test']."'>".$row['nometest']."</a></td></tr>";
?>
</table>

<h2>Statistiche avanzate</h2>
<div>
	<a href="correlation.php" class="btn btn-primary marginunder">Correlazione campionaria</a><br>
	<a class="btn btn-primary btnmenu" disabled title="Prossimamente">ANOVA</a><br>
	<a class="btn btn-primary btnmenu" disabled title="Prossimamente">Test di Tukey</a>
</div>

<script src="/statistics/js/statistics.js"></script>
<script>
<?php
$test = misc_graph();
foreach($test as $type => $data)
	echo "var ".$type."Div_vals = ".json_encode($data['vals']).";
		var ".$type."Div_lbls = ".json_encode($data['lbls']).";";
?>

plotMiscStats();
</script>

<?php show_postmain(); ?>
