<?php 
include $_SERVER['DOCUMENT_ROOT']."/librerie/general.php";
include $_SERVER['DOCUMENT_ROOT']."/librerie/lib_reg.php";
chk_access(2);
connect();

$ret=query("SELECT * FROM CLASSI WHERE id_cl=".$_GET['id']);
$cl=$ret->fetch_assoc();

show_premain("Modifica ".$cl['classe'].$cl['sez']." ".$cl['anno']."/".($cl['anno']+1));
?>

<h2>Modifica Classe <a href="show_classe.php?id=<?=$_GET['id']?>" class="btn btn-primary">Fine</a></h2>
<form id="frm" method="POST" action="up_classe.php?id=<?=$_GET['id']?>">
<?php show_cl_form($cl['classe'],$cl['sez'],$cl['anno']); ?>
	<h3>Modifica studenti</h3>
	<div id="divpro">
<?php echo build_chk_table($_GET['id']); ?>
	</div>

	<h3>Aggiungi studenti</h3>
	<table id="tabadd" class="table table-striped" style="max-width:500px">
		<tr id="r0">
			<td class="col"><input type="text" id="c0" name="lcst[0]" class="last n0" placeholder="Cognome"></td>
			<td class="col"><input type="text" id="nm0" class="n0" name="nst[0]" placeholder="Nome"></td>
			<td class="col">
				<label><input id="m0" class="n0" type="radio" name="sesso[0]" value="m">M</label>
				<label><input id="f0" class="n0" type="radio" name="sesso[0]" value="f">F</label>
			</td>
		</tr>
	</table>

	<div id="ext" style="display:none">
		<h3>Possibili studenti già registrati:</h3>
    	<table class='table table-striped' style="max-width:500px">
        	<tbody id='tabext'>
            </tbody>
        </table>
	</div>
	<div style="max-width:500px;margin:0 auto">
		<input type="submit" value="Aggiorna classe" class="btn btn-warning top-bot-margin" style="width:75%"><a href="del_classe.php?id=<?=$_GET['id']?>" class="btn btn-danger" style="width:25%" <?=confirm("La classe ".$cl['classe'].$cl['sez']." ".$cl['anno']."/".($cl['anno']+1)." e le prove ad essa attinenti saranno eliminate")?>>Elimina classe</a>
	</div>
</form>

<script src="/librerie/script_reg.js"></script>

<?php show_postmain(); ?>