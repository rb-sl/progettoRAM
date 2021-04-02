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
