<?php
include $_SERVER['DOCUMENT_ROOT']."/librerie/general.php";
chk_access(0);
connect();
show_premain("Sanificazione studenti uniti");

$ra=query("SELECT MIN(anno) AS min,MAX(anno) AS max FROM CLASSI");
$lim=$ra->fetch_assoc();
echo "<div>";
for($c=$lim['min'];$c<=$lim['max'];$c++)
{
	$ret=query("SELECT fk_stud FROM ISTANZE,CLASSI WHERE fk_cl=id_cl AND anno=$c GROUP BY fk_stud HAVING COUNT(*)>1");
	echo "<b>$c</b><br>";
	while($rext=$ret->fetch_assoc())
    {
    	
    	$r1=query("SELECT * FROM ISTANZE,CLASSI WHERE fk_cl=id_cl AND fk_stud=".$rext['fk_stud']." and anno=$c ORDER BY anno");
    	while($row=$r1->fetch_assoc())
    		echo $row['fk_stud'].": ".$row['anno']." ".$row['classe']." ".$row['sez']."<br>"; 
    	echo "<br>";
    }
}
?>
</div>
