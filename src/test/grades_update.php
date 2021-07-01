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

// Pagina chiamata da test.php per l'aggiornamento dei voti dell'utente user
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
chk_access(PROFESSOR);
connect();

// Selects which user's grades are updated
if(chk_auth(ADMINISTRATOR) and isset($_POST['slp']))
	$user = $_POST['slp'];
else
	$user = $_SESSION['id'];

$up_st = prepare_stmt("UPDATE grading SET percentile=? WHERE grade_fk=? AND user_fk=?");
$up_st->bind_param("iii", $newperc, $id, $user);

$tot = 0;
foreach($_POST['percentile'] as $id => $percentile)
{
	$newperc = $percentile + $tot;
	execute_stmt($up_st);
	$tot += $percentile;
}
$up_st->close();

set_alert("Voti aggiornati correttamente");
writelog("Voti di $user modificati");

header("Location: /test/test.php#grades"); 
exit; 
?>
