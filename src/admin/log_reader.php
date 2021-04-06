<?php
// Script to answer to an ajax request with the content of a log file
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";

// With wrong credentials nothing is shown to have ajax throw
// an error
if(chk_access(ADMINISTRATOR, false))
    echo json_encode(file_get_contents(LOG_PATH.$_GET['f']));
else
    echo "null";
?>
