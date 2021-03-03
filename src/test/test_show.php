<?php
// Test parameters visualization
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
chk_access(3);
connect();

// If the test does not exist an error is shown to the user
$test_st = prepare_stmt("SELECT * FROM TEST JOIN UNITA ON fk_udm=id_udm
	JOIN TIPOTEST ON fk_tipot=id_tipot
	JOIN CLTEST ON fk_cltest=id_cltest
	WHERE id_test=?");
$test_st->bind_param("i", $_GET['id']);

$rettest = execute_stmt($test_st);
$test_st->close();

if($rettest->num_rows == 0)
{
	$_SESSION['alert'] = "Errore: Test inesistente";
	header("Location: /test/test.php");
	exit;
}

$test = $rettest->fetch_assoc();

show_premain($test['nometest']);
?>
<h2>Informazioni <?=$test['nometest']?></h2>

<table class="table table-striped marginunder">
	<tr>
    	<td>Classe del test:</td>
    	<td class="halfpage"><?=$test['nomec']?></td>
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
    	<th colspan="2" class="textcenter">Informazioni aggiuntive</th>
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
	    <td><?=$test['valut']?></td>
	</tr>
</table>

<?php
if($_SESSION['priv'] <= 1)
	echo "<div class='marginunder'><a href='./test_modify.php?id=".$_GET['id']."' class='btn btn-warning'>Modifica test</a></div>";
	
show_postmain();
?>