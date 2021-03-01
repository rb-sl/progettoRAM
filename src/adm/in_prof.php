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

/*
$headers .= "Reply-To: Roberto Basla <roberto.basla164@gmail.com>\r\n"; 
  $headers .= "Return-Path: Roberto Basla <roberto.basla164@gmail.com>\r\n";
  $headers .= "From: Roberto Basla <roberto.basla164@gmail.com>\r\n"; 
  $headers .= "MIME-Version: 1.0\r\n";
  $headers .= "Content-type: text/plain; charset=iso-8859-1\r\n";
  $headers .= "X-Priority: 3\r\n";
  $headers .= "X-Mailer: PHP". phpversion() ."\r\n";

mail($_POST[mail],"Credenziali RAM","Buongiorno, le ho assegnato le seguenti credenziali:
Nome utente: $_POST[usr]
Password: $_POST[pwd]
Se desidera cambiare queste informazioni deve cliccare su login -> profilo.
Ho anche impostato i voti in base al percentile (scheda Gestione test) di default.

Prima di iniziare ad usare l'applicazione le consiglierei di leggere le schede Il progetto e Manuale.
L'unico accorgimento che le chiederei sarebbe di prestare attenzione alle linee guida dei test in Gestione test.
Resto comunque a disposizione per chiarimenti.

Roberto Basla",$headers);
*/
echo "Inserito!";
show_postmain(); 
?>