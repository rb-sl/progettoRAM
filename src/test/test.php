<?php
// Pagina iniziale della sezione di gestione dei test. Permette di
// - Visualizzare un elenco dei test nel sistema per raggiungere le rispettive pagine
// - Visualizzare e modificare i propri voti (o quelli degli altri utenti se amministratore)
include $_SERVER['DOCUMENT_ROOT']."/librerie/general.php";
chk_access(2);
connect();
show_premain("Test e valutazioni");
?>

<h2>Visualizzazione Test</h2><?= $_SESSION['priv']<=1 ? "<div><a href='./add_test.php' class='btn btn-warning btnmenu'>Aggiungi nuovo</a></div>" : "" ?>
<div class="scrollable">
	<table class="table table-striped">
		<tr><thead><th>Nome test</th></thead></tr>
<?php
$rettest=query("SELECT * FROM TEST ORDER BY nometest");

while($rowt=$rettest->fetch_assoc())
	echo "<tr><td><a href='show_test.php?id=".$rowt['id_test']."'>".$rowt['nometest']."</a></td>";
?>
    </table>
</div>
<form id="voti" action="up_voti.php" method="POST">
	<h2 id="voti">Tabella di valutazione
<?php
// Se l'utente è un amministratore può visualizzare i voti di ogni utente
if($_SESSION['priv']==0)
{
	echo "di <select class='form-control' id='slp' name='slp'>";
	$r=query("SELECT * FROM PROFESSORI ORDER BY id_prof");
  	while($p=$r->fetch_assoc())
    	echo "<option value='".$p['id_prof']."'>".$p['user']."</option>";
    echo "</select>";
}
?></h2>

	<!-- div contenente il grafico plotly -->
	<div id="cnv">
	</div>

<?php
$ret=query("SELECT * FROM VALUTAZIONI,VOTI WHERE fk_voto=id_voto AND fk_prof=".$_SESSION['id']);
while($row=$ret->fetch_assoc())
{
	$pvoti[$row['voto']*10]['perc']=$row['perc'];
	$pvoti[$row['voto']*10]['color']=$row['color'];
}	
?>
	<table class="table table-striped">
    	<tr>
        	<th>Voto</th>
           	<th>Percentuale assegnata</th>
           	<th colspan="3" class="w30">Range percentili</th>
       	</tr>
<?php
$prev=0;       
for($i=4;$i<=10;$i+=0.5)
{
	$i10=$i*10;
	$bg="background-color:#".$pvoti[$i10]['color'];
	echo "<tr>
    		<td style='$bg'>$i</td>
    		<td><input style='text-align:right' type='number' min='0' id='r$i10' class='range w50' value='".($pvoti[$i10]['perc']-$prev)."' name='r[$i10]'>%</td> 
            <td id='i$i10'>$prev</td><td>&rarr;</td><td id='f$i10'>".$pvoti[$i10]['perc']."</td>
          </tr>";

	// Componenti del grafico plotly
	$tracelist.="trace$i10,";
	$traces.="var trace$i10 = {
  		x: [".($pvoti[$i10]['perc']-$prev)."],
  		type: 'bar',
   		name: '$i',
   		text: '$i',
  		textposition: 'auto',
   		hoverinfo: 'none',
   		marker: {
    		color: '".$pvoti[$i10]['color']."',
    		line: {
      			color: '#000',
      			width: 1.5
    		}
  		}
	};\n";

	$prev=$pvoti[$i10]['perc'];
}
?>
		<tr>
        	<th class='btop'>Totale:</th>
        	<td class='err sum btop'><?=$prev?></td>
        	<td class='err btop'>0</td><td class='err btop'>&rarr;</td><td class='err btop sum'><?=$prev?></td>
    	</tr>
    </table>
	<input type="submit" id="aggv" class="btn btn-warning btnmenu" value="Aggiorna tabella voti">
</form>

<script>

// Funzione per impredire l'aggiornamento dei voti se i percentili non sommano a 100
$(function(){
	$("#voti").submit(function(e){
    	if($('.sum').html()!=100)
        {
        	alert("Valore voti non valido\nLa somma dei punti percentili deve essere 100");
        	e.preventDefault();
        }
	});
});

// Funzione per la scelta dell'utente di cui visualizzare i voti (solo amministratore)
// Aggiorna le informazioni in tabella e il grafico
$(function(){
	$("#slp").change(function(){
    	var prev = 0;
    	$.ajax({                                      
      		url: 'aj_voti.php',   
      		data: "idprof="+$(this).val(), 
      		dataType: 'json',                
      		success: function(data) 
      		{
        		for(var i=40;i<=100;i+=5)
                {
                	$('#r'+i).val(data[i]-prev);
                	$('#i'+i).html(prev);
                	$('#f'+i).html(data[i]);
                	prev=parseInt(data[i]);
                }
            	$('.sum').html($('#f100').html());
    			if(parseInt($('.sum').html())!=100)
        			$('.err').css('color','red');
    			else
        			$('.err').css('color','black');
            
            	Plotly.animate('cnv', {
 					data: [
                    	{x: [data[40]]},
                    	{x: [data[45]-data[40]]},
                     	{x: [data[50]-data[45]]},
                     	{x: [data[55]-data[50]]},
                     	{x: [data[60]-data[55]]},
                     	{x: [data[65]-data[60]]},
                     	{x: [data[70]-data[65]]},
                     	{x: [data[75]-data[70]]},
                     	{x: [data[80]-data[75]]},
                     	{x: [data[85]-data[80]]},
                     	{x: [data[90]-data[85]]},
                     	{x: [data[95]-data[90]]},
                     	{x: [data[100]-data[95]]}
                    ],
    				traces: [0,1,2,3,4,5,6,7,8,9,10,11,12],
    				layout: {}
  				}, {
    				transition: {
      					duration: 500,
      					easing: 'cubic-in-out'
    				},
    				frame: {
      					duration: 500
    				}
  				});
      		} 
    	});
    });
});

<?=$traces?>
var data = [<?=$tracelist?>];
var layout = {barmode: "stack", yaxis:{visible:false }};
Plotly.newPlot("cnv", data, layout, {responsive: true});

// Funzione per l'aggiornamento di grafico e tabella quando viene modificato
// un percentile
$(function(){
	$(".range").on("change",function(){
    	var rng = parseInt($(this).val());
    
    	var id = parseInt($(this).attr("id").substr(1));
    	var iin = parseInt($('#i'+id).html());
    	var fin = parseInt($('#f'+id).html());
		
    	var mod = rng - (fin - iin);

    	for(var i=id;i<=100;i+=5)
        {
        	$('#i'+(i+5)).html(parseInt($('#i'+(i+5)).html()) + mod);
       		$('#f'+i).html(parseInt($('#f'+i).html()) + mod);
        }
    	$('.sum').html($('#f100').html());
    	if(parseInt($('.sum').html())!=100)
        	$('.err').css('color','red');
    	else
        	$('.err').css('color','black');
    	reload(rng,id);
    });
});

// Funzione che ricarica il grafico dopo una modifica; cambia solo la trace del voto modificato 
function reload(mod,id) {
	Plotly.animate('cnv', {
 		data: [{x: [mod]}],
    	traces: [(id-40)/5],
    	layout: {}
  	}, {
    	transition: {
      		duration: 500,
      		easing: 'cubic-in-out'
    	},
    	frame: {
      		duration: 500
    	}
  	});
}

</script>

<?php show_postmain(); ?>