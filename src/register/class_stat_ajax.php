<?php
// Script to send statistical data about a class on an ajax request
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
include $_SERVER['DOCUMENT_ROOT']."/libraries/lib_reg.php";
include $_SERVER['DOCUMENT_ROOT']."/libraries/lib_stat.php";
chk_access(2);
connect();

$cond = cond_builder(); 

if(isset($_GET['forstud']) && $_GET['forstud'] == "true")
	$forstud = $_GET['forstud'];
else
	$forstud = false;

// Builds the response based on the type of data requested
switch($_GET['vis'])
{
	case "prc":
		if($rstud = get_perc($_GET['id'], $cond, $forstud))
			$am = get_avgmed($_GET['id'], $rstud['val'], true, $forstud);
		break;
	case "std":
		if($rstud = get_std($_GET['id'], $cond, $forstud))	
			$am = get_avgmed($_GET['id'], $rstud['val'], false, $forstud);
		break;
	case "gr":
		if($rstud = get_grades($_GET['id'], $cond, $forstud))
			$am = get_avgmed_grades($_GET['id'], $rstud, $forstud);
		break;
}

if($rstud !== null)
	echo json_encode(array_merge($rstud, $am));
else
	echo "null";
?>
