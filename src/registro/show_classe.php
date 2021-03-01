<?php 
include $_SERVER['DOCUMENT_ROOT']."/librerie/general.php";
include $_SERVER['DOCUMENT_ROOT']."/librerie/lib_reg.php";
include $_SERVER['DOCUMENT_ROOT']."/librerie/lib_stat.php";
chk_access(2);
connect();

$ret=query("SELECT * FROM CLASSI WHERE id_cl=".$_GET['id']);
$cl=$ret->fetch_assoc();
chk_prof($cl['fk_prof']);

show_premain("Registro ".$cl['classe'].$cl['sez']." ".$cl['anno']."/".($cl['anno']+1));
?>
<h2>Registro della classe <?=$cl['classe'].$cl['sez']?> - Anno <?=$cl['anno']."/".($cl['anno']+1)?> <a href="mod_classe.php?id=<?=$_GET['id']?>" class="btn btn-warning btnmenu">Modifica</a></h2>

<div>
	<a href="show_classe_stat.php?id=<?=$_GET['id']?>" class="btn btn-primary btnmenu">Elaborazione dati della classe</a> 
</div>

<form action="in_prove.php?cl=<?=$_GET['id']?>" id="frm" method="POST">
	<button type="button" id="btnadd" class="btn btn-warning btnmenu">Aggiungi test</button>
	<button type="button" id="btncan" class="btn btn-danger btnmenu" style="display: none;">Annulla</button>
	<input type="submit" id="btncar" class="btn btn-warning btnmenu" style="display: none;" value="Salva">
     
	<div class="tdiv">
  		<div id="tos" class="inner">
    		<table id="tts" class="table table-striped">
      			<tr id="thr" class="dat"><td class="topleft topfix leftfix"><button type="button" id="btnstat" class="btn overpad wtot">Medie e mediane</button></td>
<?php
$rstud=col_stud();

