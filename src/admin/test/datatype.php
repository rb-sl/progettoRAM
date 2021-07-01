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

// Test types management page
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
chk_access(ADMINISTRATOR);
connect();
show_premain("Tipi dei dati per test");

// Gets the system's test types and the number of associated tests
$unit_st = prepare_stmt("SELECT datatype_id, datatype_name, step, (
		SELECT COUNT(*) AS n FROM datatype
		JOIN test ON datatype_fk=datatype_id
		WHERE datatype_id=T.datatype_id
	) AS n FROM datatype AS T
	LEFT JOIN test ON datatype_fk=datatype_id
	GROUP BY datatype_id 
	ORDER BY datatype_name");
$ret = execute_stmt($unit_st);
$unit_st->close();
?>

<h2>Gestione dei tipi di dati dei test</h2>

<form action="/admin/test/datatype_update.php" method="POST" class="tdiv">
	<button type="button" id="newrow" class="btn btn-primary">Aggiungi nuovo</button>

	<div class="inner">
		<table id="datatable" class="table table-light table-striped studtable">
			<tr>
				<th>Tipo dei dati</th>
				<th>Passo</th>
				<th class="col"></th>
			</tr>
<?php
while($row = $ret->fetch_assoc())
{
	echo "  <tr>
				<td id='c1_".htmlentities($row['datatype_id'])."'>".htmlentities($row['datatype_name'])."</td>
				<td id='c2_".htmlentities($row['datatype_id'])."'>".htmlentities($row['step'])."</td>
				<td>
					<div>
						<button type='button' id='mod_".$row['datatype_id']."' class='btn btn-warning btnmenu mod'>
							Modifica
						</button>"; 

	if($row['n'] == 0)
		echo "<a href='datatype_delete.php?id=".htmlentities($row['datatype_id'])."' class='btn btn-danger btnmenu' "
			.confirm("Il tipo di test ".$row['datatype_name']." passo ".$row['step']." sarà eliminato").">Elimina</a>";

	echo  "         </div>
				</td>
			</tr>";
}
?>
		</table>
	</div>

	<input type="submit" id="submit" class="btn btn-primary jQhidden" value="Salva">
</form>

<script>
var col2type = "number"
</script>
<script src="/admin/js/tables.js"></script>

<?php show_postmain(); ?>
