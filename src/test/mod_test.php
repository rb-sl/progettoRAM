<?php
// Pagina front-end per la modifica dei test - solo amministratore
include $_SERVER['DOCUMENT_ROOT']."/librerie/general.php";
chk_access(1);
connect();
show_premain("Modifica test");

$rettest=query("SELECT * FROM TEST,UNITA,TIPOTEST,CLTEST WHERE id_test=".$_GET['id']." AND fk_udm=id_udm AND fk_tipot=id_tipot AND fk_cltest=id_cltest");
$test=$rettest->fetch_assoc();

if($test['pos']=='Maggiori')
	$g=" selected='selected'";
else
	$m=" selected='selected'";

$ttest=query("SELECT * FROM TIPOTEST ORDER BY nomet");
$ctest=query("SELECT * FROM CLTEST ORDER BY nomec");
$retunita=query("SELECT * FROM UNITA ORDER BY udm");
?>
<form method="POST" action="./up_test.php?id=<?=$_GET['id']?>">
	<h2>Modifica <input type="text" value="<?=$test['nometest']?>" name="nometest" required> <a href='./show_test.php?id=<?=$_GET['id']?>' class='btn btn-warning'>Indietro</a> <a href='./del_test.php?id=<?=$_GET['id']?>' <?=confirm("Il test ".$test['nometest']." sarà eliminato")?> class='btn btn-danger'>Elimina test</a></h2>
	<table class='table table-striped'>
		<tr>
    		<td>Tipo di test:</td>
    		<td style="width:50%">
        		<select name="classe" required>
<?php
while($row=$ctest->fetch_assoc())
{
	echo "<option value='".$row['id_cltest']."'";
	if($row['nomec']==$test['nomec'])
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
    			<select name='unita' required>
<?php
while($row=$retunita->fetch_assoc())
{
	echo "<option value='".$row['id_udm']."'";
	if($row['id_udm']==$test['fk_udm'])
    	echo " selected='selected'";
	echo ">".$row['udm']."</option>";
}       
?>
    			</select>
    		</td>
		</tr>
		<tr>
	    	<td>Valori migliori:</td>
    		<td>
        		<select name="pos" required>
            		<option value="Maggiori" <?=$g?>>Maggiori</option>
            		<option value="Minori" <?=$m?>>Minori</option>
        		</select>
			</td>
    	</tr>
		<tr>
	    	<td>Tipo di valori:</td>
    		<td>
        		<select name="tipo" required>
<?php
while($row=$ttest->fetch_assoc())
{
	echo "<option value='".$row['id_tipot']."'";
	if($row['nomet']==$test['nomet'])
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
    	<textarea class="txt" name="posiz"><?=$test['posiz']?></textarea>
    	<br>
    	Materiale aggiuntivo:<br>
    	<textarea class="txt" name="equip"><?=$test['equip']?></textarea>
    	<br>
    	Esecuzione:<br>
    	<textarea class="txt" name="esec"><?=$test['esec']?></textarea>
    	<br>
    	Consigli:<br>
    	<textarea class="txt" name="cons"><?=$test['cons']?></textarea>
    	<br>
    	Limite:<br>
    	<textarea class="txt" name="limite"><?=$test['limite']?></textarea>
    	<br>
    	Valutazione:<br>
    	<textarea class="txt" name="valut" required><?=$test['valut']?></textarea>
	</div>
	<input type="submit" id="submit" class="btn btn-warning" value="Aggiorna valori test">
</form>

<?php show_postmain(); ?>