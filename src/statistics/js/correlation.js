// Javascript functions used in correlation.php
$(function(){
	var prevPlotted;
	var canUpdate = 1;

	$(".clcbl").click(function() {
		// The graph update happens only if it is not already displayed
    	if(canUpdate && $(this) != prevPlotted)
        {
        	canUpdate=0;
        
    		if(prevPlotted) {
    	   		prevPlotted.css("background-color", prevPlotted.closest("tr").css("background-color"));
    			prevPlotted.css("color", "black");
        	}

    		prevPlotted = $(this);
    
    		var idr = parseInt($(this).attr("id").substring(1 , $(this).attr("id").lastIndexOf("_")));
        	var idc = parseInt($(this).attr("id").substring($(this).attr("id").lastIndexOf("_") + 1));
    
    		$(this).css("background-color", "rgb(240,173,78)");
    		$(this).css("color", "white");

    		getData(idr, idc);
    
    		$(this).css("background-color", "rgb(51, 122, 183)");
        
        	canUpdate = 1;
        }    	
    });

	$("#update").click(function(){
    	if($(this).hasClass("btn-warning"))
        {
        	var idr = -1;
    		var idc = -1;
    		if(prevPlotted) {
    			idr = parseInt(prevPlotted.attr("id").substring(1, prevPlotted.attr("id").lastIndexOf("_")));
        		idc = parseInt(prevPlotted.attr("id").substring(prevPlotted.attr("id").lastIndexOf("_") + 1));
        	}
    		$(".gr").text("-");
    	
    		getData(idr, idc, true);
        }
    });

	function getData(idr, idc, upd = "")
	{
    	if(!checkYears())
        	return;

    	cond = buildCondFromMenu();
    	
		if($("#update").hasClass("btn-warning"))
        	upd = true;
    	
    	$.ajax({  
    		url: "./correlation_ajax.php",
      		data: cond + "upd=" + upd + "&id1=" + idr + "&id2=" + idc,
      		dataType: "json",   
      		async: false,
      		success: function(data)	{
            	if(upd) {
                	handleData(data['matrix']);
                	if(prevPlotted)
                       	drawGraph(data['test'], idr, idc);
                }
            	else
                	drawGraph(data['test'], idr, idc);
        	
        		$("#update").removeClass("btn-warning");
    			$("#update").addClass("btn-primary");
            	$("#update").attr("disabled", false);
      		},
      		error: function() {
        		alert("Errore ottenimento dati aggiornati");
      		},
        	timeout: 5000
    	});
	}

	// Funzione per l'update dei dati
	function handleData(data)
	{
   		$(".gr").each(function() {
        	var idc = parseInt($(this).attr("id").substring(1, $(this).attr("id").lastIndexOf("_")));
        	var idr = parseInt($(this).attr("id").substring($(this).attr("id").lastIndexOf("_") + 1));
        	
			$(this).text(data[idc][idr]['r']);
			
			if(data[idc][idr]['r'] != "-")
				$(this).addClass("point clcbl");
			else
				$(this).removeClass("point clcbl");

        	$(this).attr("title", "n=" + data[idc][idr]['n']);
        });
	}	

	function drawGraph(data, idr, idc)
	{
    	var trace = [{
 			x: data['t1'],
  			y: data['t2'],
  			mode: "markers",
  			type: "scatter",
		}];

    	var layout = {
        	height: "600",
        	title: "Diagramma di dispersione " + data['n1'] + "/" + data['n2'] 
				+ " (ρ=" + $("#m" + idr + "_" + idc).html() + ")",
        	xaxis: {
            	title: data['n1'] + data['u1']
            },
        	yaxis:{
            	title: data['n2'] + data['u2']
            },
        	hovermode: "closest"
        };

		Plotly.newPlot("cnv", trace, layout, {responsive: true}); 	
	}

	function plotSplom() {
		var data = [{
			type: "splom",
			dimensions: splomDimensions,
			text: splomText,
			marker: {
				size: 3,
				line: {
					color: 'white',
					width: 0.3
				}
			}
		}];
	  
		var layout = {
			title: "Matrice di dispersione dei test",
			height: window.innerWidth - 50,
			width: window.innerWidth - 50,
			autosize: true,
			hovermode:'closest',
			dragmode:'select',
			plot_bgcolor:'rgba(240,240,240, 0.95)'
		}
	  
		Plotly.react("splom", data, layout)
	}
	
	plotSplom();
});
