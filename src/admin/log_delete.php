<?php
// Backend script to delete a log on an ajax request
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
chk_access(0);
echo unlink(LOG_PATH.$_GET['f']);
?>