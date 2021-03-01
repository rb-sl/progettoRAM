<?php
include $_SERVER['DOCUMENT_ROOT']."/librerie/general.php";
chk_access(2);
connect();

$ret=query("SELECT id_test,nometest FROM TEST WHERE id_test NOT IN (
  SELECT fk_test FROM PROVE,ISTANZE WHERE fk_ist=id_ist AND fk_cl=".$_GET['id']." 
  ) ORDER BY nometest");
while($row=$ret->fetch_assoc())
  $data.="<option value='".$row['id_test']."'>".$row['nometest']."</option>";
          
echo json_encode($data);
?>