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

// Backend script to update a user's permissions
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
include $_SERVER['DOCUMENT_ROOT']."/libraries/lib_admin.php";
chk_access(ADMINISTRATOR);
connect();

$chk_st = prepare_stmt("SELECT privileges FROM user WHERE user_id=?");
$chk_st->bind_param("i", $_GET['id']);
$ret = execute_stmt($chk_st);
$chk_st->close();

$user = $ret->fetch_assoc();

// Blocks updates that would remove admin privileges to an admin higher in
// the hierarchy wrt the current user
if(!can_downgrade($_GET['id']) and $_POST['privileges'] < $user['privileges'])
{
	set_alert("Modifica dei privilegi dell'utente non autorizzata");
	header("Location: /admin/user/users.php");
}

// If the privilege is not modified the updated is not carried out (in order to
// not modify the granter)
if($_POST['privileges'] != $user['privileges'])
{
	$up_st = prepare_stmt("UPDATE user SET privileges=?, granted_by=? WHERE user_id=?");
	$up_st->bind_param("iii", $_POST['privileges'], $_SESSION['id'], $_GET['id']);
	execute_stmt($up_st);
	$up_st->close();
}

set_alert("Privilegi aggiornati correttamente");
header("Location: /admin/user/users.php");
?>
