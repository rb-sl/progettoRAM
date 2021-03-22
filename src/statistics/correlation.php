<?php
include $_SERVER['DOCUMENT_ROOT']."/librerie/general.php";
include $_SERVER['DOCUMENT_ROOT']."/librerie/lib_stat.php";
chk_access(3);
connect();
show_premain("Correlazione dei test",true);

$ret=query("SELECT id_test,nometest,pos FROM TEST WHERE id_test IN (SELECT fk_test FROM PROVE GROUP BY fk_test) ORDER BY nometest");
while($row=$ret->fetch_assoc())
{
	$test[$row['id_test']]=$row['nometest'];
	$pos[$row['id_test']]=$row['pos'];
}
?>
<h2>Statistiche di correlazione campionaria dei test</h2>

<h3>Matrice di correlazione campionaria</h3>
<div class="tdiv">
  	<div class="inner">
		<table class="table table-striped">
			<tr id="thr"><th class="topleft leftfix topfix"><button id="btncol" class="btn wtot overpad" style="height:100%">Colori</button></th>

<?php
foreach($test as $id => $nome)
	echo "<th id='c$id' pos='".$pos[$id]."' class='col topfix'>$nome</th>\n";
echo "</tr>";

$i=0; 
foreach($test as $idc => $nomec)
{
	if($i%2==0)
    	$bc="#eee";
	else
    	$bc="#f9f9f9";
	$tab[$idc]['st']="<tr><th id='r$idc' class='leftfix dat2' style='background-color:$bc'>$nomec</th>";

	foreach($test as $idr => $nomer)
    	if($nomer<=$nomec)
        {
            $coeff=calc_r($idc,$idr);
        	
        	if($coeff['r']=="-")
            	$cl="";
        	else
            	$cl="point clcbl";
        
        	$tab[$idr][$idc]="<td id='m$idr"."_$idc' class='dat2 r_$idc $cl gr' title='n=".$coeff['n']."'>".$coeff['r']."</td>";
        	// La matrice è simmetrica
        	$tab[$idc][$idr]="<td id='m$idc"."_$idr' class='dat2 r_$idr $cl gr' title='n=".$coeff['n']."'>".$coeff['r']."</td>";
        }

	echo "</tr>";
	
	$i++;
}

foreach($tab as $id => $row)
{
	foreach($row as $x => $cell)
    	echo $cell;
	echo "</tr>";
}
?>	
		</table>
	</div>
</div>

<div id="cnv"></div>
 
<script>
$(function(){
	var prev;
	var token=1;

	$(".clcbl").click(function(){
    	if(token && $(this)!=prev)
        {
        	token=0;
        
    		if(prev){
    	   		prev.css("background-color",prev.closest("tr").css("background-color"));
    			prev.css("color","black");
        	}
    		prev=$(this);
    
    		var idr=parseInt($(this).attr("id").substring(1 , $(this).attr("id").lastIndexOf("_")));
        	var idc=parseInt($(this).attr("id").substring($(this).attr("id").lastIndexOf("_")+1));
    
    		$(this).css("background-color","rgb(240,173,78)");
    		$(this).css("color","white");

    		getData(idr,idc);
    
    		$(this).css("background-color","rgb(51, 122, 183)");
        
        	token=1;
        }    	
    });

	$("#update").click(function(){
    	if($(this).hasClass("btn-warning"))
        {
        	var idr=-1;
    		var idc=-1;
    		if(prev)
        	{
    			idr=parseInt(prev.attr("id").substring(1 , prev.attr("id").lastIndexOf("_")));
        		idc=parseInt(prev.attr("id").substring(prev.attr("id").lastIndexOf("_")+1));
        	}
    		$(".gr").text("-");
    	
    		getData(idr,idc,true);
        }
    });

	function getData(idr,idc,upd="")
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
    
    	if($("#update").hasClass("btn-warning"))
        	upd=true;
    	
    	$.ajax({  
    		url: "./aj_corr.php",
      		data: "upd="+upd+"&id1="+idr+"&id2="+idc+"&anno1="+$("#a1").val()+"&anno2="+$("#a2").val()+cond,
      		dataType: "json",   
      		async: false,
      		success: function(data)
      		{
            	if(upd)
                {
                	handleData(data['matrix']);
                	if(prev)
                       	drawGraph(data['test'],idr,idc);
                }
            	else
                	drawGraph(data['test'],idr,idc);
        	
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
	function handleData(data)
	{
   		$(".gr").each(function(){
        	var idc=parseInt($(this).attr("id").substring(1 , $(this).attr("id").lastIndexOf("_")));
        	var idr=parseInt($(this).attr("id").substring($(this).attr("id").lastIndexOf("_")+1));
        	
			$(this).text(data[idc][idr]['r']);
			
			if(data[idc][idr]['r']!="-")
				$(this).addClass("point clcbl");
			else
				$(this).removeClass("point clcbl");

        	$(this).attr("title","n="+data[idc][idr]['n']);
        });
	}	

	function drawGraph(data,idr,idc)
	{
    	var trace1 = {
 			x: data['t1'],
  			y: data['t2'],
  			mode: 'markers',
  			type: 'scatter',
        
		};
    	var layout = {
        	height: '600',
        	title: "Diagramma di dispersione "+data['n1']+"/"+data['n2']+" (ρ="+$("#m"+idr+"_"+idc).html()+")",
        	xaxis:{
            	title: data['n1']+data['u1']
            },
        	yaxis:{
            	title: data['n2']+data['u2']
            },
        	hovermode: 'closest'
        };

	Plotly.newPlot('cnv', [trace1],layout,{responsive:true}); 	
    	
	}

});
</script>
<?php show_postmain(); ?>