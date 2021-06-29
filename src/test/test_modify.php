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

// Frontend for test update
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
chk_access(PROFESSOR_GRANTS);
connect();

// If the test does not exist an error is shown to the user
$test_st = prepare_stmt("SELECT * FROM test WHERE test_id=?");
$test_st->bind_param("i", $_GET['id']);

$rettest = execute_stmt($test_st);
$test_st->close();

if($rettest->num_rows == 0)
{
	$_SESSION['alert'] = "Errore: Test inesistente";
	header("Location: /test/test.php");
	exit;
}
$test = $rettest->fetch_assoc();

show_premain("Modifica test");

// Getting test associated info
$type_st = prepare_stmt("SELECT * FROM datatype ORDER BY datatype_name");
$ttest = execute_stmt($type_st);
$type_st->close();

$class_st = prepare_stmt("SELECT * FROM testtype ORDER BY testtype_name");
$ctest = execute_stmt($class_st);
$class_st->close();

$unit_st = prepare_stmt("SELECT * FROM unit ORDER BY unit_name");
$retunit = execute_stmt($unit_st);
$unit_st->close();
?>

<form method="POST" action="./test_update.php?id=<?=$_GET['id']?>" class="marginunder">
	<h2>
		Modifica <input type="text" value="<?=htmlentities($test['test_name'])?>" name="testname" required> 
		<a href="./test_show.php?id=<?=$_GET['id']?>" class="btn btn-warning">Annulla</a> 
<?php
echo "<a href='./test_delete.php?id=".$_GET['id']."' ".confirm("Il test ".$test['test_name']." sarà eliminato")
	." class='btn btn-danger'>Elimina test</a>";
?>
	</h2>
	<table class="table table-light table-striped">
		<tr>
			<td>Tipo di test:</td>
			<td class="halfwidth">
				<select name="class" class="form-control" required>
<?php
while($row = $ctest->fetch_assoc())
{
	echo "<option value='".$row['testtype_id']."'";
	if($row['testtype_id'] == $test['testtype_fk'])
		echo " selected='selected'";
	echo ">".htmlentities($row['testtype_name'])."</option>";
}
?>
				</select>
			</td>
		</tr>
		<tr>
			<td>Unità di misura:</td>
			<td>
				<select name="unit" class="form-control" required>
<?php
while($row = $retunit->fetch_assoc())
{
	echo "<option value='".$row['unit_id']."'";
	if($row['unit_id'] == $test['unit_fk'])
		echo " selected='selected'";
	echo ">".htmlentities($row['unit_name'])."</option>";
}       

if($test['positive_values'] == GREATER)
{
	$g = " selected='selected'";
	$m = "";
}
else
{
	$g = "";
	$m = " selected='selected'";
}
?>
				</select>
			</td>
		</tr>
		<tr>
			<td>Valori migliori:</td>
			<td>
				<select name="positive" class="form-control" required>
					<option value="<?=GREATER?>"<?=$g?>>Maggiori</option>
					<option value="<?=LOWER?>"<?=$m?>>Minori</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>Tipo di dati:</td>
			<td>
				<select name="type" class="form-control" required>
<?php
while($row=$ttest->fetch_assoc())
{
	echo "<option value='".$row['datatype_id']."'";
	if($row['datatype_id'] == $test['datatype_fk'])
		echo " selected='selected'";
	echo ">".htmlentities($row['datatype_name'])."</option>";
}
?>
				</select>
			</td>
		</tr>
	</table>
	<div>
		<h3>Informazioni aggiuntive</h3>
		Posizione:<br>
		<textarea class="txt" name="position"><?=$test['position']?></textarea>
		<br>
		Materiale aggiuntivo:<br>
		<textarea class="txt" name="equipment"><?=$test['equipment']?></textarea>
		<br>
		Esecuzione:<br>
		<textarea class="txt" name="execution"><?=$test['execution']?></textarea>
		<br>
		Consigli:<br>
		<textarea class="txt" name="suggestions"><?=$test['suggestions']?></textarea>
		<br>
		Limite:<br>
		<textarea class="txt" name="limit"><?=$test['test_limit']?></textarea>
		<br>
		Valutazione:<br>
		<textarea class="txt" name="grading" required><?=$test['assessment']?></textarea>
	</div>
	<input type="submit" id="submit" class="btn btn-primary marginunder" value="Aggiorna test">
</form>

<?php show_postmain(); ?>
