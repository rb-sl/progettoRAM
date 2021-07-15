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

// Collection of functions used for test_stats.php

// Update button handlers
$("#update").click(function() {
	getData();
});

$("#graph").change(function() {
	getData();
});

// Ajax function to extract from the DB the requested data 
// int he labels - values format  
function getData() {
	if(!checkYears())
		return;

	disableUpdate();

	cond = buildCondFromMenu();
	
	$.ajax({  
		url: "/statistics/test_stats_ajax.php",
		data: "id=" + id + "&graph=" + $("#graph").val() + cond,
		dataType: "json",   
		async: false,
		success: function(data)	{
			if(data === null) {
				window.location.reload();
			}

			// Data is updated only if some returned,
			// otherwise all is invalidated
			if(parseInt(data.n) > 0) {
				// Update of statistics
				$("#n").text(data.n);
				$("#avg").text(Math.round(data.avg * 100) / 100);
				$("#std").text(Math.round(data.std * 100) / 100);
				$("#med").text(Math.round(data.med * 100) / 100);

				// Update of records
				$("#best").text(Math.round(data.best * 100) / 100);
				$("#worst").text(Math.round(data.worst * 100) / 100);

				// Update of record table
				$("#tbest").html(data.list);
			}
			else {
				$("#n").text("-");
				$("#avg").text("-");
				$("#med").text("-");
				$("#std").text("-");

				$("#best").text("-");
				$("#worst").text("-");
				$("#tbest").html("");
			}

			// Update of plot type
			switch($("#graph").val()) {
				case "val":
					draw_graph_val(data.plot.vals);
					break;
				case "box":
					draw_graph_box(data.plot.vals);
					break;
				case "prc":
					draw_graph_prc(data.plot.lbls, data.plot.vals);
					break;
				case "cbox":
				case "sbox":
					draw_graph_multibox(data.plot);
					break;
				case "trend":
					draw_graph_trend(data.plot);
					break;
			}

			enableUpdate();
		},
		error: function() {
			alert("Errore ottenimento dati aggiornati");
		},
		timeout: 5000
	});
}

// Plot with values
function draw_graph_val(vals) {	
	var plotData = [{
		x: vals,
		type: "histogram",
		marker: {
			line: {
				width: 1,
				opacity: 0
			}
		}
	}];

	var layout = {
		height: "600",
		title: $("#test_name").html() + " - Valori"
	};

	Plotly.react("cnv", plotData, layout, {responsive: true});
}

// Function to draw a single box plot
function draw_graph_box(vals) {
	var trace = {
		x: vals,
		type: "box",
		boxpoints: false,
		boxmean: true,
		hoverinfo: "x"
	};

	var data = [trace];

	var layout = {
		height: "600",
		title: $("#test_name").html() + " - Box plot",
		yaxis: {
			visible: false
		}
	};

	Plotly.react("cnv", data, layout, {responsive: true});	
}

// Function to draw multiple box plots
function draw_graph_multibox(graph) {
	var data = [];

	$.each(graph, function(label, val) {
		data.push({
			y: val,
			type: "box",
			boxpoints: false,
			boxmean: true,
			hoverinfo: "y",
			name: label
		})
	});

	var layout = {
		height: "600",
		title: $("#test_name").html() + " - " + $("#graph option:selected").html()
	};

	Plotly.react("cnv", data, layout, {responsive: true});
}

// Function to deaw the percentiles plot
function draw_graph_prc(lbls, vals) {
	var plotData = [{
		x: lbls,
		y: vals,
		type: "scatter",
		line: {
			shape: "spline"
		}
	}];

	var layout = {
		height: "600",
		title: $("#test_name").html() + " - Valori percentili"
	};

	Plotly.react("cnv", plotData, layout, {responsive: true});
}

function draw_graph_trend(plot) {
	var years = plot.year_list.map(y => y.toString());
	var mean = {
		x: years,
		y: plot.mean,
		line: {shape: "spline"},
		type: "scatter",
		name: "media"
	}
	var median = {
		x: years,
		y: plot.med,
		line: {shape: "spline"},
		type: "scatter",
		name: "mediana"
	}
	var min = {
		x: years,
		y: plot.min,
		line: {shape: "spline"},
		type: "scatter",
		name: "min"
	}
	var max = {
		x: years,
		y: plot.max,
		line: {shape: "spline"},
		type: "scatter",
		name: "max"
	}
	var q1 = {
		x: years,
		y: plot.q1,
		line: {shape: "spline"},
		type: "scatter",
		name: "q1"
	}
	var q3 = {
		x: years,
		y: plot.q3,
		line: {shape: "spline"},
		type: "scatter",
		name: "q3"
	}

	var plotData = [
		max,
		q3,
		mean,
		median,
		q1,
		min		
	];

	var layout = {
		height: "600",
		hovermode: "x",
		title: $("#test_name").html() + " - Andamento statistiche"
	};

	Plotly.react("cnv", plotData, layout, {responsive: true});
}
