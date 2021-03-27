<?php
// Main page for tests, allows to show users the tests and update grades
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
chk_access(3);
connect();
show_premain("Test e valutazioni");
?>

<h2>Visualizzazione Test</h2>

<?php
// An administrator or a professor with grants can add a new test
if($_SESSION['priv'] <= 1)
	echo "<div><a href='./test_add.php' class='btn btn-warning btnmenu'>Aggiungi nuovo</a></div>";
?>

<table class="table table-striped marginunder">
	<tr><thead><th>Nome test</th></thead></tr>
<?php
$test_st = prepare_stmt("SELECT * FROM TEST ORDER BY nometest");
$rettest = execute_stmt($test_st);
$test_st->close();

while($rowt = $rettest->fetch_assoc())
	echo "<tr><td><a href='test_show.php?id=".$rowt['id_test']."'>".$rowt['nometest']."</a></td>";
?>
</table>

<?php
// Ends if the user is not a professor
if($_SESSION['priv'] > 2)
{
	show_postmain();
	exit;
}
?>

<form id="grades" action="grades_update.php" method="POST">
	<h2>Tabella di valutazione
<?php
// An administrator can see each user's grades
if($_SESSION['priv'] == 0)
{
	echo "di <select class='form-control' id='slp' name='slp'>";

	$pr_st = prepare_stmt("SELECT * FROM PROFESSORI ORDER BY user");
	$r = execute_stmt($pr_st);
	
  	while($p = $r->fetch_assoc())
    	echo "<option value='".$p['id_prof']."'>".$p['user']."</option>";
    echo "</select>";
}
?></h2>

	<!-- plotly graph -->
	<div id="cnv">
	</div>

	<table class="table table-striped marginunder">
    	<tr>
        	<th>Voto</th>
           	<th>Percentuale assegnata</th>
           	<th colspan="3" class="thirdwidth">Range percentili</th>
       	</tr>
<?php
// Prints the table for grades
$grade_st = prepare_stmt("SELECT * FROM VALUTAZIONI JOIN VOTI ON fk_voto=id_voto WHERE fk_prof=? ORDER BY voto ASC");
$grade_st->bind_param("i", $_SESSION['id']);

$prev = 0;   
$tracelist = "";
$traces = "";    

$ret = execute_stmt($grade_st);
while($row = $ret->fetch_assoc())
{
	$v10 = $row['voto'] * 10;

	echo "<tr>
			<td style='background-color:#".$row['color']."'>".$row['voto']."</td>
    		<td><input type='number' min='0' id='r$v10' class='range halfwidth textright' value='".($row['perc'] - $prev)."' name='perc[".$row['id_voto']."]'>%</td> 
            <td id='i$v10'>$prev</td>
			<td>&rarr;</td>
			<td id='f$v10'>".$row['perc']."</td>
        </tr>";

	// Plotly graph components
	$tracelist .= "trace$v10, ";
	$traces .= "var trace$v10 = {
  		x: [".($row['perc'] - $prev)."],
  		type: 'bar',
   		name: '".$row['voto']."',
   		text: '".$row['voto']."',
  		textposition: 'auto',
   		hoverinfo: 'none',
   		marker: {
    		color: '".$row['color']."',
    		line: {
      			color: '#000',
      			width: 1.5
    		}
  		}
	};\n";

	$prev = $row['perc'];
}	
?>
		<tr>
        	<th class="borderover">Totale:</th>
        	<td class="err sum borderover"><?=$prev?></td>
        	<td class="err borderover">0</td><td class="err borderover">&rarr;</td><td class="err borderover sum"><?=$prev?></td>
    	</tr>
    </table>
	<input type="submit" id="aggv" class="btn btn-warning btnmenu" value="Aggiorna tabella voti">
</form>

<script src="./test.js"></script>
<script>
	<?=$traces?>
	var data = [<?=$tracelist?>];
	var layout = { barmode: "stack", yaxis:{visible:false } };
	Plotly.newPlot("cnv", data, layout, {responsive: true});
</script>

<?php show_postmain(); ?>