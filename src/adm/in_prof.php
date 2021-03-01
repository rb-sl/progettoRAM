<?php
include $_SERVER['DOCUMENT_ROOT']."/librerie/general.php";
chk_access(0);
connect();

$query="INSERT INTO PROFESSORI(user,psw,nomp,cogp,email,fk_scuola) VALUES('$_POST[usr]',MD5('".$_POST['psw']."'),'$_POST[nom]','$_POST[cog]','$_POST[mail]',$_POST[sc])";
$ret=query($query);
$idprof=$ret->insert_id;
query("INSERT INTO VOTI(voto,perc,fk_prof) VALUES(4,5,$idprof)");
query("INSERT INTO VOTI(voto,perc,fk_prof) VALUES(4.5,10,$idprof)");
query("INSERT INTO VOTI(voto,perc,fk_prof) VALUES(5,15,$idprof)");
query("INSERT INTO VOTI(voto,perc,fk_prof) VALUES(5.5,25,$idprof)");
query("INSERT INTO VOTI(voto,perc,fk_prof) VALUES(6,35,$idprof)");
query("INSERT INTO VOTI(voto,perc,fk_prof) VALUES(6.5,45,$idprof)");
query("INSERT INTO VOTI(voto,perc,fk_prof) VALUES(7,55,$idprof)");
query("INSERT INTO VOTI(voto,perc,fk_prof) VALUES(7.5,65,$idprof)");
query("INSERT INTO VOTI(voto,perc,fk_prof) VALUES(8,75,$idprof)");
query("INSERT INTO VOTI(voto,perc,fk_prof) VALUES(8.5,85,$idprof)");
query("INSERT INTO VOTI(voto,perc,fk_prof) VALUES(9,90,$idprof)");
query("INSERT INTO VOTI(voto,perc,fk_prof) VALUES(9.5,95,$idprof)");
query("INSERT INTO VOTI(voto,perc,fk_prof) VALUES(100,100,$idprof)");

echo "Inserito!";
show_postmain(); 
?>