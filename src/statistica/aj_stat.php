<?php
include $_SERVER['DOCUMENT_ROOT']."/librerie/general.php";
include $_SERVER['DOCUMENT_ROOT']."/librerie/lib_stat.php";
chk_access(3);
connect();

$cond=cond_builder();

$upvals=get_stats($_GET['id'],$cond);

$rec=get_records($cond);

switch($_GET['graph'])
{
	case "val":
	case "box":
		$graph=graph_vals($cond);
		break;
	case "prc":
		$graph=graph_prc($cond);
		break;
	case "hbox":
		$graph=graph_multibox("anno",$cond);
		break;
	case "cbox":
		$graph=graph_multibox("classe",$cond);
		break;
	case "sbox":
		$graph=graph_multibox("sesso",$cond);
		break;
}
echo json_encode(array($upvals,$rec,$graph));
?>