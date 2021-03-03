$(function(){
	// Blocks the update if percentages do not sum to 100
	$("#voti").submit(function(e){
    	if($(".sum").html() != 100)
        {
        	alert("Valore voti non valido\nLa somma dei punti percentili deve essere 100");
        	e.preventDefault();
        }
	});

	// Function to show the chosen user's grades (administrator only)
	// Updates the graph and the table
	$("#slp").change(function(){
    	var prev = 0;
    	$.ajax({                                      
      		url: "grades_ajax.php",   
      		data: "idprof=" + $(this).val(), 
      		dataType: "json",                
      		success: function(data) 
      		{
        		for(var i = 40; i <= 100; i += 5)
                {
                	$("#r" + i).val(data[i] - prev);
                	$("#i" + i).html(prev);
                	$("#f" + i).html(data[i]);
                	prev = parseInt(data[i]);
                }
            	$(".sum").html($("#f100").html());
    			if(parseInt($(".sum").html()) != 100)
        			$(".err").css("color", "red");
    			else
        			$(".err").css("color", "black");
            
            	Plotly.animate("cnv", {
 					data: [
                    	{x: [data[40]]},
                    	{x: [data[45] - data[40]]},
                     	{x: [data[50] - data[45]]},
                     	{x: [data[55] - data[50]]},
                     	{x: [data[60] - data[55]]},
                     	{x: [data[65] - data[60]]},
                     	{x: [data[70] - data[65]]},
                     	{x: [data[75] - data[70]]},
                     	{x: [data[80] - data[75]]},
                     	{x: [data[85] - data[80]]},
                     	{x: [data[90] - data[85]]},
                     	{x: [data[95] - data[90]]},
                     	{x: [data[100] - data[95]]}
                    ],
    				traces: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
    				layout: {}
  				}, {
    				transition: {
      					duration: 500,
      					easing: "cubic-in-out"
    				},
    				frame: {
      					duration: 500
    				}
  				});
      		} 
    	});
    });

	// Function to update graph and table after a modification
	$(".range").on("change", function(){
    	var rng = parseInt($(this).val());
    
    	var id = parseInt($(this).attr("id").substr(1));
    	var iin = parseInt($("#i" + id).html());
    	var fin = parseInt($("#f" + id).html());
		
    	var mod = rng - (fin - iin);

    	for(var i = id; i <= 100; i += 5)
        {
        	$("#i"+ (i + 5)).html(parseInt($("#i" + (i + 5)).html()) + mod);
       		$("#f" + i).html(parseInt($("#f" + i).html()) + mod);
        }
    	$(".sum").html($("#f100").html());
    	if(parseInt($(".sum").html()) != 100)
        	$(".err").css("color", "red");
    	else
        	$(".err").css("color", "black");

        reloadPlot();
    });

    // Function that reloads the plot after modifications
    function reloadPlot() 
    {
        Plotly.animate("cnv", {
            data: [
                {x: [$("#r40").val()]},
                {x: [$("#r45").val()]},
                {x: [$("#r50").val()]},
                {x: [$("#r55").val()]},
                {x: [$("#r60").val()]},
                {x: [$("#r65").val()]},
                {x: [$("#r70").val()]},
                {x: [$("#r75").val()]},
                {x: [$("#r80").val()]},
                {x: [$("#r85").val()]},
                {x: [$("#r90").val()]},
                {x: [$("#r95").val()]},
                {x: [$("#r100").val()]}
            ],
			traces: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
            layout: {}
        }, {
            transition: {
                duration: 500,
                easing: "cubic-in-out"
            },
            frame: {
                duration: 200
            }
        });
    }
});