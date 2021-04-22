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

// Page used to display the project's description
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
connect();
show_premain("Il progetto");

$text_st = prepare_stmt("SELECT project_compiled FROM ADMINDATA");
$ret = execute_stmt($text_st);
$text_st->close();

$row = $ret->fetch_assoc();
?>

<div class="textwall">	
	<h2>Il Progetto RAM</h2>
	<?=$row['project_compiled']?>
</div>

<?php show_postmain(); ?>
