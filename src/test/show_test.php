<?php
// Pagina per la visualizzazione dei dati sui test
include $_SERVER['DOCUMENT_ROOT']."/librerie/general.php";
chk_access(2);
connect();

$rettest=query("SELECT * FROM TEST,UNITA,TIPOTEST,CLTEST WHERE id_test=".$_GET['id']." AND fk_udm=id_udm AND fk_tipot=id_tipot AND fk_cltest=id_cltest");
$test=$rettest->fetch_assoc();
show_premain($test['nometest']);
?>
<h2>Informazioni <?=$test['nometest']?></h2>

<table class='table table-striped'>
	<tr>
    	<td>Classe del test:</td>
    	<td style='width:50%'><?=$test['nomec']?></td>
	</tr>
	<tr>
    	<td>Unità di misura:</td>
    	<td><?=$test['udm']?></td>
	</tr>
	<tr>
    	<td>Valori migliori:</td>
    	<td><?=$test['pos']?>
		</td>
	</tr>
	<tr>
    	<td>Tipo di valori:</td>
    	<td><?=$test['nomet']?></td>
	</tr>
	<tr>
    	<td>Sensibilità del test:</td>
    	<td><?=$test['passo']." ".$test['simbolo']?></td>
	</tr>
	<tr>
    	<th colspan='2' style='text-align:center'>Informazioni aggiuntive</th>
	</tr>
	<tr>
    	<td>Posizione:</td>
    	<td><?=($test['posiz'] ? $test['posiz'] : "-")?></td>
	</tr>
	<tr>
    	<td>Materiale aggiuntivo:</td>
    	<td><?=($test['equip'] ? $test['equip'] : "-")?></td>
	</tr>
	<tr>
    	<td>Esecuzione:</td>
    	<td><?=($test['esec'] ? $test['esec'] : "-")?></td>
	</tr>
	<tr>
	    <td>Consigli:</td>
	    <td><?=($test['cons'] ? $test['cons'] : "-")?></td>
	</tr>
	<tr>
	    <td>Limite:</td>
	    <td><?=($test['limite'] ? $test['limite'] : "-")?></td>
	</tr>
	<tr>
	    <td>Valutazione:</td>
	    <td><?=($test['valut'] ? $test['valut'] : "-")?></td>
	</tr>
</table>

<?php
if($_SESSION[priv]==0)
	echo "<div><a href='./mod_test.php?id=".$_GET['id']."' class='btn btn-warning'>Modifica test</a></div>";
show_postmain();
?>