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
$test_st = prepare_stmt("SELECT test_id, test_name, test_fk AS favourite FROM test
	LEFT JOIN favourites ON test_fk=test_id
	AND (user_fk=? OR user_fk IS NULL)
	ORDER BY test_name");
$test_st->bind_param("i", $_SESSION['id']);
$rettest = execute_stmt($test_st);
$test_st->close();

while($rowt = $rettest->fetch_assoc())
{
	if(!$rowt['favourite'])
		$class = " class='inactivetext'";
	else
		$class = "";

	echo "<tr><td><a href='test_show.php?id=".$rowt['test_id']."'$class>".$rowt['test_name']."</a></td></tr>";
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

	$pr_st = prepare_stmt("SELECT * FROM user ORDER BY username");
	$r = execute_stmt($pr_st);
	
  	while($p = $r->fetch_assoc())
		echo "<option value='".$p['user_id']."'"
			.($p['user_id'] == $_SESSION['id'] ? " selected" : "")
			.">".$p['username']."</option>";
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
$grade_st = prepare_stmt("SELECT * FROM grading JOIN grade ON grade_fk=grade_id WHERE user_fk=? ORDER BY grade ASC");
$grade_st->bind_param("i", $_SESSION['id']);

$prev = 0;   
$tracelist = "";
$traces = "";    

$ret = execute_stmt($grade_st);
while($row = $ret->fetch_assoc())
{
	$v10 = $row['grade'] * 10;

	echo "<tr>
			<td id='x_$v10' style='background-color:#".$row['color']."'>".$row['grade']."</td>
			<td><input type='number' min='0' id='p_$v10' class='range halfwidth textright' 
				value='".($row['percentile'] - $prev)."' name='percentile[".$row['grade_id']."]'>%</td> 
			<td id='i$v10'>$prev</td>
			<td>&rarr;</td>
			<td id='f$v10'>".$row['percentile']."</td>
		</tr>";

	$prev = $row['percentile'];
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
