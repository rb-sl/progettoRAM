<?php 
// Page to show statistical data about a student
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
include $_SERVER['DOCUMENT_ROOT']."/libraries/lib_stat.php";
include $_SERVER['DOCUMENT_ROOT']."/libraries/lib_reg.php";
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

show_premain("Dati di ".$stud['cogs']." ".$stud['noms']." (".$stud['sesso'].")", true);

// The page opens on the percentile option, so the relative colors are loaded
$color = get_color_prc();
?>

<h2>Elaborazione dati di <span id="student"><?=$stud['cogs']?> <?=$stud['noms']?> (<?=$stud['sesso']?>)</span></h2>

<div>
	<a href="student_show.php?id=<?=$_GET['id']?>" class="btn btn-primary btnmenu">Mostra valori</a> 
    <h3>
    	Visualizzazione dati:
		<select id="vis" class="form-control">
        	<option value="prc">Valori percentili</option>
    		<option value="std">Valori standard</option>
   	 		<option value="gr">Voti</option>
		</select>
	</h3>
</div>

<div class="tdiv">
	<div class="inner">
    	<table class="table table-striped">
      		<tr id="thr" class="dat">
            	<td class="topleft leftfix topfix">
                	<button type="button" id="btnstat" class="btn overpad wtot">Medie e Mediane</button>
                	<br>
                	<button type="button" id="btncol" class="btn overpad wtot">Colori</button>
            	</td>
<?php
$ret = get_test($_GET['id'], true);

$test['row'] = "";
$test['id'] = [];
while($row = $ret->fetch_assoc())
{
    $test['id'][] = $row['id_test'];
    $test['pos'][$row['id_test']] = $row['pos'];
	$test['name'][$row['id_test']] = $row['nometest'];

    $test['row'] .= "<td id='c".$row['id_test']."' class='col topfix'>".$row['nometest']."</td>";
}

// Constuction of the table's body with percentile values
$rstud = get_perc($_GET['id'], null, true);

$am = get_avgmed($_GET['id'], $rstud['val'], true, true);

$rowavg = "";
foreach($am['avg'] as $idt => $avg)
	$rowavg .= "<td id='at$idt' class='jcol jtavg' vcolor='#".$avg['color']."'>".$avg['val']."</td>";

$rowmed = "";
foreach($am['med'] as $idt => $med)
	$rowmed .= "<td id='mt$idt' class='jcol jtmed borderunder' vcolor='#".$med['color']."'>".$med['val']."</td>";

echo $test['row']."
    <td class='col topfix r_stat evenrow jQhidden'>Media totale</td>
    <td id='tavg' class='col topfix r_stat jcol evenrow jQhidden' vcolor='#".(isset($am['tavg']['color']) ? $am['tavg']['color'] : "")."'>"
        .(isset($am['tavg']['val']) ? number_format($am['tavg']['val'], 5) : "-")."</td>
</tr>
<tr class='dat r_stat jQhidden'>
    <td class='leftfix evenrow'>Medie</td>$rowavg
    <td rowspan='2' id='med1' class='col r_stat oddrow jQhidden'>Medie<br>classi</td>
    <td rowspan='2' id='med2' class='col r_stat oddrow jQhidden'>Mediane<br>classi</td>
</tr>
<tr class='dat r_stat jQhidden'>
    <td class='leftfix oddrow'>Mediane</td>$rowmed
</tr>";

$plotinfo = "";
$plottheta = "";
$options = "";
foreach($rclass as $idcl => $class)
{
	echo $class['clrow'];
	$options = "<option value='$idcl'>".$class['name']."</option>".$options;

	$plotdata = "";
	$plottheta = "";
	$firstdata = "";
	$firsttheta = "";
	foreach($test['id'] as $idt)
	{
		echo "<td id='$idcl"."_$idt' class='jdat jcol r_$idcl c_$idt'";
		if(isset($rstud['val'][$idcl][$idt]))
		{
			if(isset($rstud['data'][$idcl][$idt]) and $rstud['data'][$idcl][$idt] != "0000-00-00")
				echo "title='".$rstud['data'][$idcl][$idt]."'";

        	echo " vcolor='#".$rstud['color'][$idcl][$idt]."'>"
				.$rstud['val'][$idcl][$idt]."</td>";

			if($firstdata == "")
			{
				$firstdata = $rstud['val'][$idcl][$idt];
				$firsttheta = "'".$test['name'][$idt]."'";
			}
			$plotdata .= $rstud['val'][$idcl][$idt].", ";
			$plottheta .= "'".$test['name'][$idt]."', ";
		}
		else 
        	echo ">-</td>";
    }

	$plotinfo .= "data[$idcl] = [{
		type: 'scatterpolar',
		r: [$plotdata $firstdata],
		theta: [$plottheta $firsttheta],
		fill: 'toself',
		name: '".$class['name']."',		
	}]\n";

	if(isset($am['savg'][$idcl]))
	{
		$avgcolor = $am['savg'][$idcl]['color'];
		$avg = $am['savg'][$idcl]['val'];
		$medcolor = $am['smed'][$idcl]['color'];
		$med = $am['smed'][$idcl]['val'];
	}
	else
	{
		$avgcolor = "";
		$avg = "-";
		$medcolor = "";
		$med = "-";
	}

	echo "<td id='a_$idcl' class='r_$idcl jsavg jcol lftbor r_stat jQhidden' vcolor='#$avgcolor'>$avg</td>
		  <td id='m_$idcl' class='r_$idcl jsmed jcol r_stat jQhidden' vcolor='#$medcolor'>$med</td>
		</tr>\n";
}
?>
		</table>
	</div>
</div>

<div>
	<h3>Grafico dei percentili della classe
	<select id="class" class="form-control">
		<?=$options?>
	</select>
</h3>
	<div id="myDiv">
	</div>
</div>
<script>
	var id = <?=$_GET['id']?>;
	var forstud = true;
	var data = [];
  	<?=$plotinfo?>
</script>
<script src="./js/student_show_stat.js"></script>
<script src="./js/class_show_stat.js"></script>

<?php show_postmain(); ?>