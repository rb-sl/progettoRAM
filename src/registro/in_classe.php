<?php 
include $_SERVER['DOCUMENT_ROOT']."/librerie/general.php";
include $_SERVER['DOCUMENT_ROOT']."/librerie/lib_reg.php";
include $_SERVER['DOCUMENT_ROOT']."/librerie/lib_stat.php";
chk_access(2);
connect();

$ret=query("INSERT INTO CLASSI(classe,sez,anno,fk_prof,fk_scuola) VALUES(".$_POST['cl'].",'".strtoupper($_POST['sez'])."',".$_POST['anno'].",".$_SESSION['id'].",".$_SESSION['scuola'].")");
$idcl=$_SESSION['sql']->insert_id;

writelog("[+cl] $idcl");

foreach($_POST['pr'] as $ids)
	insert_stud_ex($idcl,$ids,$_POST['nold'][$ids]);

foreach($_POST['cst'] as $i => $cg)
  	insert_stud_new($idcl,$cg,$_POST['nst'][$i],$_POST['sesso'][$i]);

foreach($_POST['ext'] as $dat => $ids)
{
	$info=explode($dat,"_"); 
	if($ids=="new")
    	insert_stud_new($idcl,$info[0],$info[1],$info[2]);
	else
    	insert_stud_ex($idcl,$ids,$info[1]);
}

header("Location: /registro/show_classe.php?id=$idcl");
?>