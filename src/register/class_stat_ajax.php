<?php
// Script to send statistical data about a class on an ajax request
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
include $_SERVER['DOCUMENT_ROOT']."/libraries/lib_reg.php";
include $_SERVER['DOCUMENT_ROOT']."/libraries/lib_stat.php";
chk_access(2);
connect();

$cond = cond_builder(); 

switch($_GET['vis'])
{
	case "prc":
		if($rstud = get_perc($_GET['id'], $cond))
			$am = get_avgmed($_GET['id'], $rstud['val'], true);
		else
			$am = null;
		break;
	case "std":
		if($rstud = get_std($_GET['id'], $cond))	
			$am = get_avgmed($_GET['id'], $rstud['val'], false);
		else
			$am = null;
		break;
	case "vt":
		$color=get_color_vt();
		$rstud=get_vt($color,$cond);
		$am=get_am_vt($test['id'],$rstud,$color);
		break;
}

echo json_encode(array($rstud, $am));
?>