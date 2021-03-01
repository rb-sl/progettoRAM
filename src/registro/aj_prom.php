<?php
include $_SERVER['DOCUMENT_ROOT']."/librerie/general.php";
include $_SERVER['DOCUMENT_ROOT']."/librerie/lib_reg.php";
chk_access(2);
connect();

$ret=query("SELECT classe,sez,anno FROM CLASSI WHERE id_cl=".$_GET['toprom']);
$test=$ret->fetch_assoc();
$data['cl']=$test['classe']+1;
$data['sez']=$test['sez'];
$data['anno']=$test['anno']+1;
$data['list']=build_chk_table($_GET['toprom'],true);

echo json_encode($data);
?>