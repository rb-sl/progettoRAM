<?php 
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
include $_SERVER['DOCUMENT_ROOT']."/libraries/lib_reg.php";
include $_SERVER['DOCUMENT_ROOT']."/libraries/lib_stat.php";
chk_access(2);
connect();

$class_st = prepare_stmt("SELECT * FROM CLASSI WHERE id_cl=?");
$class_st->bind_param("i", $_GET['id']);

$ret = execute_stmt($class_st);
$cl = $ret->fetch_assoc();
$class_st->close();

chk_prof($cl['fk_prof']);

show_premain("Registro ".$cl['classe'].$cl['sez']." ".$cl['anno']."/".($cl['anno'] + 1));
?>

<h2>
	Registro della classe <?=$cl['classe'].$cl['sez']?> - Anno <?=$cl['anno']."/".($cl['anno'] + 1)?> 
	<a href="./mod_classe.php?id=<?=$_GET['id']?>" class="btn btn-warning btnmenu">Modifica</a>
</h2>

<div>
	<a href="show_classe_stat.php?id=<?=$_GET['id']?>" class="btn btn-primary btnmenu">Elaborazione dati della classe</a> 
</div>

<form action="in_prove.php?cl=<?=$_GET['id']?>" id="frm" method="POST">
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

// Costruzione del corpo della tabella con i valori delle prove
$result_st = prepare_stmt("SELECT * FROM PROVE JOIN ISTANZE ON fk_ist=id_ist
	JOIN TEST ON fk_test=id_test
	JOIN UNITA ON fk_udm=id_udm
	WHERE fk_cl=?");
$result_st->bind_param("i", $_GET['id']);
$retprove = execute_stmt($result_st);

while($row = $retprove->fetch_assoc())
{
	$vals[$row['fk_test']][] = $row['valore'];
	$rstud[$row['id_ist']][$row['fk_test']] = "title='".$row['data']."'>".$row['valore']." ".$row['simbolo']."</td";
}
$result_st->close();

$ret = get_test();

// Stampa delle righe di test, medie e mediane
$ravg = "";
$rmed = "";
while($row = $ret->fetch_assoc())
{
  	echo "<td id='c".$row['id_test']."' class='col topfix'>".$row['nometest']."</td>";
	$idtest[] = $row['id_test'];

	$ravg .= "<td id='r".$row['id_test']."'>".$row['avg']." ".$row['simbolo']."</td>";	
	$rmed .= "<td id='r".$row['id_test']."' style='border-bottom:1px solid black'>"
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
<script src="js/show_classe.js"></script>

<?php show_postmain(); ?>