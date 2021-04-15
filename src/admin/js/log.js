// Functions related to log's ajax requests

// Flag used to know which element is
// selected and modify it
var selected;

// Requests the content of a log file
$(".splog").click(function() {
	$.ajax({                                      
		url: "/admin/log_reader.php",
		data: "f=" + $(this).text(),
		dataType: "json",                
		success: function(data) {
			if(data === null) {
				window.location.reload();
			}
			
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
