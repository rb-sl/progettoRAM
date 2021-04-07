<?php
// Backend page used to answer ajax queries from statistics.php
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
include $_SERVER['DOCUMENT_ROOT']."/libraries/lib_stat.php";
if(!chk_access(RESEARCH, false))
{
    echo "null";
    exit;
}
connect();

$cond = cond_builder();

$stats = get_general_stats($cond);
$graph = misc_graph($cond);

echo json_encode(array_merge($stats, $graph));
?>
