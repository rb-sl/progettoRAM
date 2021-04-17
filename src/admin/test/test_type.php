<?php
// Test types management page
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
chk_access(ADMINISTRATOR);
connect();
show_premain("Tipi dei dati per test");

// Gets the system's test types and the number of associated tests
$unit_st = prepare_stmt("SELECT id_tipot, nomet, passo, (
		SELECT COUNT(*) AS n FROM TIPOTEST
		JOIN TEST ON fk_tipot=id_tipot
		WHERE id_tipot=T.id_tipot
	) AS n FROM TIPOTEST AS T
	LEFT JOIN TEST ON fk_tipot=id_tipot
	GROUP BY id_tipot 
	ORDER BY nomet");
$ret = execute_stmt($unit_st);
$unit_st->close();
?>

<h2>Gestione dei tipi di dati dei test</h2>

<form action="/admin/test/test_type_update.php" method="POST" class="tdiv">
	<button type="button" id="newrow" class="btn btn-primary">Aggiungi nuovo</button>

	<div class="inner">
		<table id="datatable" class="table table-light table-striped studtable">
			<tr>
				<th>Tipo dei dati</th>
				<th>Passo</th>
				<th class="col"></th>
			</tr>
<?php
while($row = $ret->fetch_assoc())
{
	echo "  <tr>
				<td id='c1_".$row['id_tipot']."'>".$row['nomet']."</td>
				<td id='c2_".$row['id_tipot']."'>".$row['passo']."</td>
				<td>
					<div>
						<button type='button' id='mod_".$row['id_tipot']."' class='btn btn-warning btnmenu mod'>
							Modifica
						</button>"; 

	if($row['n'] == 0)
		echo "<a href='test_type_delete.php?id=".$row['id_tipot']."' class='btn btn-danger btnmenu'"
			.confirm("Il tipo di test ".$row['nomet']." passo ".$row['passo']." sarà eliminato").">Elimina</a>";

	echo  "         </div>
				</td>
			</tr>";
}
?>
		</table>
	</div>

	<input type="submit" id="submit" class="btn btn-primary jQhidden" value="Salva">
</form>

<script>
var col2type = "number"
</script>
<script src="/admin/js/tables.js"></script>

<?php show_postmain(); ?>
