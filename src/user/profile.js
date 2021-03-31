// Checks for password equality and shows an error message if needed
$(".psw").keyup(function() {
    if($("#psw").val() == $("#cpsw").val()) {
        $("#submit").removeAttr("disabled");
        $("#err").text("");
    }
    else {
        $("#submit").attr("disabled", true);
        $("#err").html("Le password inserite non coincidono!<br>");
    }
});

// Shows or hides the password fields
$("#btnpass").click(function() {
    if($("#pass").is(":visible")) {
          $("#pass").hide();

          $(".psw").removeAttr("required");
          $(".psw").val("");

          $("#submit").removeAttr("disabled");
          $("#err").text("");
          $("#btnpass").html("Modifica password");
    }
    else {
          $("#pass").show();
          $(".psw").attr("required", true);
          $("#btnpass").html("Annulla modifica password");
    }
});
