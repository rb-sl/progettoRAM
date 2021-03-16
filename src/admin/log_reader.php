<?php
// Script to answeer to an ajax request with the content of a log file
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
chk_access(0);
echo json_encode(file_get_contents(LOG_PATH.$_GET['f']));
?>