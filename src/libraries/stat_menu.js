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

// Functions used by the statistical menu

// Blocks an update if waiting for a previous one
var canUpdate = true;

// Sets the behaviour of the menu's elements
$(function(){
	$(".stat").click(function() {
		$("#update").addClass("btn-warning");
	
		if($(this).val() == "on") {
			$(this).removeClass("btn-primary");
			$(this).addClass("btn-secondary");
			$(this).val("off");
		}
		else {
			$(this).removeClass("btn-secondary");
			$(this).addClass("btn-primary");
			$(this).val("on");
		}
	});

	$(".menuyear").keyup(function() {
		if($(this).val().length == 4) {
			$("#flwy" + $(this).attr("id").substr(1)).text(parseInt($(this).val()) + 1); 
		}

		$("#update").addClass("btn-warning");
	});
});

// Builds the condition for ajax requests based on the menu elements
function buildCondFromMenu() {
	var cond = "";

	$(".stat").each(function() {
		if($(this).val() == "on") {
			cond += "&" + $(this).attr("id") + "=1";
		}
	});

	return "&year1=" + $("#y1").val() + "&year2=" + $("#y2").val() + cond;
}

// If years are not a numeric value or their order is wrong the
// execution is stopped and a message is shown to the user
function checkYears() {
	if(!$.isNumeric($("#y1").val()) || !$.isNumeric($("#y2").val()) 
			|| !(parseInt($("#y1").val()) <= parseInt($("#y2").val()))
			|| !($("#y1").val().length == 4 && $("#y2").val().length == 4)) {
			alert("Anni inseriti non validi");
			return false;
		}
		
	return true;
}

// Function to disable updates during functions executions
function disableUpdate() {
	$("*").addClass("loading");
	$(".trigger").attr("disabled", true);
}

// Function to reenable updates after functions end
function enableUpdate() {
	$("*").removeClass("loading");
	$(".trigger").attr("disabled", false);
	$("#update").removeClass("btn-warning");
	$("#update").addClass("btn-primary");
}
