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

// Functions related to log's ajax requests

// Flag used to know which element is
// selected and modify it
var selected;

// Requests the content of a log file
$(".splog").click(function() {
	$.ajax({                                      
		url: "/admin/log_reader.php",
		data: "f=" + $(this).text(),
		dataType: "json",                
		success: function(data) {
			if(data === null) {
				window.location.reload();
			}
			
			$("#txt").text(data);
		},
		error: function() {
			alert("Errore richiesta log");
		}
	});
	
	$("#" + selected).css("color", "black");
	$(this).css("color", "red");
	selected = $(this).attr("id");
	$("#del").attr("disabled", false);
});
