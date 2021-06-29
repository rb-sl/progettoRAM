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

// Main register page, shows the user's classes
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
chk_access(PROFESSOR);
connect();
show_premain("Registro");

$prof_st = prepare_stmt("SELECT firstname, lastname FROM user WHERE user_id=?");
$prof_st->bind_param("i", $_SESSION['id']);

$ret = execute_stmt($prof_st);
$user = $ret->fetch_assoc();
$prof_st->close();

if(!isset($_SESSION['school']))
{
	echo "<h2 class='dangercolor'>Impostare una scuola nel <a href='/user/profile.php'>Profilo</a></h2>";
	show_postmain();
	exit;
}

if(isset($user['lastname']))
	$lastname = htmlentities($user['lastname']);
else
	$lastname = "";

if(isset($user['firstname']))
	$firstname = htmlentities($user['firstname']);
else
	$firstname = "";
?>

<h2>Registro di <?=($lastname != "" or $firstname != "") ? $firstname." ".$lastname : htmlentities($_SESSION['username']) ?></h2>

<div>
	<a href="/register/class_add.php" class="btn btn-primary marginunder">Aggiungi classe</a>
<?php
if(chk_auth(ADMINISTRATOR))
{
?>
	<div class="containerflex">
		<div class="form-check">
			<input type="checkbox" id="showall" class="form-check-input" name="important">

			<label class="form-check-label" for="showall">
				Mostra tutte le classi
			</label>
		</div>
	</div>
<?php
}

if(!chk_auth(ADMINISTRATOR))
{
	$class_st = prepare_stmt("SELECT * FROM class
		JOIN school ON school_fk=school_id 
		WHERE user_fk=? ORDER BY class_year DESC, class, section");
	$class_st->bind_param("i", $_SESSION['id']);
}
else
{
	$class_st = prepare_stmt("SELECT * FROM class 
		JOIN user ON user_fk=user_id
		JOIN school ON class.school_fk=school_id
		ORDER BY class_year DESC, class, section");
}

$ret = execute_stmt($class_st);
$class_st->close();

$class_year = -1;
$class = 0;
while($row = $ret->fetch_assoc())
{
	if($row['class_year'] != $class_year)
	{
		echo "</div>
			<h3>".$row['class_year']."/".($row['class_year'] + 1)."</h3>
			<div>";
		$class_year = $row['class_year'];
	}
	else if($class != $row['class'])
		echo "</div><div>";

	if(chk_auth(ADMINISTRATOR) and isset($row['lastname']))
		$name = "\n".htmlentities($row['lastname']);
	else 
		$name = "";

	if($_SESSION['id'] != $row['user_fk'])
		$class = "btn-secondary nonpersonal";
	else
		$class= "btn-warning";

	echo "<a href='/register/class_show.php?id=".$row['class_id']."' class='btn btncl $class' 
		title='".htmlentities($row['school_name'])."\n".htmlentities($row['city']).$name."'>"
		.$row['class'].htmlentities($row['section'])."</a> ";
	
	$class = $row['class'];
}
?>

<script src="/register/js/common_register.js"></script>

<?php show_postmain(); ?>
