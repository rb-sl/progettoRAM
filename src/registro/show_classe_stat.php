<?php 
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
include $_SERVER['DOCUMENT_ROOT']."/libraries/lib_stat.php";
include $_SERVER['DOCUMENT_ROOT']."/libraries/lib_reg.php";
chk_access(2);
connect();

$class_st = prepare_stmt("SELECT * FROM CLASSI WHERE id_cl=?");
$class_st->bind_param("i", $_GET['id']);

$ret = execute_stmt($class_st);
$cl = $ret->fetch_assoc();

chk_prof($cl['fk_prof']);

show_premain("Registro ".$cl['classe'].$cl['sez']." ".$cl['anno']."/".($cl['anno'] + 1), true);

// The page opens on the percentile option, so the relative colors are loaded
$color = get_color_prc();
?>

<h2>Registro della classe <?=$cl['classe'].$cl['sez']?> - Anno <?=$cl['anno']."/".($cl['anno'] + 1)?></h2>

<div>
	<a href="show_classe.php?id=<?=$_GET['id']?>" class="btn btn-primary btnmenu">Registro della classe</a>
	<h3>
    	Visualizzazione classe:
		<select id="vis" class="form-control">
        	<option value="prc">Valori percentili</option>
    		<option value="std">Valori standard</option>
   	 		<option value="vt">Voti</option>
		</select>
	</h3>
</div>

<div class="tdiv">
	<div class="inner">
    	<table class="table table-striped">
      		<tr id="thr" class="dat">
            	<td class="topleft leftfix topfix">
                	<button type="button" id="btnstat" class="btn overpad wtot">Medie e Mediane</button>
                	<br>
                	<button type="button" id="btncol" class="btn overpad wtot">Colori</button>
            	</td>
<?php
// Header construction
$ret = get_test();

$test['row'] = "";
while($row = $ret->fetch_assoc())
{
	$test['id'][] = $row['id_test'];
	$test['pos'][$row['id_test']] = $row['pos'];

	$test['row'] .= "<td id='c".$row['id_test']."' class='col topfix'>".$row['nometest']."</td>";
}

// Constuction of the table's body with percentile values
$rstud = get_prc_all($_GET['id'], $color);

// Calculates and constructs the rows and columns related to average and median for both tests and students 
$am = get_avgmed_prc($test['id'], $rstud['val'], $color);

$rowavg = "";
foreach($am['avg'] as $idt => $avg)
	$rowavg .= "<td id='at$idt' class='jcol jtavg' vcolor='#".$avg['color']."'>".$avg['val']."</td>";

$rowmed = "";
foreach($am['med'] as $idt => $med)
	$rowmed .= "<td id='mt$idt' class='jcol jtmed' vcolor='#".$med['color']."' style='border-bottom:1px solid black'>".$med['val']."</td>";

// Prints the table
echo $test['row']."
		<td class='col topfix r_stat evenrow jQhidden'>Media totale</td>
        <td id='tavg' class='col topfix r_stat jcol oddrow jQhidden' vcolor='#".$am['tavg']['color']."'>"
			.number_format($am['tavg']['val'], 5)."</td>
	</tr>
	<tr class='dat r_stat jQhidden'>
    	<td class='leftfix evenrow'>Medie</td>$rowavg
        <td rowspan='2' id='med1' class='col r_stat oddrow jQhidden'>Medie<br>studenti</td>
        <td rowspan='2' id='med2' class='col r_stat oddrow jQhidden'>Mediane<br>studenti</td>
    </tr>
	<tr class='dat r_stat jQhidden'>
    	<td class='leftfix oddrow'>Mediane</td>$rowmed
    </tr>";

$cstud = col_stud();
foreach($cstud as $idist => $stud)
{
	echo $stud['strow'];
	
	foreach($test['id'] as $idt)
	{
    	echo "<td id='$idist"."_$idt' class='jdat jcol r_$idist c_$idt'";
		if(isset($rstud['val'][$idist][$idt]))
		{
			if(isset($rstud['data'][$idist][$idt]) and $rstud['data'][$idist][$idt] != "0000-00-00")
				echo "title='".$rstud['data'][$idist][$idt]."'";

        	echo " vcolor='#".$rstud['color'][$idist][$idt]."'>"
				.$rstud['val'][$idist][$idt]."</td>";
		}
		else 
        	echo ">-</td>";
    }

	echo "<td id='a_$idist' class='r_$idist jsavg jcol lftbor r_stat jQhidden' vcolor='#".$am['savg'][$idist]['color']."'>"
		.$am['savg'][$idist]['val']
		."</td>
		  <td id='m_$idist' class='r_$idist jsmed jcol r_stat jQhidden' vcolor='#".$am['smed'][$idist]['color']."'>"
		.$am['smed'][$idist]['val']."</td></tr>\n";
}
?>
		</table>
	</div>
</div>

