<?php
// Front end page to display a test's statistics
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
include $_SERVER['DOCUMENT_ROOT']."/libraries/lib_stat.php";
chk_access(3);
connect();

$test_st = prepare_stmt("SELECT * FROM TEST JOIN UNITA ON fk_udm=id_udm WHERE id_test=?");
$test_st->bind_param("i", $_GET['id']);
$ret = execute_stmt($test_st);
$test_st->close();

$test = $ret->fetch_assoc();
show_premain("Statistiche ".$test['nometest'], true);

$data = get_stats($_GET['id']);
$records = get_records($_GET['id']);
$graph = graph_vals($_GET['id']);
?>

<h2>Statistiche <span id="nomet"><?=$test['nometest']?></span></h2>

<table class="table table-light table-striped marginunder">
   	<tr><td>Numero totale di prove: <span id="n"><?=$data['n']?></span></td>
   	<tr><td>Media: <span id="avg"><?=number_format($data['avg'], 2)?></span> <?=$test['simbolo']?></td></tr>
	<tr><td>Mediana: <span id="med"><?=number_format($data['med'], 2)?></span> <?=$test['simbolo']?></td></tr>
   	<tr><td>Deviazione Standard: <span id="std"><?=number_format($data['std'], 2)?></span> <?=$test['simbolo']?></td></tr>	
</table>

<h3 style="margin-bottom:0px">
	Record positivo: 
	<span id="best"><?=$records['best']?></span> 
	<?=$test['simbolo']?>
</h3>

<?=$records['list']?>

<h3 class="section">
	Record negativo: 
	<span id="worst"><?=$records['worst']?></span>
	<?=$test['simbolo']?>
</h3>

<h3>
	Grafico: 
	<select id="graph" class="form-control trigger">
    	<option value="val">Valori</option>
    	<option value="box">Box plot</option>
    	<option value="hbox">Box plot (Anni)</option>
    	<option value="cbox">Box plot (Classi)</option>
	    <option value="sbox">Box plot (Sesso)</option>
    	<option value="prc">Valori percentili</option>
	</select>
</h3>

<!-- Grafico -->
<div id="cnv">
</div>

<script src="/statistics/js/test_stats.js"></script>
<script>
var id  = <?=$_GET['id']?>;
var vals = <?=json_encode($graph['vals']);?>;
   			
draw_graph_val(vals);  
</script>

<?php show_postmain(); ?>
