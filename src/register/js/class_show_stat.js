// Collection of JavaScript function used in class_show_stat.php 

// Function to add and remove colors to table entries
$("#btncol").click(function(){
	var app;
	
	$(".jcol").each(function(){
		app = $(this).css("background-color");
		$(this).css("background-color", $(this).attr("vcolor"));
		$(this).attr("vcolor", app);
	});

	if($(this).hasClass("btn-primary"))
		$(this).removeClass("btn-primary");
	else
		$(this).addClass("btn-primary");
});

// Statistical menu update handler
$("#update").click(function(){
	getData();
});

// Select change handler
$("#vis").change(function(){
	getData();
});

// Function to update table data based on the choices of the user  
function getData(){
	if(!checkYears())
		return;

	// Disables the possibility to make another request while this one
	// is running
	$("#update").attr("disabled", true);
	$("#vis").attr("disabled", true);
	
	$(".jcol").text("-");

	// Removes the colors before making the update
	if($("#btncol").hasClass("btn-primary"))
		$(".jcol").each(function(){
			app = $(this).css("background-color");
			$(this).css("background-color", $(this).attr("vcolor"));
			$(this).attr("vcolor",app);
		});

	// If grades are requested, averages are broken into first or second quadrimester		
	if($("#vis").val() == "gr"){
		$("#med1").html("Medie I<br>quadrimestre");
		$("#med2").html("Medie II<br>quadrimestre");
	}
	else{
		$("#med1").html("Medie<br>studenti");
		$("#med2").html("Mediane<br>studenti");
	}

	// Builds the condition from buttons
	cond = buildCondFromMenu();

	// Performs the ajax request
	$.ajax({  
		url: "./class_stat_ajax.php",
		data: cond + "&vis=" + $("#vis").val() + "&forstud=" + forstud,
		dataType: "json",   
		async: false, // Synchronous to allow the update inside the function
		success: function(data){
			if(data[0] != null){
				var idc;
				var idr;
				var dat_vals = data[0]['val'];
				var dat_colors = data[0]['color'];
				var avg = data[1]['avg'];
				var med = data[1]['med'];
				var savg = data[1]['savg'];
				var smed = data[1]['smed'];
				var tavg = data[1]['tavg'];
				
				var text;
			
				$(".jdat").each(function(){
					idr = parseInt($(this).attr("id").substring(0, $(this).attr("id").lastIndexOf("_")));
					idc = parseInt($(this).attr("id").substring($(this).attr("id").lastIndexOf("_") + 1));
					
					// New data is displayed, or the color is removed if none is returned
					if(dat_vals[idr])
						if(dat_vals[idr][idc]){
							$(this).text(dat_vals[idr][idc]);
							$(this).attr("vcolor", "#" + dat_colors[idr][idc]);
						}
						else
							$(this).attr("vcolor", "");
					else
						$(this).attr("vcolor", "");
				});

				// Updates the averages of tests
				$(".jtavg").each(function(){
					idt = parseInt($(this).attr("id").substring(2));

					text = avg[idt]['val'];
					if(text == "")
						text = "-";

					$(this).text(text);
					$(this).attr("vcolor", "#" + avg[idt]['color']);
				});

				// Updates the medians of tests
				$(".jtmed").each(function(){
					idt = parseInt($(this).attr("id").substring(2));

					text = med[idt]['val'];
					if(text == "")
						text = "-";

					$(this).text(text);
					$(this).attr("vcolor", "#" + med[idt]['color']);
				});

				// Updates the averages of students
				$(".jsavg").each(function(){
					ids = parseInt($(this).attr("id").substring(2));

					if(savg && savg[ids]){
						text = savg[ids]['val'];
						if(text == "")
							text = "-";

						$(this).text(text);
						$(this).attr("vcolor", "#" + savg[ids]['color']);
					}
					else{
						$(this).attr("vcolor", "");
					}
				});

				// Updates the medians of students
				$(".jsmed").each(function(){
					ids = parseInt($(this).attr("id").substring(2));

					if(smed && smed[ids]){
						text = smed[ids]['val'];
						if(text == "")
							text = "-";

						$(this).text(text);
						$(this).attr("vcolor", "#" + smed[ids]['color']);
					}
					else
						$(this).attr("vcolor", "");
				});

				// Updates the total average
				text = tavg['val'];
				if(text == "")
					text = "-";
					
				$("#tavg").text(text);
				$("#tavg").attr("vcolor", "#" + tavg['color']);

				// Restores colors, if needed
				if($("#btncol").hasClass("btn-primary"))
					$(".jcol").each(function(){
						app = $(this).css("background-color");
						$(this).css("background-color", $(this).attr("vcolor"));
						$(this).attr("vcolor", app);
					});
			}
			else {
				// When no data is returned the table colors are emptied
				$(".jcol").each(function(){
					$(this).attr("vcolor", "");
					$(this).css("background-color", "");
				});
			}

			// Restores the possibility to query again for different statistics
			$("#vis").attr("disabled", false);
			$("#update").removeClass("btn-warning");
			$("#update").addClass("btn-primary");
			$("#update").attr("disabled", false);
		},
		error: function(){
			alert("Errore ottenimento statistiche aggiornate");
		},
		timeout: 5000
	});
}

resizeText();
