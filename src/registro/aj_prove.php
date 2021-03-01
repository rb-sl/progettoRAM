<?php
// Script to validate values inserted in show_classe.php
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
include $_SERVER['DOCUMENT_ROOT']."/libraries/lib_reg.php";
chk_access(2);
connect();

// Acceptable is set to 0 if some value is found non-valid
$acceptable = 1;

// New test check
if(isset($_POST['ntest']))
	foreach($_POST['ntest'] as $fk_stud => $val)
    	if($val and !is_accettable($_POST['test'], $val))
        {
      		$acceptable = 0;

        	// Adds the current element to the array of non-valid elements
        	$err['ntest'][]=$fk_stud;
        }

// Update check
if(isset($_POST['pr']))
	foreach($_POST['pr'] as $idtest => $s)
		foreach($s as $idist => $val)
			if($val and !is_accettable($idtest, $val)) 
			{
				$acceptable = 0;

				$err['pr'][$idist] = $idtest;
			}

// No data is sent if correct
if($acceptable)
	echo json_encode(0);
else
{
	$json = json_encode($err);
	writelog("Errore di inserimento:\n>>$json");
	echo $json;
}
?>