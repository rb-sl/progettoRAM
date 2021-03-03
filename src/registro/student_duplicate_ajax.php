<?php
include $_SERVER['DOCUMENT_ROOT']."/librerie/general.php";
chk_access(2);
connect();

$st=json_decode($_GET['st']);
$cl=json_decode($_GET['cl']);

$anno=$_SESSION['sql']->real_escape_string($cl->anno)-1;
$classe=$_SESSION['sql']->real_escape_string($cl->classe);

foreach($st as $k => $stud)
{
	// Controllo duplicazione dello studente
	$ret=query("SELECT id_stud,cogs,noms,id_ist,classe,sez,anno FROM STUDENTI,ISTANZE,CLASSI,SCUOLE 
		WHERE fk_stud=id_stud AND fk_cl=id_cl AND fk_scuola=id_scuola 
		AND cogs='".$_SESSION['sql']->real_escape_string($stud->cogs)."' 
		AND (noms='".$_SESSION['sql']->real_escape_string($stud->noms)."' OR noms IS NULL) 
		AND sesso='".$_SESSION['sql']->real_escape_string($stud->sesso)."'
		AND anno=$anno AND classe<=$classe AND fk_scuola=".$_SESSION['scuola']." 
		GROUP BY id_stud HAVING(anno=MAX(anno))");

	if($ret->num_rows!=0)
    {
    	$data[$k]['idel']=$k;
    	$data[$k]['cogs']=$stud->cogs;
        $data[$k]['noms']=$stud->noms;
        $data[$k]['sesso']=$stud->sesso;
    	while($row=$ret->fetch_assoc())
        	$data[$k]['list'][]="<input type='radio' name=\"ext[".$stud->cogs."_".$stud->noms."_".$stud->sesso."]\" value='".$row['id_stud']."'> ".$row['classe'].$row['sez']." ".$row['anno']."/".($row['anno']+1);
	}
}

echo json_encode($data);