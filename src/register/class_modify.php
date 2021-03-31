<?php 
// Front end page to modify an already existing class
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
include $_SERVER['DOCUMENT_ROOT']."/libraries/lib_reg.php";
chk_access(2);
connect();

$cl = get_class_info($_GET['id']);
chk_prof($cl['fk_prof']);

show_premain("Modifica ".$cl['classe'].$cl['sez']." ".$cl['anno']."/".($cl['anno'] + 1));
?>

<h2>Modifica Classe <a href="class_show.php?id=<?=$_GET['id']?>" class="btn btn-warning">Annulla</a></h2>

<form id="frm" method="POST" action="class_update.php?id=<?=$_GET['id']?>">
<?php show_cl_form($cl['classe'], $cl['sez'], $cl['anno']); ?>
	<a href="class_delete.php?id=<?=$_GET['id']?>" class="btn btn-danger"
		<?=confirm("La classe ".$cl['classe'].$cl['sez']." ".$cl['anno']."/".($cl['anno'] + 1)
			." e le prove ad essa attinenti saranno eliminate")?>>Elimina classe</a>
	<h3>Modifica studenti</h3>
	<div id="divpro">
<?php echo build_chk_table($_GET['id']); ?>
	</div>

	<h3>Aggiungi studenti</h3>
	<div class="tdiv">
  		<div id="tos" class="innerx">
			<table id="tabadd" class="table table-striped studtable">
				<tr id="r0">
					<td class="col"><input type="text" id="c0" name="lcst[0]" class="last n0" placeholder="Cognome"></td>
					<td class="col"><input type="text" id="nm0" class="n0" name="nst[0]" placeholder="Nome"></td>
					<td class="col">
						<label><input id="m0" class="n0" type="radio" name="sesso[0]" value="m">M</label>
						<label><input id="f0" class="n0" type="radio" name="sesso[0]" value="f">F</label>
					</td>
				</tr>
			</table>
		</div>
	</div>

	<div id="ext" class="jQhidden">
		<h3>Possibili studenti già registrati:</h3>
    	<table class="table table-striped" class="studtable">
        	<tbody id="tabext">
            </tbody>
        </table>
	</div>

	<input type="submit" value="Aggiorna classe" class="btn btn-warning top-bot-margin">
</form>

<script src="/register/js/class_input.js"></script>

<?php show_postmain(); ?>