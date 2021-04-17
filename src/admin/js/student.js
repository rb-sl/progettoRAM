// JS function used in student_correction.php

// Function to show the pertinent fields in the forms 
$(".method").click(function() {
	var target = $(this).attr("id").charAt(0);
	
	if($(this).is(":checked") && $(this).val() == "id") {
		$("." + target + "_src_id").removeClass("jQhidden");
		$("." + target + "_src_name").addClass("jQhidden");
	}
	else {
		$("." + target + "_src_id").addClass("jQhidden");
		$("." + target + "_src_name").removeClass("jQhidden");
	}

	$("#" + target + "_stage1").removeClass("jQhidden");
	$("#" + target + "_clear").removeClass("jQhidden");
});

// Function to search for students to merge
$("#m_src").click(function() {
	var key =  $("input[name=m_method]:checked").val();

	if(key === undefined) {
		alert("Selezionare il metodo di ricerca");
		return;
	}

	$.ajax({                            
		url: "/admin/student/student_info_ajax.php",
		data: "key=" + key + "&id1=" + $("#m_id1").val() + "&id2=" + $("#m_id2").val() 
			+ "&name1=" + $("#m_name1").val() + "&surname1=" + $("#m_surname1").val()
			+ "&name2=" + $("#m_name2").val() + "&surname2=" + $("#m_surname2").val(),
		dataType: "json",
		success: function(data) {
			if(data === null) {
				window.location.reload();
			}

			$("#m_info1").html(getStudentsList(1, data.stud1));
			$("#m_info2").html(getStudentsList(2, data.stud2));

			$("#m_stage2").removeClass("jQhidden");
		},
		error: function() {
			alert("Errore richiesta informazioni");
		}
	});
});

// Function to search for a student to split
$("#s_src").click(function() {
	var key =  $("input[name=s_method]:checked").val();

	if(key === undefined) {
		alert("Selezionare il metodo di ricerca");
		return;
	}

	$.ajax({                            
		url: "/admin/student/student_info_ajax.php",
		data: "key=" + key + "&id1=" + $("#s_id1").val() + "&name1=" + $("#s_name1").val() 
			+ "&surname1=" + $("#s_surname1").val(),
		dataType: "json",                
		success: function(data) {
			if(data === null) {
				window.location.reload();
			}

			$("#s_info").html(getStudentsList(0, data.stud1));

			$("#s_stage2").removeClass("jQhidden");

			var cl1 = "";
			var cl2 = "";
			$.each(data.stud1[0].classlist, function(i, sclass) {
				cl1 += "<div class='form-check flexrow studentcard'>" 
				+ "		<input id='cl1_" + i + "' class='form-check-input' type='radio' name='split[" + i + "]' value='1' required>"
				+ "		<label class='form-check-label flexform' for='cl1_" + i + "'>"
				+ sclass
				+ "		</label>" 
				+ "</div>";

				cl2 += "<div class='form-check flexrow studentcard'>" 
				+ "		<input id='cl2_" + i + "' class='form-check-input' type='radio' name='split[" + i + "]' value='2' required>"
				+ "		<label class='form-check-label flexform' for='cl2_" + i + "'>"
				+ sclass
				+ "		</label>" 
				+ "</div>";
			});

			$("#classlist1").html(cl1);
			$("#classlist2").html(cl2);
		},
		error: function() {
			alert("Errore richiesta informazioni");
		}
	});
});

// Function to create one student's element
function getStudentsList(column, student) {
	var info = "";

	if(student) {		
		$.each(student, function(i, st) {
			var classes = "Classi: ";

			$.each(st.classlist, function(i, cl) {
				classes += "<div>" + cl + "</div>";
			});

			info += "<div class='form-check flexrow studentcard'>" 
				+ "		<input id='stud" + column + "_" + st.id + "' class='form-check-input student' " 
				+ " 		type='radio' name='stud" + column + "' value='"+ st.id + "' required>"
				+ "		<label class='form-check-label flexform' for='stud" + column + "_" + st.id 	+ "'>" 
				+ "			<div>Id: <span id='id" + column + "'>" + st.id + "</span></div>" 
				+ "			<div>Nome: <span id='name" + column + "'>" + st.name + "</span></div>" 
				+ "			<div>Cognome: <span id='surname" + column + "'>" + st.surname + "</span></div>"
				+ "			<div>Sesso: <span id='gender" + column + "'>" + st.gender + "</div>"
				+ "			<div>" + classes + "</div>"
				+ "		</label>" 
				+ "</div>";
		});
	}
	else {
		info += "<span class='dangercolor'>Nessuno studente trovato</span>";
	}

	return info;
}

// Function to avoid the selection of the same student in both
// columns
$(document).on("click", ".student", function() {
	$(".student").prop("disabled", false);

	$(".student:checked").each(function() {
		var num = $(this).attr("id").substr(4, 1) % 2 + 1;
		var id = $(this).attr("id").substr($(this).attr("id").indexOf("_") + 1);

		$("#stud" + num + "_" + id).prop("disabled", true);
	});
});

// Handler for the continue button in unification
$(".go").click(function() {
	var target = $(this).attr("id").charAt(0);

	$(".student:not(:checked)").prop("disabled", true);
	$("#" + target + "_stage0").addClass("jQhidden");
	$(this).hide();
	$("#" + target + "_stage3").removeClass("jQhidden");
});

// Handler for the reset button on union
$(".clear").click(function() {
	var target = $(this).attr("id").charAt(0);

	$("#" + target + "_stage0").removeClass("jQhidden");
	$("#" + target + "_stage1").addClass("jQhidden");
	$("#" + target + "_stage2").addClass("jQhidden");
	$("#" + target + "_stage3").addClass("jQhidden");
	
	$(this).addClass("jQhidden");
});
