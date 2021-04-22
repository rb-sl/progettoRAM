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
show_premain("Classi dei test");

// Gets the system's test types and the number of associated tests
$unit_st = prepare_stmt("SELECT id_cltest, nomec, (
		SELECT COUNT(*) AS n FROM CLTEST
		JOIN TEST ON fk_cltest=id_cltest
		WHERE id_cltest=C.id_cltest
	) AS n FROM CLTEST AS C
	LEFT JOIN TEST ON fk_cltest=id_cltest
	GROUP BY id_cltest 
	ORDER BY nomec");
$ret = execute_stmt($unit_st);
$unit_st->close();
?>

<h2>Gestione delle classi dei test</h2>

<form action="/admin/test/test_class_update.php" method="POST" class="tdiv">
	<button type="button" id="newrow" class="btn btn-primary">Aggiungi nuova</button>

	<div class="inner">
		<table id="datatable" class="table table-light table-striped studtable">
			<tr>
				<th>Classe del test</th>
				<th class="col"></th>
			</tr>
<?php
while($row = $ret->fetch_assoc())
{
	echo "  <tr>
				<td id='c1_".$row['id_cltest']."'>".$row['nomec']."</td>
				<td>
					<div>
						<button type='button' id='mod_".$row['id_cltest']."' class='btn btn-warning btnmenu mod'>
							Modifica
						</button>"; 

	if($row['n'] == 0)
		echo "<a href='test_class_delete.php?id=".$row['id_cltest']."' class='btn btn-danger btnmenu'"
			.confirm("La classe di test ".$row['nomec']." sarà eliminata").">Elimina</a>";

	echo  "         </div>
				</td>
			</tr>";
}
?>
		</table>
	</div>

	<input type="submit" id="submit" class="btn btn-primary jQhidden" value="Salva">
</form>

<script src="/admin/js/tables.js"></script>

<?php show_postmain(); ?>
