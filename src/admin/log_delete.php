<?php
// Backend script to delete a log on an ajax request
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";

if(chk_access(ADMINISTRATOR, false))
    echo unlink(LOG_PATH.$_GET['f']);
else
    echo "null";
?>
