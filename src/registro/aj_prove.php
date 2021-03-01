<?php 
// Questa pagina serve a validare e caricare le prove inserite da show_classe.php
include $_SERVER['DOCUMENT_ROOT']."/librerie/general.php";
include $_SERVER['DOCUMENT_ROOT']."/librerie/lib_reg.php";
chk_access(2);
connect();

// La variabile ok viene messa a 0 se qualche valore non è accettabile
$ok=1;
// Controllo sull'inserimento di un nuovo test
if($_POST['ntest'])
	foreach($_POST['ntest'] as $fk_stud => $val)
    	if($val and !is_accettable($_POST['test'],$val))
        {
      		$ok=0;
        	// Costruzione di un array per ritornare tutte le prove non conformi
        	$err['ntest'][]=$fk_stud;
        }

// Controllo modifica altre prove
foreach($_POST['pr'] as $idtest => $s)
	foreach($s as $idist => $val)
		if($val and !is_accettable($idtest,$val)) 
		{
        	$ok=0;
			$err['pr'][$idist]=$idtest;
        }

if($ok)
	echo json_encode(0);
else
{
	$json=json_encode($err);
	writelog("err:\n>>$json");
	echo $json;
}
?>