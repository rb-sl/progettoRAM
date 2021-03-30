// Functions related to log's ajax requests

// Flag used to know which element is
// selected and modify it
var selected;

// Requests the content of a log file
$(".splog").click(function() {
	$.ajax({                                      
		url: "log_reader.php",
		data: "f=" + $(this).text(),
		dataType: "json",                
		success: function(data) {
			$("#txt").text(data);
		},
		error: function() {
			alert("Errore richiesta log");
		}
	});
	
	$("#" + selected).css("color", "black");
	$(this).css("color", "red");
	selected = $(this).attr("id");
	$("#del").attr("disabled", false);
});
    
// Requests the deletion of a log file
$("#del").click(function() {
	$.ajax({                                      
		url: "log_delete.php",
		data: "f=" + $("#" + selected).text(),
		dataType: "json",                
		success: function(data) {
			$("#" + selected).remove();
			$("#txt").text("");
			$("#del").attr("disabled", true);
		},
		error: function() {
			alert("Errore cancellazione");
		}
	});
});
