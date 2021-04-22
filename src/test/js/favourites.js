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

// JS functions used in favourites_modify.php

$(".chkfav").change(function() {
	var id = $(this).attr("id").substr(3);

	$("#submit").attr("disabled", false);

	if($(this).prop("checked")) {
		$("#lbl" + id).removeClass("inactivetext");
		$("#btn" + id).removeClass("btn-secondary");
		$("#btn" + id).addClass("btn-primary");
	}
	else {
		$("#lbl" + id).addClass("inactivetext");
		$("#btn" + id).removeClass("btn-primary");
		$("#btn" + id).addClass("btn-secondary");
	}
});
