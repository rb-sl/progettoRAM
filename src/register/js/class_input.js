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

// Collection of JavaScript functions used in class_add.php 
// and class_modify.php

// Counter for new students
var i = 1;
	
// Modifies the following year when modifying the first
$(".class_year").keyup(function() {
	if($(this).val().length == 4) {
		$("#update").removeClass("btn-success");
		$("#update").addClass("btn-warning");
	
		$("#flw" + $(this).attr("id")).html(parseInt($(this).val()) + 1);
	}
});

// Shows the option to promote a class (only class_add.php)
$("#prom").click(function() {
	$(this).hide();
	$(".dpro").show();
});

// Handles the ajax request to get the promoted class's student
$("#clpr").change(function() {
	$.ajax({                                      
		url: "/register/class_promote_ajax.php",
		data: "toprom=" + $("#clpr").val(),
		dataType: "json",
		success: function(data) {
			if(data === null) {
				window.location.reload();
			}

			$("#cl").val(data.cl);
			$("#section").val(data.section);
			$("#year1").val(data.class_year);
			$("#year2").text(parseInt(data.class_year) + 1);
			
			$("#divpro").html(data.list);
		},
		error: function() {
			alert("Errore richiesta studenti");
		},
		timeout: 5000
	});
});

// Function to add a new student to the list when the last element of the list
// is modified
$("#tabadd").on("keyup change", ".last", function() {
	if($(this).val())
	{
		// Adds a new element
		$($(this).closest("table")).append("<tr id='r" + i + "'>" 
			+ "<td><input type='text' id='c" + i + "' class='last n" + i + "' name='lcst[" + i + "]' placeholder='Cognome'></td> "
			+ "<td><input type='text' id='nm" + i + "' class='n" + i + "' name='nst[" + i + "]' placeholder='Nome'></td> "
			+ "<td class='containerflex'><div class='form-check'>"
				+ "<input type='radio' id='m" + i + "' class='n" + i + " form-check-input' name='gender[" + i + "]' value='m'>"
				+ "<label class='form-check-label' for='m" + i + "'>M</label>"
			+ "</div><div class='form-check'>"
				+ "<input type='radio' id='f" + i + "' class='n" + i + " form-check-input' name='gender[" + i + "]' value='f'>"
				+ "<label class='form-check-label' for='f" + i + "'>F</label>"
			+ "</div>"); 

		i++;
		// Changes the last element
		$(this).removeClass("last");
		$(this).addClass("prev");
		// Removes the marker of last
		$(this).attr("name", $(this).attr("name").substring(1));
		// Makes required the element
		$(".n" + $(this).attr("id").substring(1)).prop("required", true);
		$(this).addClass("tocheck");
	}
});

// Function to remove a row if emptied
$("#tabadd").on("keyup change", ".prev", function() {
	if(!$(this).val()) {
		$(".last").closest("tr").remove();

		$(this).prev().addClass("prev");
		$(this).addClass("last");
		$(this).removeClass("prev");
		$(this).attr("name", "l" + $(this).attr("name"));

		// Moves this element to end
		$(this).closest("table").append($(this).closest("tr"));
		$(this).closest("tr").children().children().prop("required", false);
		$(this).removeClass("tocheck");
	}
});

// Makes required the student's fields if they are marked for promotion
$("#divpro").on("click", ".chkpro", function() {
	if($(this).prop("checked")) {
		$(this).closest("tr").removeClass("inactivetext");
		$("#n" + $(this).attr("id").substring(1)).prop("required", true);
	}
	else {
		$(this).closest("tr").addClass("inactivetext");
		$("#n" + $(this).attr("id").substring(1)).prop("required", false);
	}
});

// Function to check if there already exist students with corresponding data
// to some created as new; in this case the submission is blocked and it is
// asked to link the students
$("#frm").on("submit", function(e) {
	if($(".tocheck")[0]) {
		var count;
		var get = [];
		var tmp = {};
		
		$(".tocheck").each(function() {
			tmp = {};
			count = $(this).attr("id").substring(1);
		
			tmp.lastname = $(this).val();
			tmp.firstname = $("#nm" + count).val();
		
			if($("#m" + count).is(":checked"))
				tmp.gender = "M";
			else
				tmp.gender = "F";
		
			get.push(tmp);
		
			$(this).removeClass("tocheck");
		});
	
		// Synchronous ajax request to block the insert on possible duplicates
		$.ajax({
			url: "/register/student_duplicate_ajax.php",
			data: "cl={\"class\":\"" + $("#cl").val() + "\",\"class_year\":\"" + $("#year1").val() + "\"}&st=" + JSON.stringify(get),
			async: false,
			dataType: "json",
			success: function(data) {
				if(data === null) {
					window.location.reload();
				}
				
				if(data.length > 0) {
					e.preventDefault();
					var toprint = "";
					
					$.each(data, function(i) {
						toprint += "<tr class='borderover'><td>" + data[i].lastname + " " 
							+ data[i].firstname + " (" + data[i].gender + ")</td><td class='textleft'>";

						$.each(data[i].list, function(k) {
							toprint += "<label>" + data[i].list[k] + "</label><br>";
						});

						toprint += "<div class='form-check'>"
							+ "<input type='radio' id='new' class='form-check-input'  name='ext[" + data[i].lastname + "_" 
							+ data[i].firstname + "_" + data[i].gender + "]' value='new'>"
							+ "<label class='form-check-label' for='new'>Nuovo</label>"
							+ "</div>"
							+ "</td></tr>";
						
						$("#r" + i).remove();
					});
				
					$("#tabext").append(toprint);
					$("#ext").show();
					alert("I dati di alcuni nuovi studenti sono già presenti nel database. Selezionarne la provenienza");
				}
			},
			error: function() {
				alert("Errore controllo duplicati");
			}			
		});
	}
});
