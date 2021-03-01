$(function(){
	var i=1;	
	// La classe last indica l'ultimo elemento contenente dati
	$("#tabadd").on("keyup change", ".last", function() {
    	if($(this).val())
        {
        	$($(this).closest("table")).append("<tr id='r"+i+"'> <td><input type='text' id='c"+i+"' class='last n"+i+"' name='lcst["+i+"]' placeholder='Cognome'></td> <td><input type='text' id='nm"+i+"' class='n"+i+"' name='nst["+i+"]' placeholder='Nome'></td> <td> <label><input type='radio' id='m"+i+"' class='n"+i+"' name='sesso["+i+"]' value='m'>M</label> <label><input type='radio' id='f"+i+"' class='n"+i+"' name='sesso["+i+"]' value='f'>F</label> </td> </tr>"); 
			i++;	
        	$(this).removeClass("last");
        	$(this).attr("name",$(this).attr("name").substring(1));
    		$(this).addClass("prev");
        	$(".n"+$(this).attr('id').substring(1)).prop("required",true);
        	$(this).addClass("tocheck");
        }
    });

	$("#tabadd").on("keyup change", ".prev", function() {
    	if(!$(this).val()){
        	$(".last").closest("tr").remove();
        	$(this).prev().addClass("prev");
        	$(this).addClass("last");
        	$(this).removeClass("prev");
        	$(this).attr("name","l"+$(this).attr("name"));
        	// Sposta il nuovo last in fondo e gli toglie required
        	$(this).closest("table").append($(this).closest("tr"));
        	$(this).closest("tr").children().children().prop("required",false);
            $(this).removeClass("tocheck");
        }
    });

    $("#divpro").on("click",".chkpro",function(){
    	if($(this).prop("checked")){
			$(this).closest("tr").css("color","black");
			$("#n"+$(this).attr("id").substring(1)).prop("required",true);
		}
    	else{
			$(this).closest("tr").css("color","#b0b0b0");
			$("#n"+$(this).attr("id").substring(1)).prop("required",false);
		}
    });

	$("#frm").on("submit",function(e){
    	if($(".tocheck")[0]){
        	var count;
        	var get=[];
        	var tmp={};
        	
        	$(".tocheck").each(function(){
            	tmp={};
            	count=$(this).attr("id").substring(1);
            
            	tmp['cogs']=$(this).val();
                tmp['noms']=$("#nm"+count).val();
            
            	if($("#m"+count).is(":checked"))
                	tmp['sesso']="M";
            	else
                	tmp['sesso']="F";
            
            	get.push(tmp);
            
            	$(this).removeClass("tocheck");
            });
       
        	$.ajax({
        		url: "aj_dup_stud.php",
        		data: "cl={\"classe\":\""+$("#cl").val()+"\",\"anno\":\""+$("#a1").val()+"\"}&st="+JSON.stringify(get),
            	async: false,
            	dataType: "json",
        		success: function(data){
                	if(data){
                    	e.preventDefault();
                    	var toprint="";
                		
            			$.each(data,function(i){
                        	toprint+="<tr style='border-top:1px solid black'><td>"+data[i]['cogs']+" "+data[i]['noms']+" ("+data[i]['sesso']+")</td><td style='text-align:left'>";
                        	$.each(data[i]['list'],function(k){
                            	toprint+="<label>"+data[i]['list'][k]+"</label><br>";
                            });
                        	toprint+="<label><input type='radio' name='ext["+data[i]['cogs']+"_"+data[i]['noms']+"_"+data[i]['sesso']+"]' value='new'> Nuovo</label></td></tr>";
                        // Va fatto escape !!!
                        	$("#r"+i).remove();
                    	});
                    
                    	$("#tabext").append(toprint);
                    	$("#ext").show();
                    	alert("I dati di alcuni nuovi studenti coincidono con quelli già presenti nel database. Selezionarne la provenienza");
                    }
        		},
        		error: function(){
  					alert("Errore ajax dup");
				}			
    		});
    	}
	});
});