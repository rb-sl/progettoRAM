<?php
// Units of measure management page
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
chk_access(ADMINISTRATOR);
connect();
show_premain("Unità");

// Gets the system's unit and the number of associated tests
$unit_st = prepare_stmt("SELECT id_udm, udm, simbolo, (
		SELECT COUNT(*) AS n FROM UNITA
		JOIN TEST ON fk_udm=id_udm
		WHERE id_udm=U.id_udm
	) AS n FROM UNITA AS U
	LEFT JOIN TEST ON fk_udm=id_udm
	GROUP BY id_udm 
    ORDER BY udm");
$ret = execute_stmt($unit_st);
$unit_st->close();
?>

<h2>Gestione delle unità di misura</h2>

<form action="/admin/unit_update.php" method="POST" class="tdiv">
    <button type="button" id="newrow" class="btn btn-primary">Aggiungi nuova</button>

    <div class="inner">
        <table id="datatable" class="table table-light table-striped studtable">
            <tr>
                <th>Nome unità</th>
                <th>Simbolo</th>
                <th class="col"></th>
            </tr>
<?php
while($row = $ret->fetch_assoc())
{
    echo "  <tr>
                <td id='c1_".$row['id_udm']."'>".$row['udm']."</td>
                <td id='c2_".$row['id_udm']."'>".$row['simbolo']."</td>
                <td><div><button type='button' id='mod_".$row['id_udm']."' class='btn btn-warning btnmenu mod'>Modifica</button>"; 

	if($row['n'] == 0)
		echo "<a href='unit_delete.php?id=".$row['id_udm']."' class='btn btn-danger btnmenu'"
			.confirm("L'unità di misura ".$row['udm']." sarà eliminata").">Elimina</a>";

	echo  "</div></td>
		</tr>";
}
?>
        </table>
    </div>

    <input type="submit" id="submit" class="btn btn-primary jQhidden" value="Salva">
</form>

<script>
var col2type = "text"
</script>
<script src="/admin/js/tables.js"></script>

<?php show_postmain(); ?>
