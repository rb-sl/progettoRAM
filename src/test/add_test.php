<?php
// Page to add new tests
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
chk_access(1);
connect();
show_premain("Aggiungi test");

// Preparing statements
$test_st = prepare_stmt("SELECT * FROM TIPOTEST ORDER BY nomet");
$clt_st = prepare_stmt("SELECT * FROM CLTEST ORDER BY nomec");
$unit_st = prepare_stmt("SELECT * FROM UNITA ORDER BY udm");
?>

<h2>Aggiungi nuovo test</h2>
<form method="POST" action="./in_test.php">
	<table class="table table-striped">
    	<tr>
        	<td>Nome test:</td>
        	<td class="halfpage"><input type="text" name="nometest"></td>
		<tr>
    		<td>Tipo di test:</td>
    		<td>
        		<select name="classe">
                	<option selected="selected" disabled>
<?php
$ctest = execute_stmt($clt_st);
while($row = $ctest->fetch_assoc())
	echo "<option value='".$row['id_cltest']."'>".$row['nomec']."</option>";
?>
        		</select>
    		</td>
		</tr>
		<tr>
    		<td>Unità di misura:</td>
    		<td>
    			<select name="unita" required>
                	<option selected="selected" disabled>
<?php
$retunita = execute_stmt($unit_st);
while($row = $retunita->fetch_assoc())
	echo "<option value='".$row['id_udm']."'>".$row['udm']."</option>";
?>
    			</select>
    		</td>
		</tr>
		<tr>
	    	<td>Valori migliori:</td>
    		<td>
        		<select name="pos" required>
                	<option selected="selected" disabled>
            		<option value="Maggiori">Maggiori</option>
            		<option value="Minori">Minori</option>
        		</select>
			</td>
    	</tr>
		<tr>
	    	<td>Tipo di valori:</td>
    		<td>
        		<select name="tipo" required>
                	<option selected="selected" disabled>
<?php
$ttest = execute_stmt($test_st);
while($row = $ttest->fetch_assoc())
	echo "<option value='".$row['id_tipot']."'>".$row['nomet']."</option>";
?>
        		</select>
    		</td>
		</tr>
	</table>
	<div>
    	<h3>Informazioni aggiuntive</h3>
    	Posizione:<br>
    	<textarea class="txt" name="posiz"></textarea>
    	<br>
    	Materiale aggiuntivo:<br>
    	<textarea class="txt" name="equip"></textarea>
    	<br>
    	Esecuzione:<br>
    	<textarea class="txt" name="esec"></textarea>
    	<br>
    	Consigli per l'insegnante:<br>
    	<textarea class="txt" name="cons"></textarea>
    	<br>
    	Limite:<br>
    	<textarea class="txt" name="limite"></textarea>
    	<br>
    	Valutazione:<br>
    	<textarea class="txt" name="valut" required></textarea>
	</div>
	<input type="submit" id="submit" class="btn btn-warning marginunder" value="Inserisci test">
</form>

<?php show_postmain(); ?>