<?php 
include $_SERVER['DOCUMENT_ROOT']."/librerie/general.php";
include $_SERVER['DOCUMENT_ROOT']."/librerie/lib_reg.php";
chk_access(2);
connect();

foreach($_POST['ntest'] as $idist => $val)
if($val)
{
    query("INSERT INTO PROVE(fk_test,fk_ist,valore,data) VALUES(".$_POST['test'].",$idist,$val,CURDATE()) ON DUPLICATE KEY UPDATE valore=$val,data=CURDATE()");
    $i++;
}

foreach($_POST['pr'] as $idtest => $s)
    foreach($s as $idist => $val)
        if(is_numeric($val))
        {
            query("INSERT INTO PROVE(fk_test,fk_ist,valore,data) VALUES($idtest,$idist,$val,CURDATE()) ON DUPLICATE KEY UPDATE valore=$val,data=CURDATE()");
            $m++;
        }
        else
        {
            query("DELETE FROM PROVE WHERE fk_test=$idtest AND fk_ist=$idist");
            $d++;
        }

writelog("[prove] [classe: ".$_GET['cl']."] add: $i; up: $m; del: $d");

header("Location: ".$_SERVER['HTTP_REFERER']);
?>