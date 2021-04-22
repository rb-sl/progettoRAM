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

// Front end page to display administrative functions
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
chk_access(ADMINISTRATOR);
show_premain();
?>

<h2>Strumenti amministrativi</h2>

<p>
	<a href="/admin/log.php" class="btn btn-secondary marginunder">Log di utilizzo</a><br>
	<a href="/admin/user/users.php" class="btn btn-info marginunder">Gestione utenti</a><br>
	<a href="/admin/test/unit.php" class="btn btn-primary marginunder">Gestione unità di misura</a><br>
	<a href="/admin/test/test_type.php" class="btn btn-primary marginunder">Gestione tipi dei dati dei test</a><br>
	<a href="/admin/test/test_class.php" class="btn btn-primary marginunder">Gestione classi dei test</a><br>
	<a href="/admin/project_modify.php" class="btn btn-info marginunder">Cambia descrizione del progetto</a><br>
	<a href="/admin/announcement_modify.php" class="btn btn-info marginunder">Cambia annuncio in home page</a><br>
	<a href="/admin/student/student_correction.php" class="btn btn-warning marginunder">Correzione profili degli studenti</a>
</p>

<?php show_postmain(); ?>
