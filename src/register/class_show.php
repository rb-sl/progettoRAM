<?php 
// Page to show the data of a class
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
include $_SERVER['DOCUMENT_ROOT']."/libraries/lib_reg.php";
include $_SERVER['DOCUMENT_ROOT']."/libraries/lib_stat.php";
chk_access(2);
connect();

$cl = get_class_info($_GET['id']);
chk_prof($cl['fk_prof']);

show_premain("Registro ".$cl['classe'].$cl['sez']." ".$cl['anno']."/".($cl['anno'] + 1));
?>

<h2>
	Registro della classe <?=$cl['classe'].$cl['sez']?> - Anno <?=$cl['anno']."/".($cl['anno'] + 1)?> 
	<a href="./class_modify.php?id=<?=$_GET['id']?>" class="btn btn-warning btnmenu">Modifica</a>
</h2>

<div>
	<a href="class_show_stat.php?id=<?=$_GET['id']?>" class="btn btn-primary btnmenu">Elaborazione dati della classe</a> 
</div>

<form action="result_insert.php?cl=<?=$_GET['id']?>" id="frm" method="POST">
	<button type="button" id="btnadd" class="btn btn-warning btnmenu">Aggiungi test</button>
	<button type="button" id="btncan" class="btn btn-danger btnmenu jQhidden">Annulla</button>
	<input type="submit" id="btncar" class="btn btn-warning btnmenu jQhidden" value="Salva">
     
	<div class="tdiv">
  		<div id="tos" class="inner">
    		<table id="tts" class="table table-striped">
      			<tr id="thr" class="dat">
					<td class="topleft topfix leftfix">
						<button type="button" id="btnstat" class="btn overpad wtot">Medie e mediane</button>
					</td>
<?php
$rstud = col_stud();

// Construction of table body
$result_st = prepare_stmt("SELECT * FROM PROVE JOIN ISTANZE ON fk_ist=id_ist
	JOIN TEST ON fk_test=id_test
	JOIN UNITA ON fk_udm=id_udm
	WHERE fk_cl=?");
$result_st->bind_param("i", $_GET['id']);
$retprove = execute_stmt($result_st);

while($row = $retprove->fetch_assoc())
{
	$vals[$row['fk_test']][] = $row['valore'];
	// The date is added if it is not a placeholder
	$rstud[$row['id_ist']][$row['fk_test']] = ($row['data'] != "0000-00-00" ? "title='".$row['data']."'" : "").">"
		.$row['valore']." ".$row['simbolo']."</td";
}
$result_st->close();

$ret = get_test($_GET['id']);

// Output of rows for test, average and median
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

// Printing student rows 
foreach($rstud as $idist => $tds)
{
	echo $tds['strow'];
	foreach($idtest as $idt)
   	{
    	echo "<td id='$idist"."_$idt' class='jdat r_$idist c_$idt'";
   		if(isset($tds[$idt]))
       		echo $tds[$idt];
    	echo "></td>";
   	}
	echo "</tr>\n";
}
?>
			</table>
    	</div>
	</div>
</form>

<script>
	var id = <?=$_GET['id']?>;
</script>
<script src="js/class_show.js"></script>

<?php show_postmain(); ?>