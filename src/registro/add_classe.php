<?php 
include $_SERVER['DOCUMENT_ROOT']."/librerie/general.php";
include $_SERVER['DOCUMENT_ROOT']."/librerie/lib_reg.php";
chk_access(2);
connect();
show_premain("Aggiunta classe");

// Calcolo dell'a.s. precedente; se ci si trova nel I semestre consiste nell'anno - 1, se nel secondo nell'anno - 2
// Agosto è preso come spartiacque
$y=date('Y');
if(date("m")<8)
	$y--;
?>
<h2>Aggiungi Classe</h2>

<div>
	<button id="prom" class="btn btn-primary">Promuovi classe precedente</button>
	<div class="dpro" style="display:none">
    	Classe da promuovere: 
    	<select id="clpr" class="form-control">
			<option selected disabled></option>
<?php
if($_SESSION['priv']>0)
	$nad="fk_prof=".$_SESSION['id']." AND";

// Selezione di tutte le classi dell'anno precedente dello user (o tutte in caso di amministratore)
// che ancora non hanno avuto una classe successiva        
$ret=query("SELECT C1.id_cl,C1.classe,C1.sez FROM 
	(SELECT id_cl,classe,sez,anno FROM CLASSI WHERE $nad anno=".($y-1)." AND fk_scuola=".$_SESSION['scuola']." AND classe<>5) AS C1
	LEFT JOIN
	(SELECT id_cl,classe-1 AS classe,sez,anno-1 AS anno FROM CLASSI WHERE anno=$y AND fk_scuola=".$_SESSION['scuola'].") AS C2
	USING (classe,sez,anno) 
    WHERE C2.id_cl IS NULL 
    ORDER BY classe,sez");
while($row=$ret->fetch_assoc())
	echo "<option value='".$row['id_cl']."'>".$row['classe'].$row['sez']."</option>";
?>
		</select>
	</div>
</div>

<form id="frm" method="POST" action="/registro/in_classe.php">
<?php show_cl_form(); ?>
	<h3 class="dpro" style="display:none">Studenti promossi nella nuova classe:</h3>
	<div id="divpro" class="dpro" style="display:none">-</div>

	<h3>Nuovi studenti:</h3>
	<table id="tabadd" class="table table-striped" style='width:500px'>
    	<tr id="r0">
        	<td><input type="text" id="c0" name="lcst[0]" class="last n0" placeholder="Cognome"></td>
        	<td><input type="text" id="nm0" class="n0" name="nst[0]" placeholder="Nome"></td>
        	<td>
            	<label><input id="m0" class="n0" type="radio" name="sesso[0]" value="m">M</label>
            	<label><input id="f0" class="n0" type="radio" name="sesso[0]" value="f">F</label>
        	</td>
        </tr>
  	</table>
	
	<div id="ext" style="display:none">
		<h3>Possibili studenti già registrati:</h3>
    	<table class='table table-striped' style='width:500px'>
        	<tbody id='tabext'>
            </tbody>
        </table>
	</div>
	
	<input type="submit" value="Inserisci classe" class="btn btn-warning top-bot-margin">
</form>


<script>
$(function(){
	$("#prom").click(function(){
    	$(this).hide();
    	$(".dpro").show();
    });
	
	$("#clpr").change(function(){
    	$.ajax({                                      
        	url: "aj_prom.php",
        	data: "toprom="+$("#clpr").val(),
        	dataType: "json",
        	async: false,
        	success: function(data){
            	$("#cl").val(data['cl']);
            	$("#sez").val(data['sez']);
            	$("#a1").val(data['anno']);
            	$("#flwa1").text(parseInt(data['anno'])+1);
            	
          		$("#divpro").html(data['list']);
        	},
        	error: function(){
        		alert("Errore ajax");
      		},
        	timeout: 5000
      	});
    });
});
</script>

<script src="/librerie/script_reg.js"></script>

<?php show_postmain(); ?>