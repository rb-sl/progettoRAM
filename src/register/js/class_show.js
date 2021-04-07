// Javascript / jQuery functions connected to show_classe.php 

// Ajax function to receive and output the list of tests not yet
// done by the class
$("#btnadd").click(function() {
	$.ajax({                                      
		url: "/register/testlist_ajax.php",
		data: "id=" + id,
		dataType: "json",
		success: function(data) {
			if(data === null) {
				window.location.reload();
			}
			
			// Adds the select with the possible tests
			var select = "<td class='new col topfix testadd'>"
				+ "	<select id='test' name='test' class='form-control' required>"
				+ "<option selected disabled></option>";
			Array.from(data).forEach(function(test) {
				select += "<option value='" + test.id + "'>" + test.name + "</option>";
			});
			$("#thr").append(select + "</select></td>");

			// Adds an input field for each student (checks for valid inputs)
			$(".tdr").each(function() {
				var id = $(this).attr("id").substr(2);
				$(this).append("<td class='new'>"
					+ "<input type='number' id='n" + id + "' class='in_add datmod testinput'"
					+ " name='ntest[" + id + "]' pattern='^[+-]?\\d+(\\.\\d+)?$'> " 
					+ "<span class='udm'></span></td>");
			});
			
			// Moves the table to show the new entries
			$("#tos").scrollLeft(1000);

			// Fallback for browser that do not support the 
			// width: max-content css property
			if(parseInt($(".testadd").width()) < parseInt($("#test").outerWidth())) {
				$(".testadd").css("width", $("#test").outerWidth() + 10);
			}

			$("#btnadd").hide();            	
			$("#btncar").show();
			$("#btncan").show();

			resizeText();
		},
		error: function() {
			alert("Errore richiesta test");
		},
		timeout: 5000
	});
});

// Handler of the button that cancels the new inputs
$("#btncan").click(function() {
	$(".new").remove();
	$(".datmod").each(function() {
		$(this).closest("td").html($(this).attr("prev"));
	});
						
	$("#btnadd").show();            	
	$("#btncar").hide();
	$("#btncan").hide();

	resizeText();
});

// Function to perform a synchronous (otherwise the rest
// of the script would go on) ajax request 
// and get the unit of the selected test.
function unitAjax(test) {
	var d;

	$.ajax({                                      
		url: "/register/unit_ajax.php",   
		data: "test=" + test, 
		dataType: "json",
		async: false,     
		success: function(data) {
			if(data === null) {
				window.location.reload();
			}
			
			d = data;
		},
		error: function() {
			alert("Errore test");
		},
		timeout: 5000
	});

	return d;
}
    
// Requests the unit on change of the new test 
$(document).on("change", "#test", function() {
	var data = unitAjax($("#test").val());

	$(".udm").html(data.simbolo);
	$(".in_add").attr("step", data.passo);

	resizeText();
});
    
// Function to enable the update of table values by double-clicking on them
$(document).on("dblclick", ".jdat", function() {
	if($(this).html().indexOf("input") === -1) {
		// Content of the cell
		var inner = $(this).html().split(" ");
		// Test's id
		var test = $(this).attr("id").substr($(this).attr("id").indexOf("_") + 1);
		// Student's id
		var stud = $(this).attr("id").substr(0, $(this).attr("id").indexOf("_"));
		// Test's step
		var step;

		// Ajax request to know the unit and update the step
		var data = unitAjax(test);
		inner[1] = data.simbolo;
		step = data.passo;
	
		// Includes a pattern to accept only values like +- n.nn with the step defined in the database
		$(this).html("<input type='number' size='5' class='datmod'"
			+ " name='pr[" + test + "][" + stud + "]' id='i" + $(this).attr("id") + "' prev='"
			+ $(this).html() + "' value='" + inner[0] 
			+ "' pattern='^[+-]?\\d+(\\.\\d+)?$' step='" + step + "'> " + inner[1]);
		
		$("#btncar").show();
		$("#btncan").show();

		resizeText();
	}
});
	
// Input check before submitting (through a synchronous ajax function
// to block the page while waiting the response)
$("#frm").on("submit", function(e) {
	$.ajax({
		type: "POST",
		async: false,
		url: "/register/result_check_ajax.php",
		data: $(this).serialize(),
		dataType: "json",
		success: function(data) {
			if(data === null) {
				window.location.reload();
			}

			// If data is returned as an object some values are out of range
			if(jQuery.type(data) == "object") {     
				e.preventDefault();      

				// Highlights the wrong values by reusing button properties, 
				// both for updated and new values
				$.each(data.pr, function(ist, test) {
					$("#i" + ist + "_" + test).addClass("wrongvalue");
				});
			
				$.each(data.ntest, function(test, id) {
					$("#n" + id).addClass("wrongvalue");
				});
								
				alert("Alcuni dati non sono conformi ai valori presenti nel sistema; "
					+ "controllare l'inserimento.\n"
					+ "Per ulteriori informazioni, consultare il manuale");
			}
		},
		error: function() {
			e.preventDefault();  
			alert("Errore su controllo valori");
		}			
	});
});

// Resizes the text on load
resizeText();
