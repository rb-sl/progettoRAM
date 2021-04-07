<?php
// Page to add new tests
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
chk_access(PROFESSOR_GRANTS);
connect();
show_premain("Aggiungi test");

// Preparing statements for selects
$test_st = prepare_stmt("SELECT * FROM TIPOTEST ORDER BY nomet");
$clt_st = prepare_stmt("SELECT * FROM CLTEST ORDER BY nomec");
$unit_st = prepare_stmt("SELECT * FROM UNITA ORDER BY udm");
?>

<h2>Aggiungi nuovo test</h2>
<form method="POST" action="./test_insert.php">
	<table class="table table-light table-striped">
    	<tr>
        	<td>Nome test:</td>
        	<td class="halfwidth"><input type="text" name="testname"></td>
		<tr>
    		<td>Tipo di test:</td>
    		<td>
        		<select name="class" class="form-control">
                	<option selected="selected" disabled>
<?php
$ctest = execute_stmt($clt_st);
$clt_st->close();

while($row = $ctest->fetch_assoc())
	echo "<option value='".$row['id_cltest']."'>".$row['nomec']."</option>";
?>
        		</select>
    		</td>
		</tr>
		<tr>
    		<td>Unità di misura:</td>
    		<td>
    			<select name="unit" class="form-control" required>
                	<option selected="selected" disabled>
<?php
$retunita = execute_stmt($unit_st);
$unit_st->close();

while($row = $retunita->fetch_assoc())
	echo "<option value='".$row['id_udm']."'>".$row['udm']."</option>";
?>
    			</select>
    		</td>
		</tr>
		<tr>
	    	<td>Valori migliori:</td>
    		<td>
        		<select name="positive"  class="form-control" required>
                	<option selected="selected" disabled>
            		<option value="Maggiori">Maggiori</option>
            		<option value="Minori">Minori</option>
        		</select>
			</td>
    	</tr>
		<tr>
	    	<td>Tipo di valori:</td>
    		<td>
        		<select name="type" class="form-control" required>
                	<option selected="selected" disabled>
<?php
$ttest = execute_stmt($test_st);
$test_st->close();

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
    	<textarea class="txt" name="position"></textarea>
    	<br>
    	Materiale aggiuntivo:<br>
    	<textarea class="txt" name="equipment"></textarea>
    	<br>
    	Esecuzione:<br>
    	<textarea class="txt" name="execution"></textarea>
    	<br>
    	Consigli per l'insegnante:<br>
    	<textarea class="txt" name="suggestions"></textarea>
    	<br>
    	Limite:<br>
    	<textarea class="txt" name="limit"></textarea>
    	<br>
    	Valutazione:<br>
    	<textarea class="txt" name="grading" required></textarea>
	</div>
	<input type="submit" id="submit" class="btn btn-warning marginunder" value="Inserisci test">
</form>

<?php show_postmain(); ?>
