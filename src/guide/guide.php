<?php
// Main guide page, includes elements depending on the user's state
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
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
		<li><a href="#access">Registrazione e accesso</a></li>
<?php
// Professor-related functions
if(chk_auth(PROFESSOR))
{
?>
		<li><a href="#reg">Registro</a></li>
		<ul class="nobul">
			<li><a href="#addcl">Aggiungere una classe</a></li>
			<li><a href="#vcl">Visualizzare e modificare le prove di una classe</a></li>
			<li><a href="#stcl">Elaborare i dati della classe</a></li>
			<li><a href="#modcl">Modificare le informazioni di una classe</a></li>
			<li><a href="#visst">Visualizzare le prove di uno studente</a></li>
			<li><a href="#modst">Modificare le informazioni di uno studente</a></li>
		</ul>
<?php	
}
// A statistical access can visualize types of tests and the statistical section
if(chk_auth(RESEARCH))
{
?>
		<li><a href="#test">Test e valutazioni</a></li>
		<ul class="nobul">
			<li><a href="#vtest">Visualizzare le informazioni dei test</a></li>
<?php
	// Test modifications is reserved to admins or professors with grants
	if(chk_auth(PROFESSOR_GRANTS))
	{
?>
    		<li><a href="#modtst">Aggiungere e modificare test</a></li>
<?php
	}
	// Only professors can change evaluation parameters and their favourites list
	if(chk_auth(PROFESSOR))
	{
?>
			<li><a href="#grades">Modificare i parametri di valutazione</a></li>
			<li><a href="#fav">Modificare la lista di test preferiti</a></li>
<?php
	}
?>
		</ul>
    	<li><a href="#stat">Statistica</a></li>
		<ul class="nobul">
			<li><a href="#genstat">Visualizzare statistiche generali</a></li>
			<li><a href="#statt">Visualizzare le statistiche dei test</a></li>
			<li><a href="#correlation">Studiare la correlazione dei test</a></li>
		</ul>
		<li><a href="#menustat">Sottomenu statistico</a></li>
		<li><a href="#graph">Grafici</a></li>
<?php
}
if(chk_auth(NONE))
{
?>
		<li><a href="#profile">Profilo</a></li>
<?php
}
?>		
		<li><a href="#info">Ulteriori informazioni e contatti</a></li>
	</ul>

	<h3 id="access">Registrazione e accesso</h3>
	<p>
		La creazione di un profilo deve essere richiesto a un <a href="#contacts">amministratore</a>
		per email o, se possibile, di persona. Saranno fornite delle credenziali temporanee e al primo
		accesso verrà richiesta la modifica della password.
	</p>
	<p>
		Per accedere al sistema premere la voce Login in alto a destra (o, se da mobile, premere sul 
		bottone del menu e poi sulla voce Login) e inserire le proprie credenziali, quindi premere
		<span class="warningcolor">Accedi</span>.
	</p>
<?php
if(chk_auth(PROFESSOR))
	include "professor_guide.php";

if(chk_auth(RESEARCH))
{
	include "test_guide.php";
	include "stat_guide.php";
}

if(chk_auth(ADMINISTRATOR))
	include "admin_guide.php";

if(chk_auth(NONE))
{
?>
  	<div class="section">
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
	</div>
<?php
}
?>
	<div class="section">
		<h3 id="info">Ulteriori informazioni e contatti</h3>
		<p>
			Il Progetto RAM (Ricerca Attivit&agrave; Motorie) è un'applicazione sviluppata nell'A.S. 
			2016/2017 all'ITIS G. Fauser di Novara come progetto di maturità. È stata poi successivamente integrata
			per migliorarne l'usabilità e permettere calcoli statistici più efficaci ed efficienti.
		</p>
		<p>
			Il codice sorgente dell'applicazione è <a href="https://github.com/rb-sl/progettoRAM">disponibile su Github&#128279;</a>
			insieme alla documentazione del progetto.
		</p>

		<h4 id="contacts">Contatti</h4>
	</div>
</div>

<?php show_postmain(); ?>
