// Functions used by most register scripts

// Handles the average and median button,
// showing or hiding the information
$(function(){
	$("#btnstat").click(function(){
    	if($(this).hasClass("btn-primary"))
        {
        	$(".r_stat").hide();
        	$(this).removeClass("btn-primary");
        }
   	 	else
        {
        	$(".r_stat").show();
        	$(this).addClass("btn-primary");
        }
		resizeText();
    });
});

// Resizes table elements
function resizeText() {
	fitty(".resizetext", {
		minSize: 10,
		maxSize: 20
	});
}
