<?php
// Back end script to answer to ajax queries on correlation statistics
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
include $_SERVER['DOCUMENT_ROOT']."/libraries/lib_stat.php";
chk_access(3);
connect();

$cond = cond_builder();
open_rvals_stmt($cond);

// Matrix are recomputed only if requested
if($_GET['upd'] != false)
{
	$testinfo = get_test_correlation($cond);
	$test = $testinfo['names'];
	$positive = $testinfo['positive'];
	$stats = $testinfo['statistics'];
	$testlist = $testinfo['list'];

	$data['splom'] = splom_graph($testlist, $cond);
	
	foreach($test as $idc => $colname)
		foreach($test as $idr => $rowname)
    		if($rowname <= $colname)
        	{
				// Simmetric construction
        		$data['matrix'][$idc][$idr] = calc_r($idc, $stats[$idc], $idr, $stats[$idr], $cond);
        		$data['matrix'][$idr][$idc] = $data['matrix'][$idc][$idr];
       	 	}
}

// Graph request
if($_GET['id1'] != -1)
{
	$r_id1 = $_GET['id1'];
	$r_id2 = $_GET['id2'];
	$retvals = execute_stmt($rval_st);

	while($row = $retvals->fetch_assoc())
    {
    	$data['test']['t1'][] = $row['v1'];
    	$data['test']['t2'][] = $row['v2'];
    }

	$test_st = prepare_stmt("SELECT nometest, simbolo 
		FROM TEST JOIN UNITA ON fk_udm=id_udm 
		WHERE id_test=?");
	$test_st->bind_param("i", $id);

	// First test's info
	$id = $_GET['id1'];
	$ret = execute_stmt($test_st);
	$row = $ret->fetch_assoc();
    
	$data['test']['n1'] = $row['nometest'];
	if($row['simbolo'])
		$data['test']['u1'] = " [".$row['simbolo']."]";
	else
    	$data['test']['u1'] = "";

	// Second test's info
	$id = $_GET['id2'];
	$ret = execute_stmt($test_st);
	$row = $ret->fetch_assoc();

    $data['test']['n2'] = $row['nometest'];
	if($row['simbolo'])
    	$data['test']['u2'] = " [".$row['simbolo']."]";
	else
    	$data['test']['u2'] = "";

	$test_st->close();
}
$rval_st->close();

echo json_encode($data)
?>
