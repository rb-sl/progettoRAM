<?php
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
chk_access(2);
connect();

$test_st = prepare_stmt("SELECT id_test, nometest FROM TEST 
	WHERE id_test NOT IN (
		SELECT DISTINCT(fk_test) FROM PROVE JOIN ISTANZE ON fk_ist=id_ist WHERE fk_cl=?
	) ORDER BY nometest");
$test_st->bind_param("i", $_GET['id']);
$ret = execute_stmt($test_st);

$data = "";
while($row = $ret->fetch_assoc())
 	 $data .= "<option value='".$row['id_test']."'>".$row['nometest']."</option>";
          
echo json_encode($data);
?>