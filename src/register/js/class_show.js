// Javascript / jQuery functions connected to show_classe.php 
$(function(){
	// Ajax function to receive and output the list of tests not yet
    // done by the class
    $("#btnadd").click(function(){
      	$.ajax({                                      
        	url: "testlist_ajax.php",
			data: "id=" + id,
        	dataType: "json",
        	async: false, // Handled synchronously
        	success: function(data){
                // Adds the select with the possible tests
          		$("#thr").append("<td class='new col topfix testadd'>"
					+ "	<select id='test' name='test' class='form-control' required>"
					+ "		<option selected disabled></option>"
					+ data
					+ "	</select></td>");

                // Adds an input field for each student (checks for valid inputs)
            	$(".tdr").each(function(){
					var id = $(this).attr("id").substr(2);
        			$(this).append("<td class='new'>"
					+ "<input type='number' id='n" + id + "' class='in_add input testinput'"
					+ " name='ntest[" + id + "]' pattern='^[+-]?\\d+(\\.\\d+)?$'> " 
					+ "<span class='udm'></span></td>");
      			});
				
                // Moves the table to show the new entries
				$("#tos").scrollLeft(1000);
            
            	$("#btnadd").hide();            	
            	$("#btncar").show();
            	$("#btncan").show();
        	},
        	error: function(){
        		alert("Ajax error (Test selection)");
      		},
        	timeout: 5000
      	});
		resizeText();
	});

    // Function for the button that cancels new tests' input
	$("#btncan").click(function(){
    	$(".new").remove();
    	$(".datmod").each(function(){
        	$(this).closest("td").html($(this).attr("prev"));
        });
                          
    	$("#btnadd").show();            	
        $("#btncar").hide();
        $("#btncan").hide();
		resizeText();
    });

    // Function to perform an ajax request to get the unit of 
    // the selected test.
    function unitAjax(test)
    {
        var d;
        $.ajax({                                      
			url: "unit_ajax.php",   
			data: "test=" + test, 
			dataType: "json",
            async: false, // To give enough time for d being written        
			success: function(data){
        		d = data;
      		},
        	error: function(){
        		alert("Errore test");
      		},
        	timeout: 5000
		});

        return d;
    }
    
    // Requests the unit on change of the new test 
	$(document).on("change", "#test", function(){
        var data = unitAjax($("#test").val());

        $(".udm").html(data['simbolo']);
        $(".in_add").attr("step", data['passo']);

		resizeText();
	});
    
    // Function to enable the update of table values by double-clicking them
	$(document).on("dblclick", ".jdat", function(){
    	if($(this).html().indexOf("input") === -1){
			var inner = $(this).html().split(" ");
        	var test = $(this).attr("id").substr($(this).attr("id").indexOf("_") + 1);
        	var stud = $(this).attr("id").substr(0, $(this).attr("id").indexOf("_"));
        	var step;

            // Ajax request to know the unit and update the step
            var data = unitAjax(test);
            inner[1] = data['simbolo'];
            step = data['passo'];
    	
        	// Includes a pattern to accept only values like +- n.nn with the step defined in the database
    		$(this).html("<input type='number' size='5' class='datmod'"
				+ " name='pr[" + test + "][" + stud + "]' id='i" + $(this).attr("id") + "' prev='"
				+ $(this).html() + "' value='" + inner[0] 
				+ "' pattern='^[+-]?\\d+(\\.\\d+)?$' step='" + step + "'> " + inner[1]);
        	
        	$("#btncar").show();
			$("#btncan").show();
			resizeText();
        }
	});
	
	// Input check before submit through an ajax function
	$("#frm").on("submit", function(e){
    	$.ajax({
        	type: "POST",
			async: false, // Blocks waiting for response
        	url: "result_check_ajax.php",
        	data: $(this).serialize(), // Sends the data to be validated
        	dataType: "json",
        	success: function(data) {
                // If data is returned some values are out of range
            	if(jQuery.type(data) == "object"){        
					e.preventDefault();      

                    // Highlights the wrong values, both for updated and new values
                	$.each(data['pr'], function(ist, test){
                    	$("#i" + ist + "_" + test).css("background-color", "red");
                    	$("#i" + ist + "_" + test).css("color", "white");
                    });
                
                	$.each(data['ntest'], function(test, id){
                    	$("#n" + id).css("background-color", "red");
                    	$("#n" + id).css("color", "white");
					});
					              	
                	alert("Alcuni dati non sono conformi ai valori presenti nel sistema.\n"
                        + "Controllare l'inserimento. "
                        + "Per ulteriori informazioni, consultare il manuale");
                }
        	},
        	error: function(){
				e.preventDefault();  
  				alert("Ajax error (Insert check)");
			}			
    	});
	});

    // Resizes the text
    resizeText();
});

