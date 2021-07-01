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

// Front end page to insert a new class
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
include $_SERVER['DOCUMENT_ROOT']."/libraries/lib_reg.php";
chk_access(PROFESSOR);
connect();
show_premain("Aggiunta classe");

$year = get_current_year();
?>

<h2>Aggiungi Classe</h2>

<div>
	<div class="marginunder">
		<button id="prom" class="btn btn-primary">Promuovi classe precedente</button>
	
		<div class="dpro jQhidden">
			Classe da promuovere: 
			<select id="clpr" class="form-control">
				<option selected disabled></option>
<?php
if(chk_auth(ADMINISTRATOR))
	$nad = "";
else
	$nad = "user_fk=? AND";

// Gets all user's classes of the previous year that do not have a following class yet
// An administrator can promote every class
$lastyear = $year - 1; 
$prom_st = prepare_stmt("SELECT C1.class_id, C1.class, C1.section FROM 
	(SELECT class_id, class, section, class_year FROM class WHERE $nad class_year=? AND school_fk=? AND class<>5) AS C1
	LEFT JOIN
	(SELECT class_id, class-1 AS class, section, class_year-1 AS class_year FROM class WHERE class_year=? AND school_fk=?) AS C2
	USING (class, section, class_year) 
	WHERE C2.class_id IS NULL 
	ORDER BY class, section");

if(chk_auth(ADMINISTRATOR))
	$prom_st->bind_param("iiii", $lastyear, $_SESSION['school'], $year, $_SESSION['school']);
else
	$prom_st->bind_param("iiiii", $_SESSION['id'], $lastyear, $_SESSION['school'], $year, $_SESSION['school']);

$ret = execute_stmt($prom_st);
$prom_st->close();

while($row = $ret->fetch_assoc())
	echo "<option value='".$row['class_id']."'>".$row['class'].htmlentities($row['section'])."</option>";
?>
			</select>
		</div>
	</div>
</div>

<form id="frm" method="POST" action="/register/class_insert.php">
<?php show_cl_form(); ?>
	<h3 class="dpro jQhidden">Studenti promossi nella nuova classe:</h3>
	<div id="divpro" class="dpro jQhidden">-</div>

	<h3>Nuovi studenti:</h3>
	<div class="tdiv">
  		<div id="tos" class="innerx">
			<table id="tabadd" class="table table-light table-striped studtable">
				<tr id="r0">
					<td><input type="text" id="c0" name="lcst[0]" class="last n0" placeholder="Cognome"></td>
					<td><input type="text" id="nm0" class="n0" name="nst[0]" placeholder="Nome"></td>
					<td class="containerflex">
						<div class="form-check">
							<input id="m0" class="n0 form-check-input" type="radio" name="gender[0]" value="m">
							<label class="form-check-label" for="m0">M</label>
						</div>
						<div class="form-check">
							<input id="f0" class="n0 form-check-input" type="radio" name="gender[0]" value="f">
							<label class="form-check-label" for="f0">F</label>
						</div>
					</td>
				</tr>
			</table>
		</div>
	</div>
	
	<div id="ext" class="jQhidden">
		<h3>Possibili studenti già registrati:</h3>
		<table class="table table-light table-striped studtable">
			<tbody id="tabext">
			</tbody>
		</table>
	</div>
	
	<input type="submit" value="Inserisci classe" class="btn btn-warning top-bot-margin">
</form>

<script src="/register/js/class_input.js"></script>

<?php show_postmain(); ?>
