<?php 
// Script to show a student's information
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
include $_SERVER['DOCUMENT_ROOT']."/libraries/lib_reg.php";
include $_SERVER['DOCUMENT_ROOT']."/libraries/lib_stat.php";
chk_access(2);
connect();

// Gets the student's information
$stud_st = prepare_stmt("SELECT * FROM STUDENTI WHERE id_stud=?");
$stud_st->bind_param("i", $_GET['id']);
$ret = execute_stmt($stud_st);
$stud_st->close();

$stud = $ret->fetch_assoc();

$rclass = col_class($stud['id_stud']);

// The loading is interrupted if the user does not own at least one class with the student
if($rclass === null)
{
	$_SESSION['alert'] = "Permessi insufficienti per visualizzare le informazioni";
	header("Location: /register/register.php");
	exit;
}

show_premain("Dati di ".$stud['cogs']." ".$stud['noms']." (".$stud['sesso'].")");
?>
<h2>Prove di <?=$stud['cogs']?> <?=$stud['noms']?> (<?=$stud['sesso']?>)</h2>

<div>
	<a href="/registro/student_modify.php?id=<?=$stud['id_stud']?>" class="btn btn-warning btnmenu marginunder">Modifica</a>
	<br>
	<a href="student_show_stat.php?id=<?=$_GET['id']?>" class="btn btn-primary btnmenu">Elaborazione dati</a> 
</div>

<div class="tdiv">
	<div id="tos" class="inner">
		<table id="tts" class="table table-striped">
			<tr id="thr" class="dat">
				<td class="topleft topfix leftfix">
					<button type="button" id="btnstat" class="btn overpad wtot">Medie e mediane</button>
				</td>
<?php
// Construction of table body
$result_st = prepare_stmt("SELECT * FROM PROVE JOIN ISTANZE ON fk_ist=id_ist
	JOIN TEST ON fk_test=id_test
	JOIN UNITA ON fk_udm=id_udm
	WHERE fk_stud=?");
$result_st->bind_param("i", $_GET['id']);
$retprove = execute_stmt($result_st);

while($row = $retprove->fetch_assoc())
{
	$vals[$row['fk_test']][] = $row['valore'];
	// The date is added if it is not a placeholder
	$rclass[$row['fk_cl']][$row['fk_test']] = ($row['data'] != "0000-00-00" ? "title='".$row['data']."'" : "").">"
		.$row['valore']." ".$row['simbolo']."</td";
}
$result_st->close();

$ret = get_test($_GET['id'], true);

$ravg = "";
$rmed = "";
$idtest = [];
while($row = $ret->fetch_assoc())
{
  	echo "<td id='c".$row['id_test']."' class='col topfix'>".$row['nometest']."</td>";
	$idtest[] = $row['id_test'];

	$ravg .= "<td id='r".$row['id_test']."'>".$row['avg']." ".$row['simbolo']."</td>";	
	$rmed .= "<td id='r".$row['id_test']."' class='borderunder'>"
		.arr_med($vals[$row['id_test']], 2)." ".$row['simbolo']."</td>";
}
echo "</tr>
	<tr class='dat r_stat jQhidden'>
		<td class='leftfix evenrow'>Medie:</td>
		$ravg
	</tr>
    <tr class='dat r_stat jQhidden'>
		<td class='leftfix oddrow'>Mediane:</td>
		$rmed
	</tr>";

foreach($rclass as $id => $cl)
{
	echo $cl['clrow'];
	foreach($idtest as $idt)
   	{
    	echo "<td id='$id"."_$idt' class='jdat r_$id c_$idt'";
   		if(isset($cl[$idt]))
       		echo $cl[$idt];
    	echo "></td>";
   	}
	echo "</tr>\n";
}
?>
		</table>
	</div>
</div>

<?php show_postmain(); ?>