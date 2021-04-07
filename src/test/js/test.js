// Javascript functions for test.php
	
// Blocks the update if percentages do not sum to 100
$("#grades").submit(function(e) {
	if($(".sum").html() != 100) {
		e.preventDefault();
		alert("Valore voti non valido\nLa somma dei punti percentili deve essere 100");
	}
});

// Function to create the grades' plot
function plotGrades() {
	var layout = {
		barmode: "stack",
		yaxis: {
			visible: false
		}
	};

	var data = [];
	var id;
	$(".range").each(function() {
		id = $(this).attr("id").substr(2);

		data.push({
			x: [$(this).val()],
			type: "bar",
			name: $("#x_" + id).text(),
			text: $("#x_" + id).text(),
			textposition: "auto",
			hoverinfo: "none",
			marker: {
				color: $("#x_" + id).css("background-color"),
				line: {
					color: "#000",
					width: 1.5
				}
			}
	  	});
	});
	
	Plotly.newPlot("cnv", data, layout, {responsive: true});
}

// Function to show the chosen user's grades (administrator only)
// Updates the graph and the table
$("#slp").change(function() {
	$.ajax({                                      
		url: "/test/grades_ajax.php",   
		data: "idprof=" + $(this).val(), 
		dataType: "json",                
		success: function(data)	{
			if(data === null) {
				window.location.reload();
			}

			var prev = 0;
			var id;

			// Updates input fields
			$(".range").each(function() {
				id = $(this).attr("id").substr(2);

				$(this).val(data[id] - prev);
				prev = parseInt(data[id]);
			});

			updateTable();
		} 
	});
});

// Function to update graph and table after a modification
$(".range").on("change", function() {
	updateTable();
});

// Function to update the percentile ranges
function updateTable() {
	// Plot update data
	var plotData = [];

	// Iterates over inputs to update ranges
	var top = 0;
	$(".range").each(function() {
		i = $(this).attr("id").substr(2);

		$("#i" + i).text(top);
		top += parseInt($(this).val());
		$("#f" + i).text(top);

		plotData.push({
			x: [$(this).val()]
		});
	});

	$(".sum").html($("#f100").html());
			
	// Displays an error if the total is wrong
	if(parseInt($(".sum").html()) != 100) {
		$(".err").addClass("wrongvalue");
	}
	else {
		$(".err").removeClass("wrongvalue");
	}

	// Reloads and animates the plot
	Plotly.animate("cnv", {
		data: plotData
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
