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
		$graph = graph_vals($_GET['id'], $cond);
		break;
	case "prc":
		$graph = graph_prc($_GET['id'], $cond);
		break;
	case "hbox":
		$graph = graph_multibox($_GET['id'], MULTIBOX_YEAR, $cond);
		break;
	case "cbox":
		$graph = graph_multibox($_GET['id'], MULTIBOX_CLASS, $cond);
		break;
	case "sbox":
		$graph = graph_multibox($_GET['id'], MULTIBOX_GENDER, $cond);
		break;
}

echo json_encode(array($upvals, $rec, $graph));
?>
