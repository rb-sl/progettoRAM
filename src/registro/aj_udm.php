<?php
include $_SERVER['DOCUMENT_ROOT']."/librerie/general.php";
chk_access(2);
connect();

$udm=query("SELECT simbolo,passo FROM UNITA,TEST,TIPOTEST WHERE fk_udm=id_udm AND fk_tipot=id_tipot AND id_test=".$_GET['test']);
$data=$udm->fetch_assoc();

echo json_encode($data);
?>