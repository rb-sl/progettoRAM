<?php
include $_SERVER['DOCUMENT_ROOT']."/librerie/general.php";
include $_SERVER['DOCUMENT_ROOT']."/librerie/lib_reg.php";
include $_SERVER['DOCUMENT_ROOT']."/librerie/lib_stat.php";
chk_access(2);
connect();

$cond=cond_builder();
$test=get_test();

switch($_GET['vis'])
{
	case "prc":
		$color=get_color_prc();
		$rstud=get_prc($color,$cond);
		$am=get_am_prc($test['id'],$rstud['val'],$color);
		break;
	case "std":
		$color=get_color_std();
		$rstud=get_std($test,$cond);	
		$am=get_am_std($test['id'],$rstud['val'],$color);
		break;
	case "vt":
		$color=get_color_vt();
		$rstud=get_vt($color,$cond);
		$am=get_am_vt($test['id'],$rstud,$color);
		break;
}

echo json_encode(array($rstud,$am));
?>