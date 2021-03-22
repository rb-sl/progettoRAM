<?php
include $_SERVER['DOCUMENT_ROOT']."/librerie/general.php";
include $_SERVER['DOCUMENT_ROOT']."/librerie/lib_stat.php";
chk_access(3);
connect();

$cond=cond_builder(false);

if($_GET['upd']!=false)
{
	$ret=query("SELECT id_test,nometest FROM TEST WHERE id_test IN (SELECT fk_test FROM PROVE GROUP BY fk_test) ORDER BY nometest");
	while($row=$ret->fetch_assoc())
		$test[$row['id_test']]=$row['nometest'];

	foreach($test as $idc => $nomec)
		foreach($test as $idr => $nomer)
    		if($nomer<=$nomec)
        	{
        		$data['matrix'][$idc][$idr]=calc_r($idc,$idr,$cond);
        		$data['matrix'][$idr][$idc]=$data['matrix'][$idc][$idr];
       	 	}
}

if($_GET['id1']!=-1)
{
	$retvals=r_vals($_GET['id1'],$_GET['id2'],$cond);

	while($row=$retvals->fetch_assoc())
    {
    	$data['test']['t1'][]=$row['v1'];
    	$data['test']['t2'][]=$row['v2'];
    }

	$ret=query("SELECT nometest,simbolo FROM TEST,UNITA WHERE fk_udm=id_udm AND id_test =".$_GET['id1']);
	$row=$ret->fetch_assoc();
    $data['test']['n1']=$row['nometest'];
	if($row['simbolo'])
		$data['test']['u1']=" [".$row['simbolo']."]";
	else
    	$data['test']['u1']="";

	$ret=query("SELECT nometest,simbolo FROM TEST,UNITA WHERE fk_udm=id_udm AND id_test =".$_GET['id2']);
	$row=$ret->fetch_assoc();
    $data['test']['n2']=$row['nometest'];
	if($row['simbolo'])
    	$data['test']['u2']=" [".$row['simbolo']."]";
	else
    	$data['test']['u2']="";
}

echo json_encode($data)

?>