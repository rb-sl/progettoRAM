<?php
// Pagina che permette l'aggiunta di nuovi test
include $_SERVER['DOCUMENT_ROOT']."/librerie/general.php";
chk_access(1);
connect();
show_premain("Aggiungi test");

$ttest=query("SELECT * FROM TIPOTEST ORDER BY nomet");
$ctest=query("SELECT * FROM CLTEST ORDER BY nomec");
$retunita=query("SELECT * FROM UNITA ORDER BY udm");
?>
<h2>Aggiungi nuovo test</h2>
<form method="POST" action="./in_test.php">
	<table class='table table-striped'>
    	<tr>
        	<td>Nome test:</td>
        	<td style='width:50%'><input type="text" name="nometest"></td>
		<tr>
    		<td>Tipo di test:</td>
    		<td>
        		<select name="classe">
                	<option selected='selected' disabled>
<?php
while($row=$ctest->fetch_assoc())
	echo "<option value='".$row['id_cltest']."'>".$row['nomec']."</option>";
?>
        		</select>
    		</td>
		</tr>
		<tr>
    		<td>Unità di misura:</td>
    		<td>
    			<select name='unita' required>
                	<option selected='selected' disabled>
<?php
while($row=$retunita->fetch_assoc())
	echo "<option value='".$row['id_udm']."'>".$row['udm']."</option>";
?>
    			</select>
    		</td>
		</tr>
		<tr>
	    	<td>Valori migliori:</td>
    		<td>
        		<select name="pos" required>
                	<option selected='selected' disabled>
            		<option value="Maggiori">Maggiori</option>
            		<option value="Minori">Minori</option>
        		</select>
			</td>
    	</tr>
		<tr>
	    	<td>Tipo di valori:</td>
    		<td>
        		<select name="tipo" required>
                	<option selected='selected' disabled>
<?php
while($row=$ttest->fetch_assoc())
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
	<input type="submit" id="submit" class="btn btn-warning" value="Inserisci test">
</form>

<?php show_postmain(); ?>