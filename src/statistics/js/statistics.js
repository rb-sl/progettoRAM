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

// Collection of functions used in statistics.php

// Prints the plot in statistics.php
function plotMiscStats() {
	Plotly.react("cnv", getPlotData(), getPlotLayout(), {responsive: true});
}

// Animation of the plot
function reloadPlot() {
	Plotly.animate("cnv", {
			data: getPlotData(),
			layout: getPlotLayout()
		}, {
			transition: {
			duration: 500,
			easing: "cubic-in-out"
		},
		frame: {
			duration: 500
		}
	});
}

function getPlotData() {
	return [{
		values: testDiv_vals,
		labels: testDiv_lbls,
		type: "pie",
		name: "Per test",
		title: "Per test",
		sort: false,
		direction: "clockwise",
		domain: {
			row: 0,
			column: 0
		},
		textinfo: "none"
	}, {
		values: studDiv_vals,
		labels: studDiv_lbls,
		type: "pie",
		name: "Per sesso",
		title: "Per sesso",
		domain: {
			row: 0,
			column: 1
		},
		textinfo: "none",
		sort: false,
		direction: "clockwise"
	}, {
		values: classDiv_vals,
		labels: classDiv_lbls,
		type: "pie",
		name: "Per classe",
		title: "Per classe",
		domain: {
			row: 1,
			column: 0
		},
		textinfo: "none",
		sort: false,
		direction: "clockwise",
	}, {
		values: yearDiv_vals,
		labels: yearDiv_lbls,
		type: "pie",
		name: "Per anno",
		title: "Per anno",
		domain: {
			row: 1,
			column: 1
		},
		textinfo: "none",
		sort: false,
		direction: "clockwise"
	}];
}

function getPlotLayout() {
	return {
		height: "700",
		showlegend: false,
		title: "Suddivisione delle prove",
		grid: {
			rows: 2, 
			columns: 2
		}
	}
}

// Update button handler
$("#update").click(function() {
	if($(this).hasClass("btn-warning")) {
		getData();
	}
});

// Function to update the page's data
function getData() {
	if(!checkYears()) {
		return;
	}

	disableUpdate();

	cond = buildCondFromMenu();    
	$.ajax({  
		url: "/statistics/statistics_ajax.php",
		data: cond,
		dataType: "json",
		success: function(data)	{
			if(data === null) {
				window.location.reload();
			}

			// Update of shown statistics
			$("#stud_tot").text(data.stud_tot);
			$("#res_tot").text(data.res_tot);

			if(data["stud_num"] !== undefined) {
				$("#stud_num").text(data.stud_num);
				$("#stud_perc").text(data.stud_perc);

				$("#res_num").text(data.res_num);
				$("#res_perc").text(data.res_perc);
			}
			else {
				$("#stud_num").text(data.stud_tot);
				$("#stud_perc").text("100");

				$("#res_num").text(data.res_tot);
				$("#res_perc").text("100");
			}

			// Reloads the plot with the new data
			testDiv_vals = data.test.vals;
			testDiv_lbls = data.test.lbls;

			studDiv_vals = data.stud.vals;
			studDiv_lbls = data.stud.lbls;

			classDiv_vals = data.class.vals;
			classDiv_lbls = data.class.lbls;

			yearDiv_vals = data.year.vals;
			yearDiv_lbls = data.year.lbls;
					   
			reloadPlot();
			enableUpdate();
		},
		error: function() {
			alert("Errore ottenimento dati aggiornati");
		},
		timeout: 5000
	});
}
