// Collection of JavaScript function used in class_show_stat.php 

// Function to add and remove colors to table entries
$("#btncol").click(function() {
	var app;
	
	$(".jcol").each(function() {
		app = $(this).css("background-color");
		$(this).css("background-color", $(this).attr("vcolor"));
		$(this).attr("vcolor", app);
	});

	if($(this).hasClass("btn-primary")) {
		$(this).removeClass("btn-primary");
		$(this).addClass("btn-secondary");
	}
	else {
		$(this).removeClass("btn-secondary");
		$(this).addClass("btn-primary");
	}
});

// Statistical menu update handler
$("#update").click(function() {
	getData();
});

// Select change handler
$("#vis").change(function() {
	getData();
});

// Function to update table data based on the choices of the user  
function getData() {
	if(!checkYears()) {
		return;
	}

	disableUpdate();

	// If grades are requested, averages are broken into first or second quadrimester		
	if($("#vis").val() == "gr") {
		$("#med1").html("Medie I<br>quadrimestre");
		$("#med2").html("Medie II<br>quadrimestre");
	}
	else {
		$("#med1").html("Medie<br>studenti");
		$("#med2").html("Mediane<br>studenti");
	}

	// Builds the condition from buttons
	cond = buildCondFromMenu();

	// Performs the ajax request
	$.ajax({  
		url: "/register/class_stat_ajax.php",
		data: "id=" + id + "&vis=" + $("#vis").val() + "&forstud=" + forstud + cond,
		dataType: "json",
		success: function(data) {
			if(data != null) {
				// Variables to store ids
				var idc;
				var idr;
				// Variables used for clarity
				var dat_vals = data.val;
				var dat_colors = data.color;
				var avg = data.avg;
				var med = data.med;
				var savg = data.savg;
				var smed = data.smed;
				var tavg = data.tavg;
			
				$(".jdat").each(function() {
					idr = parseInt($(this).attr("id").substring(0, $(this).attr("id").lastIndexOf("_")));
					idc = parseInt($(this).attr("id").substring($(this).attr("id").lastIndexOf("_") + 1));
					
					// New data is displayed, or the color is removed if none is returned
					if(dat_vals[idr] !== undefined && dat_vals[idr][idc] !== undefined) {
						$(this).text(dat_vals[idr][idc]);
						updateColor($(this), dat_colors[idr][idc]);
					}
					else {
						$(this).text("-");
						$(this).attr("vcolor", "");
						$(this).css("background-color", "");
					}
				});

				// Updates the averages of tests
				$(".jtavg").each(function() {
					idt = parseInt($(this).attr("id").substring(2));

					$(this).text(avg[idt].val);
					updateColor($(this), avg[idt].color);
				});

				// Updates the medians of tests
				$(".jtmed").each(function() {
					idt = parseInt($(this).attr("id").substring(2));

					$(this).text(med[idt].val);
					updateColor($(this), med[idt].color);
				});

				// Updates the averages of students
				$(".jsavg").each(function() {
					ids = parseInt($(this).attr("id").substring(2));

					if(savg[ids] != undefined) {
						$(this).text(savg[ids].val);
						updateColor($(this), savg[ids].color);
					}
					else {
						$(this).text("-");
						$(this).attr("vcolor", "");
						$(this).css("background-color", "");
					}
				});

				// Updates the medians of students
				$(".jsmed").each(function() {
					ids = parseInt($(this).attr("id").substring(2));

					if(smed[ids] !== undefined) {
						$(this).text(smed[ids].val);
						updateColor($(this), smed[ids].color);
					}
					else {
						$(this).text("-");
						$(this).attr("vcolor", "");
						$(this).css("background-color", "");
					}
				});

				// Updates the total average									
				$("#tavg").text(tavg.val);
				updateColor($("#tavg"), tavg.color);
			}
			else {
				// When no data is returned the table is emptied
				$(".jcol").text("-");
				$(".jcol").attr("vcolor", "");
				$(".jcol").css("background-color", "");
			}

			// Plots the graph if requested by the student page
			if(forstud) {
				drawRadarGraph();
			}

			// Restores the possibility to query again for different statistics
			enableUpdate();
		},
		error: function() {
			alert("Errore ottenimento statistiche aggiornate");
		},
		timeout: 5000
	});
}

// Function to change the color of a cell
function updateColor(object, color) {
	if(color != "") {
		color = "#" + color;
	}

	if($("#btncol").hasClass("btn-primary")) {
		object.css("background-color", color);
	}
	else {
		object.attr("vcolor", color);
	}
}

resizeText();
