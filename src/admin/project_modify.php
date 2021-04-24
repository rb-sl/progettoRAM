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

// Front end page to modify the application's description
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
chk_access(ADMINISTRATOR);
connect();
show_premain("Modifica descrizione");

// Construction of the form based on the session
if(!isset($_SESSION['project_text']))
{
	$text_st = prepare_stmt("SELECT project_text FROM admindata");
	$ret = execute_stmt($text_st);
	$text_st->close();

	$row = $ret->fetch_assoc();
	if(isset($row['project_text']) and $row['project_text'] != "")
		$text = $row['project_text'];
	else
		$text = "";
}
else
	$text = $_SESSION['project_text'];
?>

<form action="project_update.php" method="POST">
	<h2>
		Descrizione nella pagina Il progetto
		<a href="/admin/admin.php" class="btn btn-warning marginunder">Indietro</a>
		<input type="submit" class="btn btn-primary marginunder" value="Aggiorna">
	</h2>

<?php
if(isset($_SESSION['syntax_error']))
{
	echo "<div class='dangercolor'>".$_SESSION['syntax_error']."</div>";
	unset($_SESSION['syntax_error']);
}
?>
	
	<textarea class="bigtextarea" name="project"><?=$text?></textarea>
</form>

<?php show_postmain(); ?>
