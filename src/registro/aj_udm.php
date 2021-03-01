<?php
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
chk_access(2);
connect();

$unit_st = prepare_stmt("SELECT simbolo, passo FROM UNITA JOIN TEST ON fk_udm=id_udm
JOIN TIPOTEST ON fk_tipot=id_tipot 
WHERE id_test=?");
$unit_st->bind_param("i", $_GET['test']);

$udm = execute_stmt($unit_st);
$data = $udm->fetch_assoc();

echo json_encode($data);
?>