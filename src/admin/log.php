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

// Front end page to show the application's logs
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
chk_access(ADMINISTRATOR);
connect();
show_premain("Log dell'applicazione");
?>

<h2>Log di utilizzo</h2>

<div class="logdiv">
	<div class="loglist">
<?php
$cont = array_diff(scandir(LOG_PATH, SCANDIR_SORT_DESCENDING), array("..", "."));
$i = 0;
foreach($cont as $g)
{
	echo "<span id='sp$i' class='splog'>$g<br></span>";
	$i++;
}
?>
	</div>
  
	<div class="logcontainer">
		<textarea id="txt" class="logtxt"></textarea>
	</div>
</div>

<script src="/admin/js/log.js"></script>

<?php show_postmain(); ?>
