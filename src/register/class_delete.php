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

// Backend script to delete a class
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
chk_access(PROFESSOR);
connect();

$del_st = prepare_stmt("DELETE FROM CLASSI WHERE id_cl=?");
$del_st->bind_param("i", $_GET['id']);
execute_stmt($del_st);
$del_st->close();

// The trigger to delete students without instances does not activate
// for cascade deletions, so this query is executed
$stud_st = prepare_stmt("DELETE FROM STUDENTI 
	WHERE id_stud NOT IN (SELECT DISTINCT(fk_stud) FROM ISTANZE)");
execute_stmt($stud_st);
$stud_st->close();

writelog("[Classe eliminata] ".$_GET['id']);
$_SESSION['alert'] = "Classe eliminata correttamente";
header("Location: /register/register.php");
?>
