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

// Frontend page to show a user's details and change their permission
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
include $_SERVER['DOCUMENT_ROOT']."/libraries/lib_admin.php";
chk_access(0);
connect();

$user_st = prepare_stmt("SELECT * FROM user 
	LEFT JOIN school ON school_fk=school_id 
	WHERE user_id=?");
$user_st->bind_param("i", $_GET['id']);
$ret = execute_stmt($user_st);
$user_st->close();

$user = $ret->fetch_assoc();

$dg = can_downgrade($user['user_id']);

show_premain("Profilo di ".$user['username']);
?>

<h2>
	Profilo di <?=$user['username']?>
	<a href="/admin/user/users.php" class="btn btn-warning">Indietro</a>
</h2>

<form action="user_update.php?id=<?=$_GET['id']?>" method="POST" class="tdiv">
	<div class="inner">
		<table class="table table-light table-striped marginunder">
			<tr>
				<td class="col">Cognome</td>
				<td class="col"><?=$user['lastname']?></td>
			</tr>
			<tr>
				<td class="col">Nome</td>
				<td class="col"><?=$user['firstname']?></td>
			</tr>
			<tr>
				<td class="col">E-mail</td>
				<td class="col"><?=$user['email']?></td>
			</tr>
			<tr>
				<td class="col">Scuola</td>
				<td class="col"><?=$user['school_name']?></td>
			</tr>
			<tr>
				<td class="col">Privilegi</td>
				<td class="col">
					<select class="form-control" name="privileges">
<?php
// Options to change the privilege level are shown only in 
// upgrade if the username is not the original granter
$end = $dg ? NONE : $user['privileges'];
for($i = 0; $i <= $end; $i++)
{
	$privileges = get_privilege($i);
	if($privileges != null)
	{
		echo "<option value='$i'";
		if($user['privileges'] == $i)
			echo " selected";
		echo ">".$privileges['text']."</option>";
	}
}
?>
					</select>
				</td>
			</tr>
		</table>
		<input type="submit" class="btn btn-primary" value="Aggiorna privilegi">
	</div>
</form>

<?php show_postmain(); ?>
