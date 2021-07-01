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

$prof_st = prepare_stmt("SELECT * FROM user WHERE BINARY username=? AND password=MD5(?)");
$prof_st->bind_param("ss", $_POST['user'], $_POST['password']);
$ret = execute_stmt($prof_st);
$prof_st->close();

if($ret->num_rows != 0)
{
	$row = $ret->fetch_assoc();
	$_SESSION['username'] = $row['username'];
	$_SESSION['id'] = $row['user_id'];
	$_SESSION['privileges'] = $row['privileges'];
	$_SESSION['alert'] = [];
	if($row['school_fk'] !== null)
		$_SESSION['school'] = $row['school_fk'];

	// Updates the login time
	$up_st = prepare_stmt("UPDATE user SET last_login=NOW() WHERE user_id=?");
	$up_st->bind_param("i", $row['user_id']);
	execute_stmt($up_st);
	$up_st->close();
	
	writelog("Accesso");

	if($row['last_password'] === null)
		set_error(FIRST_ACCESS);
	
	// Redirects based on the user's status
	if($row['privileges'] > PROFESSOR)
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
