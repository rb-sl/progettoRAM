<?php 
include $_SERVER['DOCUMENT_ROOT']."/librerie/general.php";
include $_SERVER['DOCUMENT_ROOT']."/librerie/lib_reg.php";
chk_access(2);
connect();

$ret=query("UPDATE CLASSI SET classe=".$_POST['cl'].",sez='".strtoupper($_POST['sez'])."',anno=".$_POST['anno']." WHERE id_cl=".$_GET['id']);
writelog("[modcl] ".$_GET['id']);

$idlist="-1";
foreach($_POST['pr'] as $ids)
	$idlist.=",".$ids;

$rs=query("SELECT id_ist FROM ISTANZE WHERE fk_cl=".$_GET['id']." AND fk_stud NOT IN ($idlist)");
while($row=$rs->fetch_assoc())
	delete_inst($row['id_ist']);

foreach($_POST['cst'] as $i => $cg)
  	insert_stud_new($_GET['id'],$cg,$_POST['nst'][$i],$_POST['sesso'][$i]);

foreach($_POST['ext'] as $dat => $ids)
{
	$info=explode("_",$dat);
	if($ids=="new")
		insert_stud_new($_GET['id'],$info[0],$info[1],$info[2]);
	else
    	insert_stud_ex($_GET['id'],$ids,$info[1]);
}

$_SESSION['alert']="Aggiornamento effettuato con successo";

header("Location: /registro/mod_classe.php?id=".$_GET['id']);
?>