// Costruzione del corpo della tabella con i valori delle prove
$retprove=query("SELECT * FROM PROVE,ISTANZE,TEST,UNITA 
	WHERE fk_ist=id_ist 
	AND fk_udm=id_udm 
	AND fk_test=id_test 
	AND fk_cl=".$_GET['id']);
while($row=$retprove->fetch_assoc())
{
	$vals[$row['fk_test']][]=$row['valore'];
	$rstud[$row['id_ist']][$row['fk_test']]="title='".$row['data']."'>".$row['valore']." ".$row['simbolo']."</td";
}

$ret=query("SELECT id_test,nometest,MIN(data),simbolo,ROUND(AVG(valore),2) as avg,ROUND(STD(valore),2) as std
	FROM TEST,PROVE,STUDENTI,ISTANZE,UNITA
	WHERE fk_cl=".$_GET['id']."
	AND fk_test=id_test 
	AND fk_udm=id_udm
	AND fk_ist=id_ist
	AND fk_stud=id_stud 
	GROUP BY id_test
	ORDER BY data,id_test ASC");

// Stampa delle righe di test, medie e mediane
while($row=$ret->fetch_assoc())
{
  	echo "<td id='c".$row['id_test']."' class='col topfix'>".$row['nometest']."</td>";
	$idtest[]=$row['id_test'];
	$ravg.="<td id='r".$row['id_test']."'>".$row['avg']." ".$row['simbolo']."</td>";	
	$rmed.="<td id='r".$row['id_test']."' style='border-bottom:1px solid black'>".arr_med($vals[$row['id_test']],2)." ".$row['simbolo']."</td>";
}
echo "</tr>
	<tr class='dat r_stat' style='display: none;'><td style='background-color:#eee' class='leftfix'>Medie:</td>$ravg</tr>
    <tr class='dat r_stat' style='display: none;'><td style='background-color:#f9f9f9' class='leftfix'>Mediane:</td>$rmed</tr>";

foreach($rstud as $idist => $tds)
{
	echo $tds['strow'];
	foreach($idtest as $idt)
   	{
    	echo "<td id='$idist"."_$idt' class='jdat r_$idist c_$idt'";
   		if($tds[$idt])
       		echo $tds[$idt];
    	echo "></td>";
   	}
	echo "</tr>\n";
}
?>
			</table>
    	</div>
	</div>
</form>

<script>
$(function(){
	// Funzione per ricevere la lista dei test non ancora effettuati dalla classe
    $("#btnadd").click(function(){
      	$.ajax({                                      
        	url: "aj_tslist.php",
			data: "id="+<?=$_GET['id']?>,
        	dataType: "json",
        	async: false,
        	success: function(data){
          		$("#thr").append("<td class='new col topfix' style='width:250px !important'><select id='test' name='test' class='form-control' required><option selected disabled></option>"+data+"</select></td>");
            	$(".tdr").each(function(){
        			$(this).append("<td class='new'><input type='number' id='n"+$(this).attr("id").substr(2)+"' class='in_add input' name='ntest["+$(this).attr("id").substr(2)+"]' style='width:70px' pattern='^[+-]?\\d+(\\.\\d+)?$'> <span class='udm'></span></td>");
      			});
				
				$("#tos").scrollLeft($("#tts").width() - $(window).width());
            
            	$("#btnadd").hide();            	
            	$("#btncar").show();
            	$("#btncan").show();
        	},
        	error: function(){
        		alert("Errore ajax");
      		},
        	timeout: 5000
      	});
	});

	$("#btncan").click(function(){
    	$(".new").remove();
    	$(".in_mod").each(function(){
        	$(this).closest("td").html($(this).attr("prev"));
        });
                          
    	$("#btnadd").show();            	
        $("#btncar").hide();
        $("#btncan").hide();
    });
     
	$(document).on("change","#test",function(){
		$.ajax({                                      
			url: "aj_udm.php",   
			data: "test="+$("#test").val(), 
			dataType: "json",                
			success: function(data){
        		$(".udm").html(data['simbolo']);
            	$(".in_add").attr("step",data['passo'])
      		},
        	error: function(){
        		alert("Errore test");
      		},
        	timeout: 5000
		});
	});
    
	$(document).on("dblclick",".jdat",function(){
    	if($(this).html().indexOf("input")===-1){
			var inner=$(this).html().split(" ");
        	var test=$(this).attr("id").substr($(this).attr("id").indexOf("_")+1);
        	var stud=$(this).attr("id").substr(0,$(this).attr("id").indexOf("_"));
        	var step;

    		// Richiesta dell'unità sempre per avere il passo
    		$.ajax({                                      
				url: "aj_udm.php",   
				data: "test="+test,
				dataType: "json",
                async: false, // Per permettere l'handling sincrono e mostrare l'unità di misura
				success: function(data){
					inner[1]=data['simbolo'];
                   	step=data['passo'];
      			},
        		error: function(){
         			alert("Errore ajax udm");
       			}
    		});
    	
        	// pattern permette di validare solo i dati del tipo +/- n.nn, step serve per validare il passo dei dati (definito in TIPOTEST)
    		$(this).html("<input type='number' size='5' class='in_mod input' name='pr["+test+"]["+stud+"]' id='i"+$(this).attr("id")+"' prev='"+$(this).html()+"' style='width:70px' value='"+inner[0]+"' pattern='^[+-]?\\d+(\\.\\d+)?$' step='"+step+"'> "+inner[1]);
        	
        	$("#btncar").show();
			$("#btncan").show();
        }
	});
	
	// Funzione per il controllo e l'inserimento delle prove
	$("#frm").on("submit",function(e){
    	$.ajax({
        	type: "POST",
			async: false,
        	url: "aj_prove.php",
        	data: $(this).serialize(),
        	dataType: "json",
        	success: function(data) {
            	if(jQuery.type(data)=="object"){        
					e.preventDefault();      

                	$.each(data['pr'],function(ist,test){
                    	$("#i"+ist+"_"+test).css("background-color","red");
                    	$("#i"+ist+"_"+test).css("color","white");
                    });
                
                	$.each(data['ntest'],function(test,id){
                    	$("#n"+id).css("background-color","red");
                    	$("#n"+id).css("color","white");
					});
					              	
                	alert("Alcuni dati non sono conformi ai valori presenti nel sistema.\nControllare l'inserimento. Per ulteriori informazioni, consultare il manuale");
                }
        	},
        	error: function(){
				e.preventDefault();  
  				alert("Errore ajax inserimento");
			}			
    	});
	});
	
});
</script>

<?php show_postmain(); ?>