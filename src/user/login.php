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

// Script to log in a user
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
connect();

$prof_st = prepare_stmt("SELECT * FROM PROFESSORI WHERE BINARY user=? AND psw=MD5(?)");
$prof_st->bind_param("ss", $_POST['usr'], $_POST['psw']);
$ret = execute_stmt($prof_st);
$prof_st->close();

if($ret->num_rows != 0)
{
	$row = $ret->fetch_assoc();
	$_SESSION['user'] = $row['user'];
	$_SESSION['id'] = $row['id_prof'];
	$_SESSION['priv'] = $row['priv'];
	$_SESSION['school'] = $row['fk_scuola'];

	// Updates the login time
	$up_st = prepare_stmt("UPDATE PROFESSORI SET last_login=NOW() WHERE id_prof=?");
	$up_st->bind_param("i", $row['id_prof']);
	execute_stmt($up_st);
	$up_st->close();
	
	writelog("Accesso");

	if($row['lastpsw'] === null)
		set_error(FIRST_ACCESS);
	
	// Redirects based on the user's status
	if($row['priv'] > PROFESSOR)
		header("Location: /");
	else
		header("Location: /register/register.php");
  	exit;
}

// If no user is found an error is shown
set_error(WRONG_LOGIN);
header("Location: /");
exit;
?>
