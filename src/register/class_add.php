<?php 
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
include $_SERVER['DOCUMENT_ROOT']."/libraries/lib_reg.php";
chk_access(2);
connect();
show_premain("Aggiunta classe");

// Calculation of the previous school year; for the first quadrimester it is the year - 1,
// for the second year - 2. The division is done for august
$year = date("Y");
if(date("m") < 8)
	$year--;
?>

<h2>Aggiungi Classe</h2>

<div>
	<div class="marginunder">
		<button id="prom" class="btn btn-primary">Promuovi classe precedente</button>
	
		<div class="dpro jQhidden">
			Classe da promuovere: 
			<select id="clpr" class="form-control">
				<option selected disabled></option>
<?php
if($_SESSION['priv'] > 0)
	$nad = "fk_prof=? AND";
else
	$nad = "";

// Gets all user's classes of the previous year that do not have a following class yet
// An administrator can promote every class
$lastyear = $year - 1; 
$prom_st = prepare_stmt("SELECT C1.id_cl, C1.classe, C1.sez FROM 
	(SELECT id_cl, classe, sez, anno FROM CLASSI WHERE $nad anno=? AND fk_scuola=? AND classe<>5) AS C1
	LEFT JOIN
	(SELECT id_cl, classe-1 AS classe, sez, anno-1 AS anno FROM CLASSI WHERE anno=? AND fk_scuola=?) AS C2
	USING (classe, sez, anno) 
	WHERE C2.id_cl IS NULL 
	ORDER BY classe, sez");

if($_SESSION['priv'] == 0)
	$prom_st->bind_param("iiii", $lastyear, $_SESSION['scuola'], $year, $_SESSION['scuola']);
else
	$prom_st->bind_param("iiiii", $_SESSION['id'], $lastyear, $_SESSION['scuola'], $year, $_SESSION['scuola']);

$ret = execute_stmt($prom_st);
$prom_st->close();

while($row = $ret->fetch_assoc())
	echo "<option value='".$row['id_cl']."'>".$row['classe'].$row['sez']."</option>";
?>
			</select>
		</div>
	</div>
</div>

<form id="frm" method="POST" action="/register/class_insert.php">
<?php show_cl_form(); ?>
	<h3 class="dpro jQhidden">Studenti promossi nella nuova classe:</h3>
	<div id="divpro" class="dpro jQhidden">-</div>

	<h3>Nuovi studenti:</h3>
	<div class="tdiv">
  		<div id="tos" class="innerx">
			<table id="tabadd" class="table table-light table-striped studtable">
				<tr id="r0">
					<td><input type="text" id="c0" name="lcst[0]" class="last n0" placeholder="Cognome"></td>
					<td><input type="text" id="nm0" class="n0" name="nst[0]" placeholder="Nome"></td>
					<td class="containerflex">
						<div class="form-check">
							<input id="m0" class="n0 form-check-input" type="radio" name="sesso[0]" value="m">
							<label class="form-check-label" for="m0">M</label>
						</div>
						<div class="form-check">
							<input id="f0" class="n0 form-check-input" type="radio" name="sesso[0]" value="f">
							<label class="form-check-label" for="f0">F</label>
						</div>
					</td>
				</tr>
			</table>
		</div>
	</div>
	
	<div id="ext" class="jQhidden">
		<h3>Possibili studenti già registrati:</h3>
    	<table class="table table-light table-striped studtable">
        	<tbody id="tabext">
            </tbody>
        </table>
	</div>
	
	<input type="submit" value="Inserisci classe" class="btn btn-warning top-bot-margin">
</form>

<script src="/register/js/class_input.js"></script>

<?php show_postmain(); ?>