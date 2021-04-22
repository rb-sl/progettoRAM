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

// Pagina chiamata da test.php per l'aggiornamento dei voti dell'utente prof
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
chk_access(PROFESSOR);
connect();

// Selects which user's grades are updated
if(chk_auth(ADMINISTRATOR) and isset($_POST['slp']))
	$prof = $_POST['slp'];
else
	$prof = $_SESSION['id'];

$up_st = prepare_stmt("UPDATE VALUTAZIONI SET perc=? WHERE fk_voto=? AND fk_prof=?");
$up_st->bind_param("iii", $newperc, $id, $prof);

$tot = 0;
foreach($_POST['perc'] as $id => $perc)
{
	$newperc = $perc + $tot;
	execute_stmt($up_st);
	$tot += $perc;
}
$up_st->close();

$_SESSION['alert'] = "Voti aggiornati correttamente";
writelog("Voti di $prof modificati");

header("Location: /test/test.php#grades"); 
exit; 
?>
