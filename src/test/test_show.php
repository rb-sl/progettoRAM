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

// Test parameters visualization
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
chk_access(RESEARCH);
connect();

// If the test does not exist an error is shown to the user
$test_st = prepare_stmt("SELECT * FROM test JOIN unit ON unit_fk=unit_id
	JOIN datatype ON datatype_fk=datatype_id
	JOIN testtype ON testtype_fk=testtype_id
	WHERE test_id=?");
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

show_premain(htmlentities($test['test_name']));
?>
<h2>Informazioni <?=htmlentities($test['test_name'])?></h2>

<table class="table table-light table-striped marginunder">
	<tr>
		<td>Tipo di test:</td>
		<td class="halfwidth"><?=htmlentities($test['testtype_name'])?></td>
	</tr>
	<tr>
		<td>Unità di misura:</td>
		<td><?=htmlentities($test['unit_name'])?></td>
	</tr>
	<tr>
		<td>Valori migliori:</td>
		<td><?=$test['positive_values']?>
		</td>
	</tr>
	<tr>
		<td>Tipo di dati:</td>
		<td><?=htmlentities($test['datatype_name'])?></td>
	</tr>
	<tr>
		<td>Sensibilità del test:</td>
		<td><?=$test['step']." ".htmlentities($test['symbol'])?></td>
	</tr>
	<tr>
		<th colspan="2" class="textcenter">Informazioni aggiuntive</th>
	</tr>
	<tr>
		<td>Posizione:</td>
		<td><?=($test['position'] ? htmlentities($test['position']) : "-")?></td>
	</tr>
	<tr>
		<td>Materiale aggiuntivo:</td>
		<td><?=($test['equipment'] ? htmlentities($test['equipment']) : "-")?></td>
	</tr>
	<tr>
		<td>Esecuzione:</td>
		<td><?=($test['execution'] ? htmlentities($test['execution']) : "-")?></td>
	</tr>
	<tr>
		<td>Consigli:</td>
		<td><?=($test['suggestions'] ? htmlentities($test['suggestions']) : "-")?></td>
	</tr>
	<tr>
		<td>Limite:</td>
		<td><?=($test['test_limit'] ? htmlentities($test['test_limit']) : "-")?></td>
	</tr>
	<tr>
		<td>Valutazione:</td>
		<td><?=htmlentities($test['assessment'])?></td>
	</tr>
</table>

<?php
if(chk_auth(PROFESSOR_GRANTS))
	echo "<div class='marginunder'><a href='./test_modify.php?id=".$_GET['id']
		."' class='btn btn-warning'>Modifica test</a></div>";
	
show_postmain();
?>
