<?php
// Questa pagina serve a estrarre dal database i voti dell'utente da get
// tramite richiesta ajax di test.php
include $_SERVER['DOCUMENT_ROOT']."/librerie/general.php";
chk_access(0);
connect();
        
$ret=query("SELECT * FROM VALUTAZIONI,VOTI WHERE fk_voto=id_voto AND fk_prof=".$_GET['idprof']);
while($row=$ret->fetch_assoc())
	$data[$row['voto']*10]=$row['perc'];

echo json_encode($data);
?>