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

// Page to display and manage users
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
include $_SERVER['DOCUMENT_ROOT']."/libraries/lib_admin.php";
chk_access(ADMINISTRATOR);
connect();

show_premain("Gestione utenti");
?>

<h2>Utenti dell'applicazione</h2>

<div>
	<a href="user_add.php" class="btn btn-primary marginunder">Aggiungi nuovo</a>
</div>

<div class="tdiv">
	<div class="inner">
		<table class="table table-light table-striped">
			<tr>
				<th class="col">Username</th>
				<th class="col">Cognome</th>
				<th class="col">Scuola</th>
				<th class="col">Ultimo accesso</th>
				<th class="col">Privilegi</th>
				<th class="col"></th>
			</tr>
<?php
$user_st = prepare_stmt("SELECT * FROM user 
	LEFT JOIN school ON school_fk=school_id 
	ORDER BY privileges, last_login DESC, username");
$ret = execute_stmt($user_st);
$user_st->close();

while($row = $ret->fetch_assoc())
{
	$privileges = get_privilege($row['privileges']);

	echo "<tr>
			<td class='col'>".$row['username']."</td>
			<td class='col'>".$row['lastname']."</td>
			<td class='col'>".$row['school_name']."</td>
			<td class='col'>".$row['last_login']."</td>
			<td class='col'>
				<div class='boxdiv'><div class='colorbox ".$privileges['color']."'></div></div>
				<div class='privdiv'>".$privileges['text']."</div>
			</td>
			<td class='col'>";

	if($row['user_id'] != $_SESSION['id'])
		echo "<a href='user_details.php?id=".$row['user_id']."' class='btn btn-info'>Dettagli</a>";
			
	echo "</td>
		</tr>";
}
?>
		</table>
	</div>
</div>

<?php show_postmain(); ?>