<script>
$(function(){
	$("#btncol").click(function(){
    	var app;
    	
    	$(".jcol").each(function(){
        	app=$(this).css("background-color");
    		$(this).css("background-color",$(this).attr("vcolor"));
    		$(this).attr("vcolor",app);
        });
    
    	if($(this).hasClass("btn-primary"))
        	$(this).removeClass("btn-primary");
   	 	else
        	$(this).addClass("btn-primary");
    });
	
	// Handler del button update
	$("#update").click(function(){
    	getData();
    });
	$("#vis").change(function(){
    	getData();
    });

	// Funzione che permette di estrarre dal database i valori aggiornati 
	// nel formato labels - values.  
	function getData(){
    	if(!$.isNumeric($("#a1").val()) || !$.isNumeric($("#a2").val()) || !(parseInt($("#a1").val()) <= parseInt($("#a2").val()))){
        	alert("Anni non coerenti");
        	return;
        }
    
		var cond="";
    	$(".stat").each(function(){
    		if($(this).val()=="on")
	    		cond+="&"+$(this).attr("id")+"=1";
    	});
    	
    	$("#update").attr("disabled",true);
    	$("#vis").attr("disabled",true);
    	
		$(".jcol").text("-");
		   
		if($("#btncol").hasClass("btn-primary"))
        	$(".jcol").each(function(){
        		app=$(this).css("background-color");
    			$(this).css("background-color",$(this).attr("vcolor"));
    			$(this).attr("vcolor",app);
       		});
		
		if($("#vis").val()=="vt"){
			$("#med1").html("Medie I<br>quadrimestre");
			$("#med2").html("Medie II<br>quadrimestre");
		}
		else{
			$("#med1").html("Medie<br>studenti");
			$("#med2").html("Mediane<br>studenti");
		}


		$.ajax({  
    		url: "./aj_cl_stat.php",
      		data: "id=<?=$_GET['id']?>&anno1="+$("#a1").val()+"&anno2="+$("#a2").val()+"&vis="+$("#vis").val()+cond,
      		dataType: "json",   
      		async: false,
      		success: function(data){
				if(data[0]!=null){
					var idc;
					var idr;
					var dat_vals=data[0]['val'];
					var dat_colors=data[0]['color'];
					var avg=data[1]['avg'];
					var med=data[1]['med'];
					var savg=data[1]['savg'];
					var smed=data[1]['smed'];
					var tavg=data[1]['tavg'];
				
					$(".jdat").each(function(){
						idr=parseInt($(this).attr("id").substring(0,$(this).attr("id").lastIndexOf("_")));
						idc=parseInt($(this).attr("id").substring($(this).attr("id").lastIndexOf("_")+1));
						if(dat_vals[idr])
							if(dat_vals[idr][idc]){
								$(this).text(dat_vals[idr][idc]);
								$(this).attr("vcolor","#"+dat_colors[idr][idc]);
							}
							else
								$(this).attr("vcolor","");
						else
							$(this).attr("vcolor","");
					});

					$(".jtavg").each(function(){
						idt=parseInt($(this).attr("id").substring(2));

						$(this).text(avg[idt]['val']);
						$(this).attr("vcolor","#"+avg[idt]['color']);
					});

					$(".jtmed").each(function(){
						idt=parseInt($(this).attr("id").substring(2));

						$(this).text(med[idt]['val']);
						$(this).attr("vcolor","#"+med[idt]['color']);
					});

					$(".jsavg").each(function(){
						ids=parseInt($(this).attr("id").substring(2));

						if(savg && savg[ids]){
							$(this).text(savg[ids]['val']);
							$(this).attr("vcolor","#"+savg[ids]['color']);
						}
						else{
							$(this).attr("vcolor","");
						}
					});

					$(".jsmed").each(function(){
						ids=parseInt($(this).attr("id").substring(2));

						if(smed && smed[ids]){
							$(this).text(smed[ids]['val']);
							$(this).attr("vcolor","#"+smed[ids]['color']);
						}
						else
							$(this).attr("vcolor","");
					});

					$("#tavg").text(tavg['val']);
					$("#tavg").attr("vcolor","#"+tavg['color']);

					if($("#btncol").hasClass("btn-primary"))
						$(".jcol").each(function(){
							app=$(this).css("background-color");
							$(this).css("background-color",$(this).attr("vcolor"));
							$(this).attr("vcolor",app);
						});
				}
				else{
					$(".jcol").each(function(){
						$(this).attr("vcolor","");
						$(this).css("background-color","");
					});
				}

            	$("#vis").attr("disabled",false);
            	$("#update").removeClass("btn-warning");
    			$("#update").addClass("btn-primary");
            	$("#update").attr("disabled",false);
      		},
      		error: function(){
        		alert("Errore ajax");
      		},
        	timeout: 5000
    	});
	}
	


});
</script>

<?php show_postmain(); ?>