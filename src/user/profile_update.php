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

// Script to update a user's profile
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
chk_access();
connect();

// Check on username uniqueness; if the username is already taken,
// the update fails and the user is asked to repeat the procedure
$chk_st = prepare_stmt("SELECT * FROM user WHERE username=? AND user_id<>?");
$chk_st->bind_param("si", $_POST['user'], $_SESSION['id']);
$chk = execute_stmt($chk_st);
$chk_st->close();

if($chk->num_rows > 0)
{
	$_SESSION['alert'] = "Username già in uso! Modifiche non effettuate";
	header("Location: /user/profile.php");
	exit;
}

$_SESSION['alert'] = "Aggiornamento avvenuto con successo";
$location = "/user/profile.php";

if(isset($_POST['school']) and $_POST['school'] !== "")
	$school = $_POST['school'];
else
	$school = null;

// The query is built considering if the user wishes to update their password
if(!empty($_POST['password']))
{
	$up_st = prepare_stmt("UPDATE user SET username=?, firstname=?, lastname=?, email=?, 
		contact_info=?, show_email=?, school_fk=?, password=MD5(?), last_password=CURDATE() 
		WHERE user_id=?");
	$up_st->bind_param("sssssiisi", $_POST['user'], $_POST['firstname'], $_POST['lastname'], 
		$_POST['email'], $_POST['contact'], $showmail,  $school, 
		$_POST['password'], $_SESSION['id']);
  	
	if($_SESSION['err'] == FIRST_ACCESS)
	{
		$_SESSION['err'] = "";
		$_SESSION['alert'] = "Primo accesso completato: ora l'applicazione è attivata";
		$location = "/guide/guide.php";
	}
	$_SESSION['scad'] = false;
}
else
{
	$up_st = prepare_stmt("UPDATE user SET username=?, firstname=?, lastname=?, email=?, 
		contact_info=?, show_email=?, school_fk=? 
		WHERE user_id=?");
	$up_st->bind_param("sssssiii", $_POST['user'], $_POST['firstname'], $_POST['lastname'], 
		$_POST['email'], $_POST['contact'], $showmail, $school, 
		$_SESSION['id']);
}

if(isset($_POST['showmail']))
	$showmail = 1;
else
	$showmail = 0;

$ret = execute_stmt($up_st);
$up_st->close();

// Update of the active username
$_SESSION['username'] = $_POST['user'];  
$_SESSION['school'] = $_POST['school'];

writelog("[Modifica profilo] ".$_SESSION['id']);
header("Location: $location");
?>
