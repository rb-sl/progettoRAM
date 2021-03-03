<?php
// Frontend for test update
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
chk_access(1);
connect();

// If the test does not exist an error is shown to the user
$test_st = prepare_stmt("SELECT * FROM TEST WHERE id_test=?");
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

show_premain("Modifica test");

// Getting test associated info
$type_st = prepare_stmt("SELECT * FROM TIPOTEST ORDER BY nomet");
$ttest = execute_stmt($type_st);
$type_st->close();

$class_st = prepare_stmt("SELECT * FROM CLTEST ORDER BY nomec");
$ctest = execute_stmt($class_st);
$class_st->close();

$unit_st = prepare_stmt("SELECT * FROM UNITA ORDER BY udm");
$retunit = execute_stmt($unit_st);
$unit_st->close();
?>

<form method="POST" action="./test_update.php?id=<?=$_GET['id']?>" class="marginunder">
	<h2>
		Modifica <input type="text" value="<?=quoteHTML($test['nometest'])?>" name="testname" required> 
		<a href="./test_show.php?id=<?=$_GET['id']?>" class="btn btn-warning">Annulla</a> 
		<a href="./test_delete.php?id=<?=$_GET['id']?>"
			<?=confirm("Il test '".$test['nometest']."' sarà eliminato")?> class="btn btn-danger">Elimina test</a>
	</h2>
	<table class="table table-striped">
		<tr>
    		<td>Tipo di test:</td>
    		<td class="halfpage">
        		<select name="class" required>
<?php
while($row = $ctest->fetch_assoc())
{
	echo "<option value='".$row['id_cltest']."'";
	if($row['id_cltest'] == $test['fk_cltest'])
    	echo " selected='selected'";
	echo ">".$row['nomec']."</option>";
}
?>
        		</select>
    		</td>
		</tr>
		<tr>
    		<td>Unità di misura:</td>
    		<td>
    			<select name="unit" required>
<?php
while($row = $retunit->fetch_assoc())
{
	echo "<option value='".$row['id_udm']."'";
	if($row['id_udm'] == $test['fk_udm'])
    	echo " selected='selected'";
	echo ">".$row['udm']."</option>";
}       

if($test['pos'] == "Maggiori")
{
	$g = " selected='selected'";
	$m = "";
}
else
{
	$g = "";
	$m = " selected='selected'";
}
?>
    			</select>
    		</td>
		</tr>
		<tr>
	    	<td>Valori migliori:</td>
    		<td>
        		<select name="positive" required>
            		<option value="Maggiori" <?=$g?>>Maggiori</option>
            		<option value="Minori" <?=$m?>>Minori</option>
        		</select>
			</td>
    	</tr>
		<tr>
	    	<td>Tipo di valori:</td>
    		<td>
        		<select name="type" required>
<?php
while($row=$ttest->fetch_assoc())
{
	echo "<option value='".$row['id_tipot']."'";
	if($row['id_tipot'] == $test['fk_tipot'])
    	echo " selected='selected'";
	echo ">".$row['nomet']."</option>";
}
?>
        		</select>
    		</td>
		</tr>
	</table>
	<div>
    	<h3>Informazioni aggiuntive</h3>
    	Posizione:<br>
    	<textarea class="txt" name="position"><?=$test['posiz']?></textarea>
    	<br>
    	Materiale aggiuntivo:<br>
    	<textarea class="txt" name="equipment"><?=$test['equip']?></textarea>
    	<br>
    	Esecuzione:<br>
    	<textarea class="txt" name="execution"><?=$test['esec']?></textarea>
    	<br>
    	Consigli:<br>
    	<textarea class="txt" name="suggestions"><?=$test['cons']?></textarea>
    	<br>
    	Limite:<br>
    	<textarea class="txt" name="limit"><?=$test['limite']?></textarea>
    	<br>
    	Valutazione:<br>
    	<textarea class="txt" name="grading" required><?=$test['valut']?></textarea>
	</div>
	<input type="submit" id="submit" class="btn btn-warning marginunder" value="Aggiorna valori test">
</form>

<?php show_postmain(); ?>