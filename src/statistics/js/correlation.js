// Javascript functions used in correlation.php
$(function() {
	// Variable to avoid replotting a graph if already
	// up to date
	var prevPlotted;
	
	// Blocks an update if waiting for a previous one
	var canUpdate = true;

	// Functions to hide the graph overlay:
	// - on click on the shaded area
	$(".overlay").click(function() {
		if($(event.target).is("#over"))	{
			$(this).hide();
			window.history.back();
		}
	});
	// - on press of any key
	$(document).keyup(function() {
		if(!$(".overlay").is(":hidden"))
			$(".overlay").hide();
	});
	// - (*) on the press of the back button
	$(window).on("popstate", function() {
		if(!$(".overlay").is(":hidden"))
			$(".overlay").hide();
	});
	// And to avoid a dangling state on reload
	window.onbeforeunload =  function() {
		if(!$(".overlay").is(":hidden"))
			window.history.back();
    };

	// Function to create the scatter plot of a test 
	$(".clcbl").click(function() {
		// The graph is updated only if its data is not already displayed
    	if(canUpdate && $(this) != prevPlotted) {
        	canUpdate = false;
    		prevPlotted = $(this);
    
			// Test ids for the request
    		var idr = parseInt($(this).attr("id").substring(1 , $(this).attr("id").lastIndexOf("_")));
        	var idc = parseInt($(this).attr("id").substring($(this).attr("id").lastIndexOf("_") + 1));

    		getData(idr, idc);
        }    	
    });

	// Handler for the update from the statistical menu
	$("#update").click(function() {
		// The update is performed only in case of modifications
    	if($(this).hasClass("btn-warning")) {
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

	// Function to perform the data request
	function getData(idr, idc, upd = false) {
    	if(!checkYears()) {
			$("#update").attr("disabled", false);
			return;
		}
    	cond = buildCondFromMenu();
    	
		if($("#update").hasClass("btn-warning"))
        	upd = true;
    	
    	$.ajax({  
    		url: "./correlation_ajax.php",
      		data: "upd=" + upd + "&id1=" + idr + "&id2=" + idc + cond,
      		dataType: "json",
      		success: function(data)	{
				// Different actions are performed based on what the
				// user changed
            	if(upd) {
                	handleData(data['matrix']);
					prevplotted = null;
					splomDimensions = data['splom'];
					splomWH = Object.keys(data['splom']).length * 130;
					plotSplom();
                }
            	else {
                	drawGraph(data['test'], idr, idc);
					showGraph();
				}

				// Restores the possibility to change data again
        		$("#update").removeClass("btn-warning");
    			$("#update").addClass("btn-primary");
            	$("#update").attr("disabled", false);
				canUpdate = true;
      		},
      		error: function() {
        		alert("Errore ottenimento dati aggiornati");
      		},
        	timeout: 5000
    	});
	}

	// Data update function
	function handleData(data) {
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

	// Function to draw (but not make visible) the scatter plot for two tests
	function drawGraph(data, idr, idc) {
    	var trace = [{
 			x: data['t1'],
  			y: data['t2'],
  			mode: "markers",
  			type: "scatter",
		}];

    	var layout = {
        	title: "Diagramma di dispersione " + data['n1'] + "/" + data['n2'] 
				+ " (ρ=" + $("#m" + idr + "_" + idc).html() + ")",
        	xaxis: {
            	title: data['n1'] + data['u1']
            },
        	yaxis: {
            	title: data['n2'] + data['u2']
            },
        	hovermode: "closest"
        };

		Plotly.newPlot("cnv", trace, layout, {responsive: true}); 	
	}

	// Function to make visible the plot for two tests
	function showGraph() {
		$("#over").show();
		$("#over").css("display", "flex");

		// Adds an entry to the history so that a back button press can
		// just remove the overlay with function (*)
		window.history.pushState("overlay", null, window.location.href);
	}

	// Function to create the scatter matrix plot
	function plotSplom() {
		var data = [{
			type: "splom",
			dimensions: splomDimensions,
			marker: {
				size: 3,
				line: {
					color: "white",
					width: 0.3
				}
			}
		}];
	  
		var layout = {
			title: "Matrice di dispersione dei test",
			height: splomWH,
			width: splomWH,
			hovermode: "closest",
			dragmode: false,
			plot_bgcolor: "rgba(240,240,240, 0.95)"
		}
	  
		Plotly.react("splom", data, layout);
	}
	
	plotSplom();
});
