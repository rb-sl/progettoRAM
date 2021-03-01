<?php
// Main page for tests, allows to show users the tests and update grades
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
chk_access(2);
connect();
show_premain("Test e valutazioni");
?>

<h2>Visualizzazione Test</h2>

<?php
if(isset($_SESSION['priv']) and $_SESSION['priv'] <= 1)
	echo "<div><a href='./add_test.php' class='btn btn-warning btnmenu'>Aggiungi nuovo</a></div>";
?>

<div class="scrollable">
	<table class="table table-striped">
		<tr><thead><th>Nome test</th></thead></tr>
<?php
$test_st = prepare_stmt("SELECT * FROM TEST ORDER BY nometest");
$rettest = execute_stmt($test_st);

while($rowt = $rettest->fetch_assoc())
	echo "<tr><td><a href='show_test.php?id=".$rowt['id_test']."'>".$rowt['nometest']."</a></td>";
$test_st->close();
?>
    </table>
</div>

<?php
// Ends if the user is not a professor
if($_SESSION['priv'] > 1)
{
	show_postmain();
	exit;
}
?>

<form id="voti" action="up_voti.php" method="POST">
	<h2 id="voti">Tabella di valutazione
<?php
// Se l'utente è un amministratore può visualizzare i voti di ogni utente
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

	<!-- div contenente il grafico plotly -->
	<div id="cnv">
	</div>

<?php

?>
	<table class="table table-striped marginunder">
    	<tr>
        	<th>Voto</th>
           	<th>Percentuale assegnata</th>
           	<th colspan="3" class="w30">Range percentili</th>
       	</tr>
<?php
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
    		<td><input type='number' min='0' id='r$v10' class='range w50 textright' value='".($row['perc'] - $prev)."' name='perc[".$row['id_voto']."]'>%</td> 
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
        	<th class="btop">Totale:</th>
        	<td class="err sum btop"><?=$prev?></td>
        	<td class="err btop">0</td><td class="err btop">&rarr;</td><td class="err btop sum"><?=$prev?></td>
    	</tr>
    </table>
	<input type="submit" id="aggv" class="btn btn-warning btnmenu" value="Aggiorna tabella voti">
</form>

<script src="./test.js"></script>
<script>
	<?=$traces?>
	var data = [<?=$tracelist?>];
	var layout = {barmode: "stack", yaxis:{visible:false }};
	Plotly.newPlot("cnv", data, layout, {responsive: true});
</script>

<?php show_postmain(); ?>