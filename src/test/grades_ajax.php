<?php
// Retrieves grade levels for the given user from the database on an ajax request
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
chk_access(0);
connect();
     
$gr_st = prepare_stmt("SELECT * FROM VALUTAZIONI JOIN VOTI ON fk_voto=id_voto WHERE fk_prof=?");
$gr_st->bind_param("i", $_GET['idprof']);

$ret = execute_stmt($gr_st);
$gr_st->close();

while($row = $ret->fetch_assoc())
	$data[$row['voto'] * 10] = $row['perc'];

echo json_encode($data);
?>