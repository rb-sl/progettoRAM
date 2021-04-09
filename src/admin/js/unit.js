// Functions for unit.php

// Function to show the form to create a new unit
$("#newunit").click(function() {
    $(this).hide();

    $("#units").append("<tr><td><input type='text' class='form-control textcenter' name='newunit'></td>"
        + "<td><input type='text' class='form-control textcenter' name='symbol'></td><td></td></tr>");

    $("#submit").show();
});

// Function to transform a row into a form
$(".mod").click(function() {
    id = $(this).attr("id").substr(4);
    
    $("#u_" + id).html("<input type='text' class='form-control textcenter' name='unit[" + id + "]' value=" 
        + $("#u_" + id).text() + ">");
    $("#s_" + id).html("<input type='text' class='form-control textcenter' name='symbol[" + id + "]' value=" 
        + $("#s_" + id).text() + ">");
    $(this).hide();

    $("#submit").show();
});
