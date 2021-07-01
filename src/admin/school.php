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

// Schools management page
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
chk_access(ADMINISTRATOR);
connect();
show_premain("Scuole");

// Gets the system's schools and the number of associated classes
$unit_st = prepare_stmt("SELECT school_id, school_name, city, (
		SELECT COUNT(*) AS n FROM school
		JOIN class ON school_fk=school_id
		WHERE school_id=S.school_id
	) AS n FROM school AS S
	LEFT JOIN class ON school_fk=school_id
	GROUP BY school_id 
	ORDER BY school_name");
$ret = execute_stmt($unit_st);
$unit_st->close();
?>

<h2>Gestione dei tipi di test</h2>

<form action="/admin/school_update.php" method="POST" class="tdiv">
	<button type="button" id="newrow" class="btn btn-primary">Aggiungi nuova</button>

	<div class="inner">
		<table id="datatable" class="table table-light table-striped studtable">
			<tr>
				<th>Nome scuola</th>
				<th>Città</th>
				<th class="col"></th>
			</tr>
<?php
while($row = $ret->fetch_assoc())
{
	echo "  <tr>
				<td id='c1_".$row['school_id']."'>".htmlentities($row['school_name'])."</td>
                <td id='c2_".$row['school_id']."'>".htmlentities($row['city'])."</td>
				<td>
					<div>
						<button type='button' id='mod_".$row['school_id']."' class='btn btn-warning btnmenu mod'>
							Modifica
						</button>"; 

	if($row['n'] == 0)
		echo "<a href='school_delete.php?id=".$row['school_id']."' class='btn btn-danger btnmenu' "
			.confirm("La scuola ".$row['school_name']." sarà eliminata").">Elimina</a>";

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
var col2type = "text"
</script>
<script src="/admin/js/tables.js"></script>

<?php show_postmain(); ?>
