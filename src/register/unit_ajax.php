<?php
// Ajax script to return unit of measure's information given the test
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
if(!chk_access(PROFESSOR, false))
{
	echo "null";
	exit;
}
connect();

$data['simbolo'] = "";
$data['passo'] = "";

$unit_st = prepare_stmt("SELECT simbolo, passo FROM UNITA JOIN TEST ON fk_udm=id_udm
    JOIN TIPOTEST ON fk_tipot=id_tipot 
    WHERE id_test=?");
$unit_st->bind_param("i", $_GET['test']);

$udm = execute_stmt($unit_st);
$unit_st->close();
if($udm->num_rows > 0)
    $data = $udm->fetch_assoc();

echo json_encode($data);
?>
