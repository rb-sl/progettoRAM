// Funzione per aggiornare il dato dell'anno scolastico sul nav

$(function(){    
	$(".anno").change(function(){
    	$("#update").removeClass("btn-success");
    	$("#update").addClass("btn-warning");
    
    	$("#flw"+$(this).attr("id")).html(parseInt($(this).val())+1);
   	});
	
});

$(function(){
	$(".stat").click(function(){
    	$("#update").removeClass("btn-success");
    	$("#update").addClass("btn-warning");
    
    	if($(this).val()=="on")
        {
        	$(this).removeClass("btn-primary");
        	$(this).val("off");
        }
    	else
        {
        	$(this).addClass("btn-primary");
        	$(this).val("on");
        }
    });
});

$(function(){
	
	function navbar()
	{
    	if($(window).width()>750)
        {
        	$(".pg-head").addClass("navbar-fixed-top");
        	$("#nav2").css("margin-top","50px");
        }
    	else
        {
        	$(".pg-head").removeClass("navbar-fixed-top");
 	        $("#nav2").css("margin-top","0px");
        }
	}

	$(document).ready(function(){
    	navbar();
    });

	$(window).resize(function(){
    	navbar();
    });

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
    });
});
