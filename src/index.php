<?php
// Home page of the application
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
show_premain();
?>
<h2>Progetto RAM</h2>
<!-- Uncomment for announcements -- >
<h2 class="dangercolor">
  >> Attenzione << <br>
  Il login è temporaneamente disabilitato per permettere un upgrade del sistema
</h2>
< !-- -->
<p>
	Il Progetto RAM (Ricerca Attivit&agrave; Motorie) è un'applicazione che consiste in un registro elettronico per i 
  	professori di Educazione Fisica degli Istituti Superiori; consente di registrare diversi tipi di test e le prove 
	svolte dagli studenti, per poi effettuare statistiche sulla popolazione scolastica.
</p>
<p>
	Per utilizzare l'applicazione è necessario effettuare il login con le credenziali fornite. Per ottenere o 
	ripristinare le credenziali <a href="mailto:$ADDRESS">inviare un messaggio</a> all'amministratore.<br>
  	Per ulteriori informazioni si rimanda alla <a href="/progetto.php">descrizione del progetto</a> e al <a href="/manuale.php">manuale</a>.
</p>

<?php show_postmain(); ?>