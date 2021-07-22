// Copyright 2021 Roberto Basla

// This file is part of progettoRAM.

// progettoRAM is free software: you can redistribute it and/or modify
// it under the terms of the GNU Affero General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

// progettoRAM is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU Affero General Public License for more details.

// You should have received a copy of the GNU Affero General Public License
// along with progettoRAM.  If not, see <http://www.gnu.org/licenses/>.

// Javascript functions used in correlation.php

// Variable to avoid replotting a graph if already
// up to date
var prevPlotted;

// Function to change background to the table
$("#btncol").click(function() {
	var app;
	
	$(".gr").each(function() {
		app = $(this).css("background-color");
		$(this).css("background-color", $(this).attr("vcolor"));
		$(this).attr("vcolor", app);
	});

	if($(this).hasClass("btn-primary")) {
		$(this).removeClass("btn-primary");
		$(this).addClass("btn-secondary");
		$("table").addClass("table-striped");
	}
	else {
		$(this).removeClass("btn-secondary");
		$(this).addClass("btn-primary");
		$("table").removeClass("table-striped");
	}
});

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
	if(!$(".overlay").is(":hidden")) {
		$(".overlay").hide();
	}
});
// - (*) on the press of the back button
$(window).on("popstate", function() {
	if(!$(".overlay").is(":hidden")) {
		$(".overlay").hide();
	}
});
// And to avoid a dangling state on reload
window.onbeforeunload =  function() {
	if(!$(".overlay").is(":hidden")) {
		window.history.back();
	}
};

// Function to show the scatter plot of a test when requested
$(".clcbl").click(function() {
	// The graph is updated only if its data is not already computed
	if(prevPlotted !== $(this).attr("id")) {
		prevPlotted = $(this).attr("id");

		// Test ids for the request
		var idr = parseInt($(this).attr("id").substring(1 , $(this).attr("id").lastIndexOf("_")));
		var idc = parseInt($(this).attr("id").substring($(this).attr("id").lastIndexOf("_") + 1));

		plotScatter(idr, idc);
	}

	showGraph();
});

// Function to create the scatter plot of a test
function plotScatter(idrow, idcol) {
	// Gets the axis names from the table
	var namerow = $("#test" + idrow).text();
	var namecol = $("#test" + idcol).text();
	// Test data from the list computed in correlation.php
	var testrow = testInfo.find(x => x.label.toString() === namerow);
	var testcol = testInfo.find(x => x.label.toString() === namecol);

	var trace = [{
		x: testcol.values,
		y: testrow.values,
		mode: "markers",
		type: "scatter",
	}];

	var layout = {
		title: "Diagramma di dispersione " + namerow + "/" + namecol 
			+ " (ρ=" + $("#m" + idrow + "_" + idcol).html() + ")",
		xaxis: {
			title: namecol + testcol.unit
		},
		yaxis: {
			title: namerow + testrow.unit
		},
		hovermode: "closest"
	};

	Plotly.react("cnv", trace, layout, {responsive: true}); 	
}

// Function to update the active plot
function rePlot() {
	// Test ids for the request
	var idr = parseInt(prevPlotted.substring(1 , prevPlotted.lastIndexOf("_")));
	var idc = parseInt(prevPlotted.substring(prevPlotted.lastIndexOf("_") + 1));

	plotScatter(idr, idc);
}

// Function to make visible the plot for two tests
function showGraph() {
	$("#over").show();
	$("#over").css("display", "flex");

	// Adds an entry to the history so that a back button press can
	// remove the overlay with function (*)
	window.history.pushState("overlay", null, window.location.href);
}

// Handler for the update from the statistical menu
$("#update").click(function() {
	// The update is performed only in case of modifications
	if($(this).hasClass("btn-warning")) {
		getData();
	}
});

// Function to perform the data request
function getData() {
	if(!checkYears()) {
		$("#update").attr("disabled", false);
		return;
	}

	disableUpdate();

	cond = buildCondFromMenu();
			
	$.ajax({  
		url: "/statistics/correlation_ajax.php",
		data: cond,
		dataType: "json",
		success: function(data)	{
			if(data === null) {
				window.location.reload();
			}

			// Updates the table
			handleData(data.matrix);
			
			// Test data and dimension for the splom are updated
			testInfo = data.testInfo;
			splomWH = Math.max(500, Object.keys(data.testInfo).length * 130);
			plotSplom();

			// Changes the active graph
			if(prevPlotted !== undefined) {
				rePlot()
			}			
			
			// Restores the possibility to change data again
			enableUpdate();
		},
		error: function() {
			alert("Errore ottenimento dati aggiornati");
		},
		timeout: 5000
	});
}

// Table data update function
function handleData(data) {
	$(".gr").each(function() {
		var idc = parseInt($(this).attr("id").substring(1, $(this).attr("id").lastIndexOf("_")));
		var idr = parseInt($(this).attr("id").substring($(this).attr("id").lastIndexOf("_") + 1));
		
		$(this).text(data[idc][idr].r);
		updateColor($(this), data[idc][idr].color);
		
		if(data[idc][idr].r != "-") {
			$(this).addClass("point clcbl");
		}
		else {
			$(this).removeClass("point clcbl");
		}
		
		$(this).attr("title", "n=" + data[idc][idr].n);
	});
}

// Function to change the color of a cell
function updateColor(object, color) {
	if($("#btncol").hasClass("btn-primary")) {
		object.css("background-color", color);
	}
	else {
		object.attr("vcolor", color);
	}
}

// Function to create the scatter matrix plot
function plotSplom() {
	// Replaces spaces with newlines in order to 
	// avoid overflows on the axis
	var splomBreak = testInfo.map(obj => { 
		return {
			label:	obj.label.toString().replaceAll(" ", "<br>"), 
			values: obj.values 
		}
	});

	var data = [{
		type: "splom",
		dimensions: splomBreak,
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
		plot_bgcolor: "rgba(240, 240, 240, 0.95)"
	}
	
	Plotly.react("splom", data, layout);
}

// The plot matrix is computed only after the page is
// displayed to reduce load time
$(document).ready(function() {
	plotSplom();
});
