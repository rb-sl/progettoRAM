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

// Functions used by most register scripts

// Handles the average and median button,
// showing or hiding the information
$("#btnstat").click(function() {
	if($(this).hasClass("btn-primary")) {
		$(".r_stat").hide();
		$(this).removeClass("btn-primary");
		$(this).addClass("btn-secondary");
	}
	else {
		$(".r_stat").show();
		$(this).removeClass("btn-secondary");
		$(this).addClass("btn-primary");
	}
	resizeText();
});

// Resizes table elements
function resizeText() {
	fitty(".resizetext", {
		minSize: 10,
		maxSize: 20
	});
}

// Hides all non-personal classes
$(".nonpersonal").hide();

// Function to show or hide non-personal classes
// for an admin in register.php
$("#showall").change(function() {
	$(".nonpersonal").toggle();
});
