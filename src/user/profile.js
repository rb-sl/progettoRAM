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

// Scripts used in profile.js

// Checks for password equality and shows an error message if needed
$(".password").keyup(function() {
	if($("#password").val() == $("#cpsw").val()) {
		$("#submit").removeAttr("disabled");
		$("#err").text("");
	}
	else {
		$("#submit").attr("disabled", true);
		$("#err").html("Le password inserite non coincidono!<br>");
	}
});

// Shows or hides the password fields
$("#btnpass").click(function() {
	if($("#pass").is(":visible")) {
		  $("#pass").hide();

		  $(".password").removeAttr("required");
		  $(".password").val("");

		  $("#submit").removeAttr("disabled");
		  $("#err").text("");
		  $("#btnpass").html("Modifica password");
	}
	else {
		  $("#pass").show();
		  $(".password").attr("required", true);
		  $("#btnpass").html("Annulla modifica password");
	}
});
