<?php
// Main guide page, includes elements depending on the user's state
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
chk_access(5);
connect();
show_premain("Manuale");
?>

<div class="textwall">
	<h2>Manuale utente</h2>
	<p>
		Questo manuale è finalizzato a facilitare l'utilizzo dell'applicazione per gli utenti; per 
		ulteriori informazioni sul progetto si rimanda alla pagina <a href="/project.php">Il progetto</a>,
		che ne descrive metodi e finalità.
	</p>
	<p>
		Ogni pagina presenta un menu principale (visibile cliccando sul pulsante in alto a destra 
		nella versione mobile) che permette di raggiungere le sezioni principali dell'applicazione, 
		ovvero Registro, Test e valutazioni e Statistica; sono poi disponibili funzioni legate 
		al proprio profilo e all'amministrazione dell'applicazione, raggiungibili da ogni pagina 
		cliccando sul proprio nome utente.
	</p>
	<p>
		Il simbolo &#128279; indica un collegamento a un sito esterno.
	</p>

	<h3>Indice</h3>
	<ul class="nobul bordermenu section">
<?php
// Professor-related functions
if($_SESSION['priv'] <= 2)
	echo "<li><a href='#reg'>Registro</a></li>
		<ul class='nobul'>
			<li><a href='#addcl'>Aggiungere una classe</a></li>
			<li><a href='#vcl'>Visualizzare e modificare le prove di una classe</a></li>
			<li><a href='#stcl'>Elaborare i dati della classe</a></li>
			<li><a href='#modcl'>Modificare le informazioni di una classe</a></li>
			<li><a href='#visst'>Visualizzare le prove di uno studente</a></li>
			<li><a href='#modst'>Modificare le informazioni di uno studente</a></li>
		</ul>";

// A statistical access can visualize types of tests and the statistical section
if($_SESSION['priv'] <= 3)
{
	echo "<li><a href='#test'>Test e valutazioni</a></li>
		<ul class='nobul'>
			<li><a href='#vtest'>Visualizzare le informazioni dei test</a></li>";
	
	// Test modifications is reserved to admins or professors with grants
	if($_SESSION['priv'] <= 1)
    	echo "<li><a href='#modtst'>Aggiungere e modificare test</a></li>";
	// Only professors can change evaluation parameters and their favourites list
	if($_SESSION['priv'] <= 2)
		echo "<li><a href='#grades'>Modificare i parametri di valutazione</a></li>
			<li><a href='#fav'>Cambiare la lista di test preferiti</a></li>";
	
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
		<li><a href="#profile">Profilo</a></li>
		<li><a href="#info">Ulteriori informazioni e contatti</a></li>
	</ul>

<?php
if($_SESSION['priv'] <= 2)
	include "professor_guide.php";

if($_SESSION['priv'] <= 3)
{
	include "test_guide.php";
	include "stat_guide.php";
}

if($_SESSION['priv'] == 0)
	include "admin_guide.php";
?>
  	
	<h3 id="profile">Profilo</h3>
	<p>
		Una volta effettuato l'accesso è possibile cliccare sul proprio nome utente (in alto a sinistra), quindi 
		<span class="warningcolor">Profilo</span>. Da questa pagina è possibile modificare:
		<ul>
			<li>Nome utente</li>
			<li>Nome e cognome</li>
			<li>E-mail</li>
			<li>Scuola</li>
			<li>Password</li>
		</ul>
	</p>
	
	<h3 id="info">Ulteriori informazioni e contatti</h3>
	<p>
		Il Progetto RAM (Ricerca Attivit&agrave; Motorie) è un'applicazione sviluppata nell'A.S. 
		2016/2017 all'ITIS G. Fauser di Novara come progetto di maturità. È stata poi successivamente per migliorarne
		l'usabilità e permettere calcoli statistici più efficaci ed efficienti.
	</p>
	<p>
		Il codice sorgente dell'applicazione è <a href="https://github.com/rb-sl/progettoRAM">disponibile su Github</a>
		insieme alla documentazione del progetto.
	</p>

	<h4>Contatti</h4>
</div>
<?php show_postmain(); ?>
