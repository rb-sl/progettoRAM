<?php     
echo json_encode(file_get_contents($_SERVER['DOCUMENT_ROOT']."/logs/".$_GET[f]));
?>