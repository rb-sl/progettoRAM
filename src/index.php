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

// Home page of the application
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
connect();
show_premain();

$info_st = prepare_stmt("SELECT index_compiled FROM admindata");
$ret = execute_stmt($info_st);
$info_st->close();
$info = $ret->fetch_assoc();
?>

<h2>Progetto RAM</h2>

<?php
// No HTML escape as it's already compiled
if(isset($info['index_compiled']))
	echo $info['index_compiled'];
?>

<p>
	Il Progetto RAM (Ricerca Attività Motorie) è un'applicazione che consiste in un registro elettronico per i 
  	professori di Educazione Fisica degli Istituti Superiori; consente di registrare diversi tipi di test e le prove 
	svolte dagli studenti, per poi effettuare statistiche sulla popolazione scolastica.
</p>
<p>
	Per utilizzare l'applicazione è necessario effettuare il login con le credenziali fornite. Per ottenere o 
	ripristinare le credenziali <a href="/guide/guide.php#contacts">contattare un amministratore</a>.<br>
  	Per ulteriori informazioni si rimanda alla <a href="/project.php">descrizione del progetto</a> e al 
	<a href="/guide/guide.php">manuale</a>.
</p>

<?php show_postmain(); ?>
