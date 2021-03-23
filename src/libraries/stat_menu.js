// Functions used by the statistical menu

// Sets the behaviour of the menu's elements
$(function(){
    $(".stat").click(function() {
        $("#update").addClass("btn-warning");
    
        if($(this).val() == "on") {
            $(this).removeClass("btn-primary");
            $(this).val("off");
        }
        else {
            $(this).addClass("btn-primary");
            $(this).val("on");
        }
    });
});

// Builds the condition for ajax requests based on the menu elements
function buildCondFromMenu() {
	var cond = "";
	$(".stat").each(function() {
		if($(this).val() == "on")
			cond += "&" + $(this).attr("id") + "=1";
	});
	return "id=" + id + "&year1=" + $("#a1").val() + "&year2=" + $("#a2").val() + cond;
}

// If years are not a numeric value or their order is wrong the
// execution is stopped and a message is shown to the user
function checkYears() {
	if(!$.isNumeric($("#a1").val()) || !$.isNumeric($("#a2").val()) 
            || !(parseInt($("#a1").val()) <= parseInt($("#a2").val()))) {
        	alert("Anni inseriti non validi");
        	return false;
        }
		return true;
}
