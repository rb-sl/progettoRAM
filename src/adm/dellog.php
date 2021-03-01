<?php
fclose($_SERVER['DOCUMENT_ROOT']."/logs/".$_GET[f]);
echo unlink($_SERVER['DOCUMENT_ROOT']."/logs/".$_GET[f]);
?>