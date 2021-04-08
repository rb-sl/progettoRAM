<?php
// Home page of the application
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
connect();
show_premain();

$info_st = prepare_stmt("SELECT * FROM ADMINDATA 
	JOIN PROFESSORI ON main_admin=id_prof");
$ret = execute_stmt($info_st);
$info_st->close();
$info = $ret->fetch_assoc();
?>

<h2>Progetto RAM</h2>

<?php
if(isset($info['index_compiled']))
	echo $info['index_compiled'];
?>

<p>
	Il Progetto RAM (Ricerca Attività Motorie) è un'applicazione che consiste in un registro elettronico per i 
  	professori di Educazione Fisica degli Istituti Superiori; consente di registrare diversi tipi di test e le prove 
	svolte dagli studenti, per poi effettuare statistiche sulla popolazione scolastica.
</p>
<p>
	Per utilizzare l'applicazione è necessario effettuare il login con le credenziali fornite. Per ottenere o 
	ripristinare le credenziali <a href="mailto:<?=$info['email']?>">inviare un messaggio</a> all'amministratore.<br>
  	Per ulteriori informazioni si rimanda alla <a href="/project.php">descrizione del progetto</a> e al 
	<a href="/guide/guide.php">manuale</a>.
</p>

<?php show_postmain(); ?>
