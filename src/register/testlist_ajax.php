<?php
// Ajax script to retrieve the tests yet to do of a given class
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
chk_access(2);
connect();

// Statement to find tests in the favourite list of the user
// not yet done by the class
$test_st = prepare_stmt("SELECT id_test, nometest FROM TEST
	JOIN PROF_TEST ON fk_test=id_test
	WHERE id_test NOT IN (
		SELECT DISTINCT(fk_test) FROM PROVE JOIN ISTANZE ON fk_ist=id_ist WHERE fk_cl=?
	) AND fk_prof=? 
	ORDER BY nometest");
$test_st->bind_param("ii", $_GET['id'], $_SESSION['id']);
$ret = execute_stmt($test_st);
$test_st->close();

$data = "";
while($row = $ret->fetch_assoc())
 	 $data .= "<option value='".$row['id_test']."'>".$row['nometest']."</option>";
          
echo json_encode($data);
?>