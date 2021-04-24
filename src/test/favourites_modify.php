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

// Page used to display and modify a user's favourite tests
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
chk_access(PROFESSOR);
connect();
show_premain("Modifica test preferiti");
?>

<h2>Modifica test preferiti</h2>

<form id="frm" method="POST" action="favourites_update.php" class="textleft containerflex flexform">
	<input type="submit" id="submit" class="btn btn-primary" value="Aggiorna" disabled> 
	<div class="fullwidth">
<?php
$test_st = prepare_stmt("SELECT test_id, test_name, position, equipment, 
	execution, suggestions, test_limit, assessment, test_fk AS favourite FROM test
	LEFT JOIN favourites ON test_fk=test_id
	AND (user_fk=? OR user_fk IS NULL)
	ORDER BY test_name");
$test_st->bind_param("i", $_SESSION['id']);
$rettest = execute_stmt($test_st);
$test_st->close();

while($rowt = $rettest->fetch_assoc())
{
	if(!$rowt['favourite'])
	{
		$chk = "";
		$class = " inactivetext";
		$btn = "btn-secondary";
	}
	else
	{
		$chk = " checked";
		$class = "";
		$btn = "btn-primary";
	}
	echo "<div class='testcard'>
			<h3 id='h".$rowt['test_id']."' class='card-header testrow'> 
				<div class='form-check'>
					<input type='checkbox' id='fav".$rowt['test_id']."' 
						class='form-check-input chkfav' name='fav[]' value='".$rowt['test_id']."'$chk>

					<label id='lbl".$rowt['test_id']."' class='form-check-label$class' for='fav".$rowt['test_id']."'>
						".$rowt['test_name']."
					</label>
				</div>
				<button type='button' id='btn".$rowt['test_id']."' 
					class='btn $btn' data-bs-toggle='collapse' data-bs-target='#coll".$rowt['test_id']."' 
					aria-expanded='false' aria-controls='#coll".$rowt['test_id']."'>Mostra informazioni</button>
			</h3>

			<div id='coll".$rowt['test_id']."' class='collapse textcenter'>
				<div class='card card-body'>
					<h4><b>Posizione</b></h4>
					<p>".($rowt['position'] == "" ? "-" : str_replace("\n", "<br>", $rowt['position']))."</p>
					<h4><b>Equipaggiamento</b></h4>
					<p>".($rowt['equipment'] == "" ? "-" : str_replace("\n", "<br>", $rowt['equipment']))."</p>
					<h4><b>Esecuzione</b></h4>
					<p>".($rowt['execution'] == "" ? "-" : str_replace("\n", "<br>", $rowt['execution']))."</p>
					<h4><b>Consigli</b></h4>
					<p>".($rowt['suggestions'] == "" ? "-" : str_replace("\n", "<br>", $rowt['suggestions']))."</p>
					<h4><b>Limite</b></h4>
					<p>".($rowt['test_limit'] == "" ? "-" : str_replace("\n", "<br>", $rowt['test_limit']))."</p>
					<h4><b>Valutazione</b></h4>
					<p>".($rowt['assessment'] == "" ? "-" : str_replace("\n", "<br>", $rowt['assessment']))."</p>
				</div>
			</div>

		</div>";
}
?>
	</div>

</form>

<script src="/test/js/favourites.js"></script>

<?php show_postmain(); ?>
