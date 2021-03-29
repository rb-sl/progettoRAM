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

    $(".menuyear").keyup(function(){
        if($(this).val().length == 4)
            $("#flwy" + $(this).attr("id").substr(1)).text(parseInt($(this).val()) + 1); 
        $("#update").addClass("btn-warning");
    });
});

// Builds the condition for ajax requests based on the menu elements
function buildCondFromMenu() {
	var cond = "";

	$(".stat").each(function() {
		if($(this).val() == "on")
			cond += "&" + $(this).attr("id") + "=1";
	});

	return "&year1=" + $("#y1").val() + "&year2=" + $("#y2").val() + cond;
}

// If years are not a numeric value or their order is wrong the
// execution is stopped and a message is shown to the user
function checkYears() {
	if(!$.isNumeric($("#y1").val()) || !$.isNumeric($("#y2").val()) 
            || !(parseInt($("#y1").val()) <= parseInt($("#y2").val()))) {
        	alert("Anni inseriti non validi");
        	return false;
        }
		return true;
}
