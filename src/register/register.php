<?php 
// Main register page - shows the user's classes and allows to add more
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
if($_SESSION['priv'] != ADMINISTRATOR)
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

	if($_SESSION['priv'] == ADMINISTRATOR and isset($row['cogp']))
		$name = "\n".$row['cogp'];
	else 
		$name = "";

	echo "<a href='/register/class_show.php?id=".$row['id_cl']."' class='btn btn-warning btncl' 
		title='".$row['nomescuola']."\n".$row['citta'].$name."'>".$row['classe'].$row['sez']."</a> ";
	
	$classe = $row['classe'];
}

show_postmain();
?>
