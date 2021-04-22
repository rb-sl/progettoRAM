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

$prof_st = prepare_stmt("SELECT nomp, cogp FROM PROFESSORI WHERE id_prof=?");
$prof_st->bind_param("i", $_SESSION['id']);

$ret = execute_stmt($prof_st);
$prof = $ret->fetch_assoc();
$prof_st->close();

if(isset($prof['cogp']))
	$lastname = $prof['cogp'];
else
	$lastname = "";

if(isset($prof['nomp']))
	$firstname = $prof['nomp'];
else
	$firstname = "";
?>

<h2>Registro di <?=($lastname != "" or $firstname != "") ? $firstname." ".$lastname : $_SESSION['user'] ?></h2>

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
	$class_st = prepare_stmt("SELECT * FROM CLASSI
		JOIN SCUOLE ON fk_scuola=id_scuola 
		WHERE fk_prof=? ORDER BY anno DESC, classe, sez");
	$class_st->bind_param("i", $_SESSION['id']);
}
else
{
	$class_st = prepare_stmt("SELECT * FROM CLASSI 
		JOIN PROFESSORI ON fk_prof=id_prof
		JOIN SCUOLE ON CLASSI.fk_scuola=id_scuola
		ORDER BY anno DESC, classe, sez");
}

$ret = execute_stmt($class_st);
$class_st->close();

$anno = -1;
$classe = 0;
while($row = $ret->fetch_assoc())
{
	if($row['anno'] != $anno)
	{
		echo "</div>
			<h3>".$row['anno']."/".($row['anno'] + 1)."</h3>
			<div>";
		$anno = $row['anno'];
	}
	else if($classe != $row['classe'])
		echo "</div><div>";

	if(chk_auth(ADMINISTRATOR) and isset($row['cogp']))
		$name = "\n".$row['cogp'];
	else 
		$name = "";

	if($_SESSION['id'] != $row['fk_prof'])
		$class = " nonpersonal";
	else
		$class= "";

	echo "<a href='/register/class_show.php?id=".$row['id_cl']."' class='btn btn-warning btncl$class' 
		title='".$row['nomescuola']."\n".$row['citta'].$name."'>".$row['classe'].$row['sez']."</a> ";
	
	$classe = $row['classe'];
}
?>

<script src="/register/js/common_register.js"></script>

<?php show_postmain(); ?>
