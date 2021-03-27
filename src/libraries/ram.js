// Collection of functions used in all pages

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

function resizeText() {
	fitty(".resizetext", {
		minSize: 10,
		maxSize: 20
	});
}
