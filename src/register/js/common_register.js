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
