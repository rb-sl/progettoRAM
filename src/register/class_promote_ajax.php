<?php
// Script to handle ajax calls about students to be promoted
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
include $_SERVER['DOCUMENT_ROOT']."/libraries/lib_reg.php";
if(!chk_access(PROFESSOR, false))
{
    echo "null";
    exit;
}
connect();

$prom_st = prepare_stmt("SELECT classe, sez, anno FROM CLASSI WHERE id_cl=?");
$prom_st->bind_param("i", $_GET['toprom']);
$ret = execute_stmt($prom_st);
$prom_st->close();

$newclass = $ret->fetch_assoc();
$data['cl'] = $newclass['classe'] + 1;
$data['sez'] = $newclass['sez'];
$data['anno'] = $newclass['anno'] + 1;
$data['list'] = build_chk_table($_GET['toprom'], true);

echo json_encode($data);
?>
