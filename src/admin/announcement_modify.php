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

// Front end page to modify the index's text
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
include $_SERVER['DOCUMENT_ROOT']."/libraries/lib_admin.php";
chk_access(ADMINISTRATOR);
connect();
show_premain("Modifica annuncio");

// Construction of the form based on the session
if(!isset($_SESSION['index_text']))
{
	$text_st = prepare_stmt("SELECT index_text, index_compiled FROM admindata");
	$ret = execute_stmt($text_st);
	$text_st->close();

	$row = $ret->fetch_assoc();
	$text = $row['index_text'];
	if(strpos($row['index_compiled'], "<h2 class='dangercolor'>") !== false)
		$chk = " checked";
	else
		$chk = "";
}
else
{
	$text = $_SESSION['index_text'];
	if($_SESSION['important'])
		$chk = " checked";
	else
		$chk = "";
}   
?>

<h2>
	Annuncio in home page
	<a href="/admin/admin.php" class="btn btn-warning marginunder">Indietro</a>
</h2>

<?php print_markup_menu(); ?>

<form action="announcement_update.php" method="POST">
	<div class="containerflex">
		<div class="form-check">
			<input type="checkbox" id="important" class="form-check-input" name="important"<?=$chk?>>

			<label class="form-check-label" for="important">
				Importante
			</label>
		</div>
	</div>

<?php
if(isset($_SESSION['syntax_error']))
{
	echo "<div class='dangercolor'>".$_SESSION['syntax_error']."</div>";
	unset($_SESSION['syntax_error']);
}
?>
	
	<textarea class="txt" name="announcement"><?=$text?></textarea>
	<br>
	<input type="submit" class="btn btn-primary marginunder" value="Aggiorna">
</form>

<?php show_postmain(); ?>
