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

// Page to add new tests
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
chk_access(PROFESSOR_GRANTS);
connect();
show_premain("Aggiungi test");

// Preparing statements for selects
$test_st = prepare_stmt("SELECT * FROM datatype ORDER BY datatype_name");
$clt_st = prepare_stmt("SELECT * FROM testtype ORDER BY testtype_name");
$unit_st = prepare_stmt("SELECT * FROM unit ORDER BY unit_name");
?>

<h2>Aggiungi nuovo test</h2>
<form method="POST" action="./test_insert.php">
	<table class="table table-light table-striped">
		<tr>
			<td>Nome test:</td>
			<td class="halfwidth"><input type="text" name="testname"></td>
		<tr>
			<td>Tipo di test:</td>
			<td>
				<select name="class" class="form-control">
					<option selected="selected" disabled>
<?php
$ctest = execute_stmt($clt_st);
$clt_st->close();

while($row = $ctest->fetch_assoc())
	echo "<option value='".$row['testtype_id']."'>".$row['testtype_name']."</option>";
?>
				</select>
			</td>
		</tr>
		<tr>
			<td>Unità di misura:</td>
			<td>
				<select name="unit" class="form-control" required>
					<option selected="selected" disabled>
<?php
$retunita = execute_stmt($unit_st);
$unit_st->close();

while($row = $retunita->fetch_assoc())
	echo "<option value='".$row['unit_id']."'>".$row['unit_name']."</option>";
?>
				</select>
			</td>
		</tr>
		<tr>
			<td>Valori migliori:</td>
			<td>
				<select name="positive"  class="form-control" required>
					<option selected="selected" disabled>
					<option value="<?=GREATER?>">Maggiori</option>
					<option value="<?=LOWER?>">Minori</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>Tipo di valori:</td>
			<td>
				<select name="type" class="form-control" required>
					<option selected="selected" disabled>
<?php
$ttest = execute_stmt($test_st);
$test_st->close();

while($row = $ttest->fetch_assoc())
	echo "<option value='".$row['datatype_id']."'>".$row['datatype_name']."</option>";
?>
				</select>
			</td>
		</tr>
	</table>
	<div>
		<h3>Informazioni aggiuntive</h3>
		Posizione:<br>
		<textarea class="txt" name="position"></textarea>
		<br>
		Materiale aggiuntivo:<br>
		<textarea class="txt" name="equipment"></textarea>
		<br>
		Esecuzione:<br>
		<textarea class="txt" name="execution"></textarea>
		<br>
		Consigli per l'insegnante:<br>
		<textarea class="txt" name="suggestions"></textarea>
		<br>
		Limite:<br>
		<textarea class="txt" name="limit"></textarea>
		<br>
		Valutazione:<br>
		<textarea class="txt" name="grading" required></textarea>
	</div>
	<input type="submit" id="submit" class="btn btn-warning marginunder" value="Inserisci test">
</form>

<?php show_postmain(); ?>
