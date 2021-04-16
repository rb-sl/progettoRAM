<?php
// Main page for tests, allows to show users the tests and update grades
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
chk_access(RESEARCH);
connect();
show_premain("Test e valutazioni");
?>

<h2>Visualizzazione Test</h2>

<?php
if(chk_auth(PROFESSOR))
{
	echo "<div class='marginunder'>";
	// An administrator or a professor with grants can add a new test
	if(chk_auth(PROFESSOR_GRANTS))
		echo "<a href='./test_add.php' class='btn btn-primary'>Aggiungi nuovo</a> ";
	echo "<a href='./favourites_modify.php' class='btn btn-warning'>Modifica preferiti</a>
		</div>";
}
?>

<table class="table table-light table-striped marginunder">
<?php
$test_st = prepare_stmt("SELECT id_test, nometest, fk_test AS favourite FROM TEST
	LEFT JOIN PROF_TEST ON fk_test=id_test
	AND (fk_prof=? OR fk_prof IS NULL)
	ORDER BY nometest");
$test_st->bind_param("i", $_SESSION['id']);
$rettest = execute_stmt($test_st);
$test_st->close();

while($rowt = $rettest->fetch_assoc())
{
	if(!$rowt['favourite'])
		$class = " class='inactivetext'";
	else
		$class = "";

	echo "<tr><td><a href='test_show.php?id=".$rowt['id_test']."'$class>".$rowt['nometest']."</a></td></tr>";
}
?>
</table>

<?php
// Ends if the user is not a professor
if(!chk_auth(PROFESSOR))
{
	show_postmain();
	exit;
}
?>

<form id="grades" action="grades_update.php" method="POST">
	<h2>Tabella di valutazione
<?php
// An administrator can see each user's grades
if(chk_auth(ADMINISTRATOR))
{
	echo "di <select class='form-control' id='slp' name='slp'>";

	$pr_st = prepare_stmt("SELECT * FROM PROFESSORI ORDER BY user");
	$r = execute_stmt($pr_st);
	
  	while($p = $r->fetch_assoc())
    	echo "<option value='".$p['id_prof']."'"
			.($p['id_prof'] == $_SESSION['id'] ? " selected" : "")
			.">".$p['user']."</option>";
    echo "</select>";
}
?></h2>

	<!-- plotly graph -->
	<div id="cnv">
	</div>

	<table class="table table-light marginunder">
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
			<td id='x_$v10' style='background-color:#".$row['color']."'>".$row['voto']."</td>
    		<td><input type='number' min='0' id='p_$v10' class='range halfwidth textright' 
				value='".($row['perc'] - $prev)."' name='perc[".$row['id_voto']."]'>%</td> 
            <td id='i$v10'>$prev</td>
			<td>&rarr;</td>
			<td id='f$v10'>".$row['perc']."</td>
        </tr>";

	$prev = $row['perc'];
}	
?>
		<tr>
        	<th class="borderover">Totale:</th>
        	<td class="err sum borderover"><?=$prev?></td>
        	<td class="err borderover">0</td><td class="err borderover">&rarr;</td><td class="err borderover sum"><?=$prev?></td>
    	</tr>
    </table>
	<input type="submit" id="aggv" class="btn btn-warning" value="Aggiorna tabella voti">
</form>

<script src="/test/js/test.js"></script>
<script>
	plotGrades();
</script>

<?php show_postmain(); ?>
