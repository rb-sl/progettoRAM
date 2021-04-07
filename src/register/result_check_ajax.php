<?php
// Script to validate values inserted in show_classe.php
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
include $_SERVER['DOCUMENT_ROOT']."/libraries/lib_reg.php";
if(!chk_access(PROFESSOR, false))
{
    echo "null";
    exit;
}
connect();

// Acceptable is set to 0 if some value is found non-valid
$acceptable = true;

// New test check
if(isset($_POST['ntest']))
	foreach($_POST['ntest'] as $fk_stud => $val)
    	if($val and !is_accettable($_POST['test'], $val))
        {
      		$acceptable = false;

        	// Adds the current element to the array of non-valid elements
        	$err['ntest'][] = $fk_stud;
        }

// Update check
if(isset($_POST['pr']))
	foreach($_POST['pr'] as $idtest => $s)
		foreach($s as $idist => $val)
			if($val and !is_accettable($idtest, $val)) 
			{
				$acceptable = false;

				$err['pr'][$idist] = $idtest;
			}

// No data is sent if correct
if($acceptable)
	echo json_encode(true);
else
{
	$json = json_encode($err);
	writelog("Errore di inserimento:\n>>$json");
	echo $json;
}
?>
