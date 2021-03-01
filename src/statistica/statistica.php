<?php 
// Pagina iniziale della sezione di statistica. Permette di
// - Visualizzare alcuni dati generali del sistema (numero studenti, prove...)
// - Raggiungere le pagine relative alle statistiche per ogni test
include $_SERVER['DOCUMENT_ROOT']."/librerie/general.php";
chk_access(3);
connect();
show_premain("Statistica");
?>

<h2>Statistiche globali</h2>
<h3>Studenti nel sistema:
<?php
$ret=query("SELECT COUNT(*) AS n FROM STUDENTI");
$row=$ret->fetch_assoc();
echo $row[n];
?>
</h3>

<h3>Numero totale di prove: 
<?php
$ret=query("SELECT COUNT(*) AS n FROM PROVE");
$row=$ret->fetch_assoc();
echo $row[n];
?>
</h3>

<!-- div per grafici  -->
<div id="cnv">
</div>

<h2>Statistiche per test</h2>
<table class="table table-striped">
<?php
$ret=query("SELECT id_test,nometest,COUNT(*) AS n FROM TEST,PROVE WHERE fk_test=id_test GROUP BY nometest HAVING(COUNT(*)>0) ORDER BY nometest");
while($row=$ret->fetch_assoc())
	echo "<tr><td><a href='stat_test.php?id=".$row['id_test']."' class='btn'>".$row['nometest']."</a></td></tr>";
?>
</table>

<h2>Statistiche avanzate</h2>
<div>
	<a href="./corr.php" class="btn btn-primary btnmenu">Correlazione campionaria</a><br>
	<a class="btn btn-primary btnmenu" disabled title="Prossimamente">ANOVA</a><br>
	<a class="btn btn-primary btnmenu" disabled title="Prossimamente">Test di Tukey</a>
</div>

<?php
$ret=query("SELECT nometest,COUNT(*) AS n FROM PROVE,TEST WHERE fk_test=id_test GROUP BY id_test ORDER BY n");
while($row=$ret->fetch_assoc())
{
	$valp.=$row['n'].",\n";
	$lblp.="'".$row['nometest']."',\n";
}
    
$ret=query("SELECT sesso,COUNT(*) AS n FROM PROVE,ISTANZE,STUDENTI WHERE fk_ist=id_ist AND fk_stud=id_stud GROUP BY sesso");
while($row=$ret->fetch_assoc())
{
	$vals.=$row['n'].",\n";
	$lbls.="'".$row['sesso']."',\n";
}

$ret=query("SELECT classe,COUNT(*) AS n FROM PROVE,ISTANZE,CLASSI WHERE fk_ist=id_ist AND fk_cl=id_cl GROUP BY classe ORDER BY classe ASC");
while($row=$ret->fetch_assoc())
{
	$valc.=$row['n'].",\n";
	$lblc.="'".$row['classe']."',\n";
}

$ret=query("SELECT anno,COUNT(*) AS n FROM PROVE,ISTANZE,CLASSI WHERE fk_ist=id_ist AND fk_cl=id_cl GROUP BY anno ORDER BY anno ASC");
while($row=$ret->fetch_assoc())
{
	$vala.=$row['n'].",\n";
	$lbla.="'".$row['anno']."/".($row['anno']+1)."',\n";
}   
?>

<script>
var data =[{
	values: [
<?=$valp?>
	],
	labels: [
<?=$lblp?>
    ],
	type: "pie",
	name: "Per test",
	sort: false,
	direction: "clockwise",
	domain: {
    	row: 0,
    	column: 0
  	},
	textinfo: "none"
},{
	values: [
<?=$vals?>
    ],
	labels: [
<?=$lbls?>    
    ],
  	type: "pie",
  	name: "Per sesso",
  	domain: {
    	row: 0,
    	column: 1
  	},
  	textinfo: "none",
	sort: false,
	direction: "clockwise"
},{
	values: [
<?=$valc?>
    ],
	labels: [
<?=$lblc?>    
    ],
  	type: "pie",
  	name: "Per classe",
  	domain: {
    	row: 1,
    	column: 0
  	},
  	textinfo: "none",
	sort: false,
	direction: "clockwise",
},{
	values: [
<?=$vala?>
    ],
	labels: [
<?=$lbla?>    
    ],
  	type: "pie",
  	name: "Per anno",
  	domain: {
    	row: 1,
    	column: 1
  	},
  	textinfo: "none",
	sort: false,
	direction: "clockwise"
}]

var layout = {
	height: "700",
	showlegend: false,
	title: "Suddivisione delle prove",
	grid: {rows: 2, columns: 2}
}

Plotly.newPlot("cnv", data, layout, {responsive: true});
</script>

<?php show_postmain(); ?>