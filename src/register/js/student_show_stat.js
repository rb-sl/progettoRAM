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

// Scripts exclusive to student_show_stat.php

// Function to get the plot's layout based on the type
function getLayout() {
	var axis;

	switch($("#vis").val()) {
		case "prc":
			axis = {
				visible: true,
				range: [0, 100]
			};
			break;
		case "std":
			axis = {
				visible: true
			};
			break;
		case "gr":
			axis = {
				visible: true,
				range: [0, 10]
			};
	}

	return {
		polar: {
			radialaxis: axis
		},
		title: "Prove di " + $("#student").text() + " - " 
			+ $("#class option:selected").text()
			+ " (" + $("#vis option:selected").text() + ")"
	}
}

// Function to build the plot's data based on the table's content
function getPlotData() {
	var rad = [];
	var th = [];
	
	$(".dat" + $("#class").val()).each(function() {
		if($(this).text() !== "-") {
			rad.push($(this).text());
			th.push($("#test" + $(this).attr("id")
				.substr($(this).attr("id").indexOf("_") + 1))
				.text());
		}
	});

	// Closes the figure drawn by the plot
	rad.push(rad[0]);
	th.push(th[0]);

	return [{
		type: "scatterpolar",
		r: rad,
		theta: th,
		fill: "toself",
		name: $("#class option:selected").text()		
	}];
}

// Function to draw the plotly graph
function drawRadarGraph() {
	Plotly.newPlot("cnv", getPlotData(), getLayout(), {responsive: true});
}

// Reacts to the change of plot type
$("#class").on("change", function() {
	drawRadarGraph();
});

// Plots the graph on load
drawRadarGraph();
