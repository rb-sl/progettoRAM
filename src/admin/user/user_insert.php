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

// Backend script to add a new user
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
chk_access(ADMINISTRATOR);
connect();

$in_st = prepare_stmt("INSERT INTO PROFESSORI(user, psw, priv, granted_by, nomp, cogp, email, fk_scuola) 
	VALUES (?, MD5(?), ?, ?, ?, ?, ?, ?)");
$in_st->bind_param("ssiisssi", $_POST['usr'], $_POST['psw'], $_POST['priv'], $_SESSION['id'], 
	$_POST['nom'], $_POST['cog'], $_POST['mail'], $_POST['sc']);
execute_stmt($in_st);
$in_st->close();
$id = $mysqli->insert_id;

// The default grades of the new user are equal to the ones of the administrator
$val_st = prepare_stmt("INSERT INTO VALUTAZIONI(fk_prof, fk_voto, perc) 
	SELECT ? AS fk_prof, fk_voto, perc
	FROM VALUTAZIONI
	WHERE fk_prof=?");
$val_st->bind_param("ii", $id, $_SESSION['id']);
execute_stmt($val_st);
$val_st->close();

// Adds all available tests to favourites
$fav_st = prepare_stmt("INSERT INTO PROF_TEST(fk_prof, fk_test) 
	SELECT ? AS fk_prof, id_test
	FROM TEST");
$fav_st->bind_param("i", $id);
execute_stmt($fav_st);
$fav_st->close();

$_SESSION['alert'] = "Utente inserito correttamente";
header("Location: /admin/user/users.php");
?>
