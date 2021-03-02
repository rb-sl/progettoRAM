<?php
// Main guide page, includes elements depending on the user's state
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
chk_access(5);
connect();
show_premain("Manuale");


?>

<div class="textwall">
	<h2>Manuale utente</h2>
	Questo manuale è finalizzato a facilitare l'utilizzo dell'applicazione per gli utenti; per ulteriori informazioni sul progetto si 
	rimanda alla pagina <a href="/progetto.php">Il progetto</a>.<br>
	Ogni pagina presenta un menu principale (visibile cliccando sul pulsante in alto a destra nella versione mobile) che permette 
	di raggiungere le sezioni principali dell'applicazione, ovvero Registro, Test e valutazioni e Statistica.<br>
	Sono poi disponibili funzioni legate al proprio profilo e all'amministrazione del database 
	(disponibili solo se l'utente ha i permessi necessari) raggiungibili da ogni pagina cliccando sul proprio nome utente.<br>
	Il simbolo &#128279; indica un collegamento a un sito esterno.

	<h3>Indice</h3>
	<ul class="nobul border">
<?php
// Professor-related functions
if($_SESSION['priv'] <= 2)
	echo "<li><a href='#reg'>Registro</a></li>
		<ul class='nobul'>
			<li><a href='#addcl'>Aggiungere una classe</a></li>
			<li><a href='#vcl'>Visualizzare e modificare le prove di una classe</a></li>
			<li><a href='#stcl'>Elaborare i dati della classe</a></li>
			<li><a href='#modcl'>Modificare le informazioni di una classe</a></li>
			<li><a href='#vst'>Visualizzare e modificare le informazioni di uno studente</a></li>
			<li><a href='#modst'>Visualizzare e modificare uno studente</a></li>
		</ul>";

// A statistical access can visualize types of tests and the statistical section
if($_SESSION['priv'] <= 3)
{
	echo "<li><a href='#test'>Test e valutazioni</a></li>
		<ul class='nobul'>
			<li><a href='#vtest'>Visualizzare le impostazioni dei test</a></li>";
	
	// Only professors can change evaluation parameters
	if($_SESSION['priv'] <= 2)
		echo "<li><a href='#voti'>Modificare i parametri di valutazione</a></li>";
	
	echo "</ul>
    	<li><a href='#stat'>Statistica</a></li>
		<ul class='nobul'>
			<li><a href='#statt'>Statistiche per test</a></li>
			<li><a href='#stata'>Statistiche avanzate</a></li>
		</ul>
		<li><a href='#menustat'>Sottomenu statistico</a></li>
		<li><a href='#graph'>Grafici</a></li>";
}
?>
		<li><a href="#profilo">Profilo</a></li>
		<li><a href="#info">Informazioni e contatti</a></li>
	</ul>

<?php
if($_SESSION['priv'] <= 2)
	include "professor_guide.php";

if($_SESSION['priv'] <= 3)
	include "test_stat_guide.php";

if($_SESSION['priv'] == 0)
	include "admin_guide.php";
?>
  	
	<h3 id="profilo">Profilo</h3>
  	Una volta effettuato l'accesso è possibile cliccare sul proprio nome utente (in alto a sinistra), quindi <span class="warningcolor">Profilo</span>.
 	Da questa pagina è possibile aggiornare informazioni personali quali utente o password.
	
	<h3 id="info">Informazioni e contatti</h3>
	Il Progetto RAM (Ricerca Attivit&agrave; Motorie) è un'applicazione sviluppata nell'A.S. 2016/2017 all'ITIS G. Fauser di Novara come progetto di maturit&agrave;. &Egrave; stata poi integrata successivamente per migliorarne
	l'usabilit&agrave; e permettere calcoli statistici pi&ugrave; potenti ed efficienti.<br>
	<h4>Contatti</h4>
</div>
<?php show_postmain(); ?>