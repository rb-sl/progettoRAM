<?php 
// Initial page of the statistical section; shows some general statistics
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
chk_access(3);
connect();
show_premain("Statistica");
?>

<h2>Statistiche globali</h2>

<h3>Studenti nel sistema:
<?php
$count_st = prepare_stmt("SELECT COUNT(*) AS n FROM STUDENTI");
$ret_s = execute_stmt($count_st);
$count_s = $ret_s->fetch_assoc();

echo $count_s['n'];
$count_st->close();
?>
</h3>

<h3>Numero totale di prove: 
<?php
$count_st = prepare_stmt("SELECT COUNT(*) AS n FROM PROVE");
$ret_r = execute_stmt($count_st);
$count_r = $ret_r->fetch_assoc();

echo $count_r['n'];
$count_st->close();
?>
</h3>

<!-- Plot div  -->
<div id="cnv">
</div>

<h2>Statistiche per test</h2>

<table class="table table-striped">
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
	<a href="./corr.php" class="btn btn-primary btnmenu">Correlazione campionaria</a><br>
	<a class="btn btn-primary btnmenu" disabled title="Prossimamente">ANOVA</a><br>
	<a class="btn btn-primary btnmenu" disabled title="Prossimamente">Test di Tukey</a>
</div>

<script src="statistics.js"></script>
<script>
<?php
// Number of results divided by test
$vals = "";
$lbls = "";
$test_st = prepare_stmt("SELECT nometest, COUNT(*) AS n 
	FROM PROVE JOIN TEST ON fk_test=id_test
	GROUP BY id_test 
	ORDER BY n");
$ret = execute_stmt($test_st);
$test_st->close();

while($row = $ret->fetch_assoc())
{
	$vals .= $row['n'].",\n";
	$lbls .= "'".$row['nometest']."',\n";
}

echo "var testDiv_vals = [
	$vals
];
var testDiv_lbls = [
	$lbls
];";

// Number of results divided by students' gender
$vals = "";
$lbls = "";
$stud_st = prepare_stmt("SELECT sesso, COUNT(*) AS n FROM PROVE 
	JOIN ISTANZE ON fk_ist=id_ist
	JOIN STUDENTI ON fk_stud=id_stud 
	GROUP BY sesso");
$ret = execute_stmt($stud_st);
$stud_st->close();

while($row = $ret->fetch_assoc())
{
	$vals .= $row['n'].",\n";
	$lbls .= "'".$row['sesso']."',\n";
}

echo "var studDiv_vals = [
	$vals
];
var studDiv_lbls = [
	$lbls
];";

// Number of results divided by class
$vals = "";
$lbls = "";
$class_st = prepare_stmt("SELECT classe, COUNT(*) AS n FROM PROVE
	JOIN ISTANZE ON fk_ist=id_ist
	JOIN CLASSI ON fk_cl=id_cl 
	GROUP BY classe 
	ORDER BY classe ASC");
$ret = execute_stmt($class_st);
$class_st->close();

while($row = $ret->fetch_assoc())
{
	$vals .= $row['n'].",\n";
	$lbls .= "'".$row['classe']."',\n";
}

echo "var classDiv_vals = [
	$vals
];
var classDiv_lbls = [
	$lbls
];";

// Number of results divided by year
$vals = "";
$lbls = "";
$year_st = prepare_stmt("SELECT anno, COUNT(*) AS n FROM PROVE
	JOIN ISTANZE ON fk_ist=id_ist
	JOIN CLASSI ON fk_cl=id_cl 
	GROUP BY anno 
	ORDER BY anno ASC");
$ret = execute_stmt($year_st);
$year_st->close();

while($row = $ret->fetch_assoc())
{
	$vals .= $row['n'].",\n";
	$lbls .= "'".$row['anno']."/".($row['anno']+1)."',\n";
}

echo "var yearDiv_vals = [
	$vals
];
var yearDiv_lbls = [
	$lbls
];";
?>

plotMiscStats();
</script>

<?php show_postmain(); ?>
