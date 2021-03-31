<?php
// Script to answer to ajax request about test data
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
include $_SERVER['DOCUMENT_ROOT']."/libraries/lib_stat.php";
chk_access(3);
connect();

$cond = cond_builder();

$upvals = get_stats($_GET['id'], $cond);
$rec = get_records($_GET['id'], $cond);

switch($_GET['graph'])
{
	case "val":
	case "box":
		$graph['plot'] = graph_vals($_GET['id'], $cond);
		break;
	case "prc":
		$graph['plot'] = graph_prc($_GET['id'], $cond);
		break;
	case "hbox":
		$graph['plot'] = graph_multibox($_GET['id'], GRAPH_YEAR, $cond);
		break;
	case "cbox":
		$graph['plot'] = graph_multibox($_GET['id'], GRAPH_CLASS, $cond);
		break;
	case "sbox":
		$graph['plot'] = graph_multibox($_GET['id'], GRAPH_GENDER, $cond);
		break;
}

echo json_encode(array_merge($upvals, $rec, $graph));
?>
