<?php
include $_SERVER['DOCUMENT_ROOT']."/librerie/general.php";
include $_SERVER['DOCUMENT_ROOT']."/librerie/lib_stat.php";
chk_access(3);
connect();

$ret=query("SELECT * FROM TEST,UNITA WHERE fk_udm=id_udm AND id_test=".$_GET['id']);
$test=$ret->fetch_assoc();

show_premain("Statistiche ".$test['nometest'],true);

$dati=get_stats($_GET['id']);
$rcr=get_records();
$graph=graph_vals();
?>

<h2>Statistiche <span id="nomet"><?=$test['nometest']?></span></h2>

<table class='table table-striped'>
   	<tr><td>Numero totale di prove: <span id="n"><?=$dati['n']?></span></td>
   	<tr><td>Media: <span id="avg"><?=number_format($dati['avg'],2)?></span> <?=$test['simbolo']?></td></tr>
	<tr><td>Mediana: <span id="med"><?=number_format($dati['med'],2)?></span> <?=$test['simbolo']?></td></tr>
   	<tr><td>Deviazione Standard: <span id="std"><?=number_format($dati['std'],2)?></span> <?=$test['simbolo']?></td></tr>	
</table>

<h3 style="margin-bottom:0px">Record positivo: <span id="best"><?=$rcr['best']?></span> <?=$test['simbolo']?></h3>

<?=$rcr['list']?>

<h3>Record negativo: <span id="worst"><?=$rcr['worst']?></span> <?=$test['simbolo']?></h3>

<h3>
	Grafico: 
	<select id="graph">
    	<option value="val">Valori</option>
    	<option value="box">Box plot</option>
    	<option value="hbox">Box plot (Anni)</option>
    	<option value="cbox">Box plot (Classi)</option>
	    <option value="sbox">Box plot (Sesso)</option>
    	<option value="prc">Valori percentili</option>
	</select>
</h3>

<!-- Grafico -->
<div id="cnv">
</div>

<script>

$(function(){
	// Inizializzazione del grafico
	$(document).ready(function(){
    	var lbls=[<?php
		foreach($graph['lbls'] as $lbl)
			echo "'$lbl',\n";
		?>];
    	
    	var vals=[<?php
		foreach($graph['vals'] as $val)
			echo "$val,";
		?>];
   			
    	draw_graph_val(vals);
    });
	
	// Handler del button update
	$("#update").click(function(){
    	getData();
    });
	$("#graph").change(function(){
    	getData();
    });
	
	// Funzione che permette di estrarre dal database i valori aggiornati 
	// nel formato labels - values.  
	function getData()
	{
    	if(!$.isNumeric($("#a1").val()) || !$.isNumeric($("#a2").val()) || !(parseInt($("#a1").val()) <= parseInt($("#a2").val())))
        {
        	alert("Anni non coerenti");
        	return;
        }
    
		var cond="";
    	$(".stat").each(function(){
    		if($(this).val()=="on")
	    		cond+="&"+$(this).attr("id")+"=1";
    	});
    	
    	$("#update").attr("disabled",true);
    	$("#graph").attr("disabled",true);
    	
   		$("#n").text("-");
    	$("#avg").text("-");
    	$("#std").text("-");
    
    	$("#best").text("-");
    	$("#worst").text("-");
    	$("#tbest").text("");    
    	
    	$.ajax({  
    		url: "./aj_stat.php",
      		data: "id=<?=$_GET['id']?>&anno1="+$("#a1").val()+"&anno2="+$("#a2").val()+"&graph="+$("#graph").val()+cond,
      		dataType: "json",   
      		async: false,
      		success: function(data)
      		{
            	var stats = data[0]; // In data[0] ci sono i nuovi valori dei parametri statistici
				var rec = data[1]; // data[1] -> record
				var graph = data[2] // data[2] -> valori grafico
                
            	handleData(stats,rec);
            	switch($("#graph").val())
                {
               	 	case "val":
                		draw_graph_val(graph['vals']);
                		break;
                	case "box":
                		draw_graph_box(graph['vals']);
                		break;
               		case "prc":
                		draw_graph_prc(graph['lbls'],graph['vals']);
                		break;
                	case "hbox":
                	case "cbox":
                	case "sbox":
                		draw_graph_multibox(graph,$("#graph").val());
                		break;
                }
            	$("#graph").attr("disabled",false);
            	$("#update").removeClass("btn-warning");
    			$("#update").addClass("btn-primary");
            	$("#update").attr("disabled",false);
      		},
      		error: function()
      		{
        		alert("Errore ajax");
      		},
        	timeout: 5000
    	});
	}

	// Funzione per l'update dei dati
	function handleData(stats,rec)
	{
   		$("#n").text(stats['n']);
    	if(stats['avg'])
			$("#avg").text(stats['avg']);
    	if(stats['std'])
	    	$("#std").text(stats['std']);
    	if(stats['med'])
	    	$("#med").text(stats['med']);
    
    	if(rec['best'])
	    	$("#best").text(rec['best']);
    	if(rec['worst'])
	    	$("#worst").text(rec['worst']);
    	$("#tbest").html(rec['list']);
	}	

	// Costruzione del grafico dei valori
	function draw_graph_val(vals)
	{	
		var dgraph = [{
    		x: vals,
    		type: 'histogram',
        	marker: {
            	line: {
                	width: 1,
                	opacity: 0
                }
            }
  		}];
		var layout = {
			height: '600',
      		title: $("#nomet").html()+" - Valori"
		}
		Plotly.newPlot('cnv', dgraph,layout,{responsive: true});
	}
	
	function draw_graph_box(vals)
	{
    	var trace1 = {
  			x: vals,
  			type: 'box',
        	boxpoints: false,
        	boxmean: true,
        	hoverinfo: "x"
		};

		var data = [trace1];

		var layout = {
  			height: '600',
        	title: $("#nomet").html()+" - Box plot",
        	yaxis: {
            	visible: false
            }
		};

		Plotly.newPlot('cnv', data, layout,{responsive: true});	
    }

	function draw_graph_prc(lbls,vals)
	{
    	var dgraph = [{
        	x: lbls,
    		y: vals,
    		type: 'scatter',
        	line: {shape: 'spline'}
  		}];
    	var layout = {
			height: '600',
        	title: $("#nomet").html()+" - Valori percentili"
		}
		Plotly.newPlot('cnv',dgraph,layout,{responsive: true});
    }

	function draw_graph_multibox(graph,add)
	{
    	var data=[];
    	$.each(graph,function(key,val){
        	if(add=="hbox")
            	key=key+"/"+(parseInt(key)+1);
        	data.push({
  				y: val,
  				type: 'box',
        		boxpoints: false,
        		boxmean: true,
        		hoverinfo: "y",
            	name: key
			})
        });
    	var layout = {
  			height: '600',
        	title: $("#nomet").html()+" - "+$("#graph option:selected").html()
		};

		Plotly.newPlot('cnv', data, layout,{responsive: true});
    }
});
  
</script>
<?php show_postmain(); ?>