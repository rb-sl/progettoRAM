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

// Functions for unit.php, test_type.php, test_class.php

// Function to show the form to create a new element
$("#newrow").click(function() {
	$(this).hide();

	var append = "<tr><td><input type='text' class='form-control textcenter' name='newrow1'></td>";

	if(typeof col2type !== "undefined") {
		append += "<td><input type='" + col2type + "' class='form-control textcenter' name='newrow2'></td>";
	}

	$("#datatable").append(append + "<td></td></tr>");

	$("#submit").show();
});

// Function to transform a row into a form
$(".mod").click(function() {
	id = $(this).attr("id").substr(4);
	
	$("#c1_" + id).html("<input type='text' class='form-control textcenter' name='col1[" + id + "]' value=\"" 
		+ $("#c1_" + id).text() + "\">");
	
	if(typeof col2type !== "undefined") {
		$("#c2_" + id).html("<input type='" + col2type + "' class='form-control textcenter' name='col2[" + id + "]' value=" 
			+ $("#c2_" + id).text() + ">");
	}

	$(this).hide();

	$("#submit").show();
});
