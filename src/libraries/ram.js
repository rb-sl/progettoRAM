// Collection of functions used in all pages

// Makes the menus not fixed if the window is too narrow,
// so that they don't cover content, and manages the main's
// margin
function navbar()
{
	$("footer").html(parseInt($(window).width()));
	if(parseInt($(window).width()) > 750)
	{
		$(".pg-head").addClass("navbar-fixed-top");
		

		if($("#nav2").length)
		{
			$("#nav2").css("margin-top", "50px");
			$("main").addClass("statwide");
		}
		else
			$("main").addClass("nostatwide");
	}
	else
	{
		$(".pg-head").removeClass("navbar-fixed-top");
		if($("#nav2").length)
		{
			$("#nav2").css("margin-top", "0px");
			$("main").removeClass("statwide");
		}
		else
			$("main").removeClass("nostatwide");
	}
}

$(document).ready(function(){
	navbar();
});

$(window).resize(function(){
	navbar();
});

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
