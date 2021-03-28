<?php 
// Page to show statistical data about a class
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
include $_SERVER['DOCUMENT_ROOT']."/libraries/lib_stat.php";
include $_SERVER['DOCUMENT_ROOT']."/libraries/lib_reg.php";
chk_access(2);
connect();

$cl = get_class_info($_GET['id']);
chk_prof($cl['fk_prof']);

show_premain("Registro ".$cl['classe'].$cl['sez']." ".$cl['anno']."/".($cl['anno'] + 1), true);

// The page opens on the percentile option, so the relative colors are loaded
$color = get_color_prc();
?>

<h2>Registro della classe <?=$cl['classe'].$cl['sez']?> - Anno <?=$cl['anno']."/".($cl['anno'] + 1)?></h2>

<div>
	<a href="class_show.php?id=<?=$_GET['id']?>" class="btn btn-primary btnmenu">Registro della classe</a>
	<h3>
    	Visualizzazione classe:
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
                	<button type="button" id="btnstat" class="btn overpad fullwidth">Medie e Mediane</button>
                	<br>
                	<button type="button" id="btncol" class="btn overpad fullwidth">Colori</button>
            	</td>
<?php
// Header construction
$ret = get_test($_GET['id']);

$test['row'] = "";
$test['id'] = [];
while($row = $ret->fetch_assoc())
{
	$test['id'][] = $row['id_test'];
	$test['pos'][$row['id_test']] = $row['pos'];

	$test['row'] .= "<td id='c".$row['id_test']."' class='col topfix'>".$row['nometest']."</td>";
}

// Constuction of the table's body with percentile values
$rstud = get_perc($_GET['id']);

// Calculates and constructs the rows and columns related to average and median for both tests and students 
$am = get_avgmed($_GET['id'], $rstud['val'], true);

$rowavg = "";
foreach($am['avg'] as $idt => $avg)
	$rowavg .= "<td id='at$idt' class='jcol jtavg' vcolor='#".$avg['color']."'>".$avg['val']."</td>";

$rowmed = "";
foreach($am['med'] as $idt => $med)
	$rowmed .= "<td id='mt$idt' class='jcol jtmed borderunder' vcolor='#".$med['color']."'>".$med['val']."</td>";

// Prints the table header and information on average and median values
echo $test['row']."
		<td class='col topfix r_stat evenrow jQhidden'>Media totale</td>
        <td id='tavg' class='col topfix r_stat jcol evenrow jQhidden' vcolor='#".(isset($am['tavg']['color']) ? $am['tavg']['color'] : "")."'>"
			.(isset($am['tavg']['val']) ? number_format($am['tavg']['val'], 5) : "-")."</td>
	</tr>
	<tr class='dat r_stat jQhidden'>
    	<td class='leftfix evenrow'>Medie</td>$rowavg
        <td rowspan='2' id='med1' class='col r_stat oddrow jQhidden'>Medie<br>studenti</td>
        <td rowspan='2' id='med2' class='col r_stat oddrow jQhidden'>Mediane<br>studenti</td>
    </tr>
	<tr class='dat r_stat jQhidden'>
    	<td class='leftfix oddrow'>Mediane</td>$rowmed
    </tr>";

$cstud = col_stud();
foreach($cstud as $idist => $stud)
{
	echo $stud['strow'];
	
	foreach($test['id'] as $idt)
	{
    	echo "<td id='$idist"."_$idt' class='jdat jcol r_$idist c_$idt'";
		if(isset($rstud['val'][$idist][$idt]))
		{
			if(isset($rstud['data'][$idist][$idt]) and $rstud['data'][$idist][$idt] != "0000-00-00")
				echo "title='".$rstud['data'][$idist][$idt]."'";

        	echo " vcolor='#".$rstud['color'][$idist][$idt]."'>"
				.$rstud['val'][$idist][$idt]."</td>";
		}
		else 
        	echo ">-</td>";
    }

	if(isset($am['savg'][$idist]))
	{
		$avgcolor = $am['savg'][$idist]['color'];
		$avg = $am['savg'][$idist]['val'];
		$medcolor = $am['smed'][$idist]['color'];
		$med = $am['smed'][$idist]['val'];
	}
	else
	{
		$avgcolor = "";
		$avg = "-";
		$medcolor = "";
		$med = "-";
	}

	echo "<td id='a_$idist' class='r_$idist jsavg jcol borderleft r_stat jQhidden' vcolor='#$avgcolor'>$avg</td>
		  <td id='m_$idist' class='r_$idist jsmed jcol r_stat jQhidden' vcolor='#$medcolor'>$med</td>
		</tr>\n";
}
?>
		</table>
	</div>
</div>

<script>
	var id = <?=$_GET['id']?>;
	var forstud = false;
</script>
<script src="js/common_register.js"></script>
<script src="js/class_show_stat.js"></script>

<?php show_postmain(); ?>
