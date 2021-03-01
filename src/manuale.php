<?php
include $_SERVER[DOCUMENT_ROOT]."/librerie/general.php";
chk_access(5);
connect();
show_premain("Manuale");

$ttst=query("SELECT nomet FROM TIPOTEST ORDER BY nomet");
$ctst=query("SELECT nomec FROM CLTEST ORDER BY nomec");
$unita=query("SELECT udm FROM UNITA ORDER BY udm");
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
		<li><a href="#reg">Registro</a></li>
		<ul class="nobul">
			<li><a href="#addcl">Aggiungere una classe</a></li>
			<li><a href="#vcl">Visualizzare e modificare le prove di una classe</a></li>
			<li><a href="#stcl">Elaborare i dati della classe</a></li>
			<li><a href="#modcl">Modificare le informazioni di una classe</a></li>
			<li><a href="#vst">Visualizzare e modificare le informazioni di uno studente</a></li>
			<li><a href="#modst">Modificare uno studente</a></li>
		</ul>
		<li><a href="#test">Test e valutazioni</a></li>
		<ul class="nobul">
			<li><a href="#vtest">Visualizzare le impostazioni dei test</a></li>
			<li><a href="#voti">Modificare i parametri di valutazione</a></li>
		</ul>
    	<li><a href="#stat">Statistica</a></li>
		<ul class="nobul">
			<li><a href="#statt">Statistiche per test</a></li>
			<li><a href="#stata">Statistiche avanzate</a></li>
		</ul>
		<li><a href="#menustat">Sottomenu statistico</a></li>
		<li><a href="graph">Grafici</a></li>
		<li><a href="#profilo">Profilo</a></li>
		<li><a href="#info">Informazioni e contatti</a></li>
	</ul>

	<div id="regdiv">
  		<h3 id="reg">Registro</h3>
 			Questa sezione permette di:
  			<ul>
    			<li><a href="#addcl">Aggiungere una classe</a></li>
    			<li><a href="#vcl">Visualizzare e modificare le prove di una classe</a></li>
            	<li><a href="#stcl">Elaborare i dati della classe</a></li>
            	<li><a href="#modcl">Modificare le informazioni di una classe</a></li>
            	<li><a href="#vst">Visualizzare e modificare le informazioni di uno studente</a></li>
			</ul>
    
  		<h4 id="addcl">Aggiungere una classe</h4>
  		Dalla pagina di registro, premendo il pulsante <span style="color:#337ab7">Aggiungi classe</span>, è possibile registrare 
		nel database una nuova classe. Premendo <span style="color:#337ab7">Promuovi classe precedente</span> e selezionando la 
		classe da promuovere vengono compilati automaticamente i dati della classe e viene importata la lista degli studenti 
    	(<em>Studenti promossi nella nuova classe</em>). Per non importare uno studente deselezionarne la voce.<br>
    	In seguito, si possono aggiungere nuovi studenti, inserendo le informazioni richieste nella tabella Nuovi studenti.
    	Se uno di questi presentasse dati congruenti con quelli già presenti nel sistema viene data la possibilità di 
		selezionarne la provenienza (<em>Possibili studenti già registrati</em>).<br> 
    	Premendo su <span style="color:#eea236">Inserisci classe</span> si raggiungerà la nuova pagina di registro.
  
    	<h4>Visualizzare e modificare le prove di una classe</h4>
  		Cliccando sul pulsante di una classe viene mostrata la sua pagina di registro. La tabella contiene l'elenco degli studenti 
		della classe e le prove relative; passando il puntatore su una prova è possibile visualizzare in sovrimpressione la data 
		in cui è stata effettuata. Premere il pulsante <span style="color:#337ab7">Medie e mediane</span> per visualizzare alcune
		statistiche relative ai test.<br>
		Sono possibili le seguenti operazioni sui dati:
  		<ul>
    		<li>
      			<b>Aggiungere prove</b>: premendo <span style="color:#eea236">Aggiungi prove</span> è possibile 
				aggiungere le prove di un test. Selezionare dal menu a tendina il test desiderato e inserire i valori (Viene caricata
				anche l'unità di misura da utilizzare nell'inserimento). Facendo doppio click su una 
				cella vuota è poi possibile aggiungere una singola prova a test già registrati. 
    		</li>
    		<li>
				<b>Modificare prove</b>: facendo doppio click sul valore di una prova è possibile modificarlo. 
			</li>
			<li>
      			<b>Eliminare prove</b>: fare doppio click sul valore della prova da eliminare e cancellarne il contenuto.
    		</li>
  		</ul>
  		Premere <span style="color:#eea236">Salva</span> per finalizzare l'inserimento o <span style="color:#d9534f">Annulla</span>
		per scartare le modifiche.

		<h4 id="stcl">Elaborare i dati di una classe</h4>
		Dalla pagina di registro di una classe è possibile premere il pulsante <span style="color:#337ab7">Elaborazione dati della classe</span>
		per cambiare modalità di visualizzazione dei dati; in particolare si possono selezionare:
		<ul>
			<li><a href="https://it.wikipedia.org/wiki/Centile" target="_blank">Valori percentili&#128279;</a></li>
			<li><a href="https://it.wikipedia.org/wiki/Standardizzazione_(statistica)" target="_blank">Valori standard&#128279;</a></li>
			<li>Voti</li>
		</ul>
		Cliccando sui pulsanti <span style="color:#337ab7">Medie e mediane</span> o <span style="color:#337ab7">Colori</span> si possono
		visualizzare statistiche sulla classe e codificare per colore i risultati dei test. &Egrave; presente il 
		<a href="#menustat">Sottomenu statistico</a> per effettuare calcoli su popolazioni differenti.
		
		<h4 id="modcl">Modificare le informazioni una classe</h4>
  		Premendo il pulsante <span style="color:#eea236">Modifica</span> è possibile cambiare le informazioni della classe e 
		gestirne gli studenti. Le modalità sono analoghe a quelle dell'<a href="#addcl">inserimento della classe</a>.
		Premere <span style="color:#eea236">Aggiorna classe</span> per salvare le modifiche, <span style="color:#eea236">Indietro</span> 
		per annullarle o <span style="color:#eea236">Elimina classe</span> per rimuoverla dal database.<br>
		Per rimuovere specifici studenti dalla classe deselezionarne la voce. <br>
		<b>NB</b>: eliminando la classe o un suo studente saranno persi tutti i dati delle prove collegate.

		<h4 id="vst">Visualizzare e modificare le informazioni di uno studente</h4>
		Dalla pagina di registro di una classe cliccare su uno studente per visualizzarne anagrafica e classi frequentate. Cliccando su 
		<span style="color:#eea236">Modifica</span> si possono aggiornare cognome, nome e sesso.
	</div>
  	
	<div id="testdiv">
		<h3 id="test">Test e valutazioni</h3>
		Questa sezione permette di:
		<ul>
   		 	<li><a href="#vtest">Visualizzare le impostazioni dei test</a></li>
    		<li><a href="#voti">Modificare i parametri di valutazione</a></li>
	    </ul>
		
		<h4 id="vtest">Visualizzare i test</h4>
		Per visualizzare tutti i dati relativi a un test cliccare sul link corrispondente nella lista a inizio pagina. I parametri visualizzati 
    	sono:<br>
    	<ul>
    	   	<li>
       		 	<b>Classe del test</b>: è la classificazione del test, che ricade in una delle seguenti classificazioni:
           		<ul>
<?php
while($row=$ctst->fetch_assoc())
	echo "<li>".$row['nomec']."</li>\n";
?>
           		</ul>
       		</li>
       		<li>
           		<b>Unità di misura</b>: le unità utilizzabili sono
           		<ul>
<?php
while($row=$unita->fetch_assoc())
	echo "<li>".$row['udm']."</li>\n";
?>              
           		</ul>
       		</li>
       		<li>
           		<b>Valori migliori</b>: determina se i risultati positivi sono quelli maggiori o minori
       		</li>
       		<li>
           		<b>Tipo di valori</b>: serve a determinare alcune statistiche e la scala per la realizzazione dei grafici. I valori possibili sono:
           		<ul>
<?php
while($row=$ttst->fetch_assoc())
	echo "<li>".$row['nomet']."</li>\n";
?>                 	
            	</ul>
        	</li>
    		<li>
        		<b>Sensibilità</b>: indica il passo accettato per le misurazioni
    		</li>
		</ul>

		Sono poi visualizzate alcune informazioni aggiuntive, in particolare
    	<ul>
    		<li><b>Posizione</b>: la posizione che lo studente deve mantenere per iniziare o eseguire il test</li>
        	<li><b>Materiale aggiuntivo</b>: nel caso siano richiesti strumenti particolari</li>
        	<li><b>Esecuzione</b>: istruzioni per gli studenti</li>
        	<li><b>Consigli</b>: istruzioni aggiuntive</li>
        	<li><b>Limite</b>: indica dopo quanto terminare la prova</li>
        	<li><b>Valutazione</b>: indica il valore da inserire nell'applicazione a prova terminata</li>
    	</ul>
    	Tutte queste informazioni sono facoltative tranne la valutazione.<br>
    	Se un utente possiede privilegi amministrativi è abilitato alla modifica dei valori dei test, accessibile tramite il pulsante 
		<span style="color:#eea236">Modifica test</span>. Da questa pagina è possibile modificare i valori e inserirli nel database (premendo il bottone
		<span style="color:#eea236">Aggiorna valori test</span>) o eliminare il test (con il bottone <span style="color:#d9534f">Elimina test</span>).<br>
		NB: modificare i parametri di un test non modifica i valori inseriti nel database.

		<h4 id="voti">Modificare la tabella di valutazione</h4>
	
		La tabella di valutazione in fondo alla pagina permette di creare una corrispondenza tra il percentile raggiunto in una prova e il voto assegnato
		dal sistema. Per alterare la tabella, modificare le percentuali di assegnamento di un voto (assicurandosi che la somma sia 100); l'ultima colonna della tabella mostra
		il range di percentili cui vienne assegnato il voto. Per salvare le nuove impostazioni premere 	<span style="color:#eea236">Aggiorna tabella voti</span>. 
		Se si desidera non assegnare un particolare voto è sufficiente portare a 0 la sua percentuale.<br>
    	Il <a href="graph">grafico</a> sopra la tabella dà una rappresentazione visuale della distribuzione dei voti.
	</div>

	<div id="statdiv">
		<h3 id="stat">Statistica</h3>
		La sezione statistica permette di elaborare i dati presenti nel database. La pagina iniziale della sezione presenta statistiche su studenti e prove. Permette di accedere
		a:
		<ul>
    		<li><a href="#statt">Statistiche per test</a></li>
    		<li><a href="#stata">Statistiche avanzate</a></li>
		</ul>
		<h4 id="statt">Statistiche per test</h4>
		Selezionando il nome di un test dalla lista è possibile visualizzare i parametri ad esso associati. In particolare:
		<ul>
		    <li>Numero totale di prove</li>
   		 	<li><a href="https://it.wikipedia.org/wiki/Media_(statistica)#Media_aritmetica" target="_blank">Media&#128279;</a></li>
    		<li><a href="https://it.wikipedia.org/wiki/Mediana_(statistica)" target="_blank">Mediana&#128279;</a></li>
    		<li><a href="https://it.wikipedia.org/wiki/Scarto_quadratico_medio" target="_blank">Deviazione standard&#128279;</a></li>
    		<li>Record positivi (con classe e anno) e negativi</li>
    	</ul>
		&Egrave; poi possibile visualizzare alcuni <a href="graph">grafici</a> in base ai dati dei test, secondo le opzioni:
		<ul>
    		<li><b>Valori</b>: è un istogramma che permette di visualizzare la distribuzione dei dati suddivisi in opportune classi</li>
    		<li>
    	    	<b>Box plot</b>: detto anche <i>Diagramma a scatola e baffi</i>, permette una visualizzazione immediata di alcuni dati della distribuzione:
    	    	<ul>
    	        	<li><b>Min</b>: il minimo assoluto dei dati</li>
    	        	<li><b>Q1</b>: il valore del primo quartile (o 25° percentile)</li>
    	        	<li><b>Median</b>: la mediana (o 50° percentile)</li>
        	    	<li><b>Mean</b>: la media</li>
            	 	<li><b>Q3</b>: il valore del terzo quartile (o 75° percentile)</li>
	            	<li><b>Max</b>: il massimo assoluto dei dati</li>
				</ul>
    		</li>
    		<li><b>Box plot (Anni)</b>: un box plot con i dati suddivisi per anno</li>
    		<li><b>Box plot (Classi)</b>: un box plot con i dati suddivisi per classe</li>
    		<li><b>Box plot (Sesso)</b>: un box plot con i dati suddivisi per sesso</li>
        	<li><b>Valori percentili</b>: diagramma che mostra l'andamento dei percentili dei dati</li>
		</ul>
		Per visualizzare i dati in base a diversi parametri è possibile utilizzare il <a href="#menustat">Sottomenu statistico</a>.
    
    	<h4 id="stata">Statistiche avanzate</h4>
		Il sistema permette di effettuare alcune statistiche più precise sui dati; a causa della natura di queste operazioni la navigazione
		in queste pagine pu&ograve; risultare più lenta. Gli studi possibili sono:
		<ul>
			<li>
				<a href="https://it.wikipedia.org/wiki/Correlazione_(statistica)" target="_blank">Correlazione campionaria&#128279;</a>:
				La pagina permette di visualizzare la matrice di correlazione dei test; selezionando una casella viene visualizzato un grafico
				che mostra la distribuzione delle prove rispetto ai test scelti. Il coefficiente di correlazione campionaria &rho; d&agrave;
				informazioni rispetto alla positivit&agrave; o meno della correlazione e alla sua intensit&agrave;.<br>
				&Egrave; possibile abilitare la visualizzazione per colore, codificata in modo da evidenziare la correlazione tra valori migliori
				dei test.
			</li> 
			<li>ANOVA: <i>Prossimamente</i></li>
			<li>Test di Tukey: <i>Prossimamente</i></li>
		</ul>
	</div>

	<h3 id="menustat">Sottomenu statistico</h3>
	In alcune pagine è presente un menu statistico per permettere diverse selezioni dei dati. Per modificare l'insieme dei dati, attivare i pulsanti:
	<ul>
    	<li>
        	<b>Anni da / a</b>: permette la selezione dell'intervallo di anni che viene considerato 
    	</li>
    	<li>
        	<b>Classi</b>: sono considerate solo le classi i cui bottoni vengono attivati
        </li>
    	<li>
        	<b>Sesso</b>: seleziona quali sessi considerare
        </li>
    	<li>
        	<b>Solo personali</b>: se attivato permette di scartare i dati registrati da altri docenti
        </li>    
    </ul>
	Perché le modifiche abbiano effetto è necessario premere il pulsante <span style="color:#eea236">Aggiorna</span>, che se arancione indica
	che i dati visualizzati non corrispondono alla selezione corrente.

 	<h3 id="grafici">Grafici</h3>
	I grafici dell'applicazione sono realizzati grazie alla <a href="https://plot.ly/javascript/" target="_blank">Plotly JavaScript open source graphing library&#128279;</a>. 
	Questi grafici permettono di visualizzare informazioni aggiuntive passando il cursore su un insieme di dati e presentano alcune opzioni nella parte destra del grafico:
	<ul>
		<li>
			<svg viewBox="0 0 1000 1000" class="icon" height="1em" width="1em">
				<path d="m500 450c-83 0-150-67-150-150 0-83 67-150 150-150 83 0 150 67 150 150 0 83-67 150-150 150z m400 150h-120c-16 0-34 13-39 29l-31 93c-6 15-23 28-40 28h-340c-16 0-34-13-39-28l-31-94c-6-15-23-28-40-28h-120c-55 0-100-45-100-100v-450c0-55 45-100 100-100h800c55 0 100 45 100 100v450c0 55-45 100-100 100z m-400-550c-138 0-250 112-250 250 0 138 112 250 250 250 138 0 250-112 250-250 0-138-112-250-250-250z m365 380c-19 0-35 16-35 35 0 19 16 35 35 35 19 0 35-16 35-35 0-19-16-35-35-35z" transform="matrix(1 0 0 -1 0 850)"></path>
			</svg> : Premendo l'icona è possibile scaricare un'immagine del grafico sul proprio dispositivo
   		</li>
    	<li>
        	<svg viewBox="0 0 1000 1000" class="icon" height="1em" width="1em">
            	<path d="m1000-25l-250 251c40 63 63 138 63 218 0 224-182 406-407 406-224 0-406-182-406-406s183-406 407-406c80 0 155 22 218 62l250-250 125 125z m-812 250l0 438 437 0 0-438-437 0z m62 375l313 0 0-312-313 0 0 312z" transform="matrix(1 0 0 -1 0 850)"></path>
        	</svg> : Se attivo permette di selezionare una porzione del grafico per vederla in dettaglio
    	</li>
    	<li>
        	<svg viewBox="0 0 1000 1000" class="icon" height="1em" width="1em">
            	<path d="m1000 350l-187 188 0-125-250 0 0 250 125 0-188 187-187-187 125 0 0-250-250 0 0 125-188-188 186-187 0 125 252 0 0-250-125 0 187-188 188 188-125 0 0 250 250 0 0-126 187 188z" transform="matrix(1 0 0 -1 0 850)"></path>
        	</svg> : Selezionando questa opzione è possibile muovere il grafico
        </li>
    	<li>
        	<svg viewBox="0 0 1000 1000" class="icon" height="1em" width="1em">
            	<path d="m0 850l0-143 143 0 0 143-143 0z m286 0l0-143 143 0 0 143-143 0z m285 0l0-143 143 0 0 143-143 0z m286 0l0-143 143 0 0 143-143 0z m-857-286l0-143 143 0 0 143-143 0z m857 0l0-143 143 0 0 143-143 0z m-857-285l0-143 143 0 0 143-143 0z m857 0l0-143 143 0 0 143-143 0z m-857-286l0-143 143 0 0 143-143 0z m286 0l0-143 143 0 0 143-143 0z m285 0l0-143 143 0 0 143-143 0z m286 0l0-143 143 0 0 143-143 0z" transform="matrix(1 0 0 -1 0 850)"></path>
        	</svg> : Permette di evidenziare alcuni dati in un rettangolo
        </li>
    	<li>
        	<svg viewBox="0 0 1031 1000" class="icon" height="1em" width="1em">
            	<path d="m1018 538c-36 207-290 336-568 286-277-48-473-256-436-463 10-57 36-108 76-151-13-66 11-137 68-183 34-28 75-41 114-42l-55-70 0 0c-2-1-3-2-4-3-10-14-8-34 5-45 14-11 34-8 45 4 1 1 2 3 2 5l0 0 113 140c16 11 31 24 45 40 4 3 6 7 8 11 48-3 100 0 151 9 278 48 473 255 436 462z m-624-379c-80 14-149 48-197 96 42 42 109 47 156 9 33-26 47-66 41-105z m-187-74c-19 16-33 37-39 60 50-32 109-55 174-68-42-25-95-24-135 8z m360 75c-34-7-69-9-102-8 8 62-16 128-68 170-73 59-175 54-244-5-9 20-16 40-20 61-28 159 121 317 333 354s407-60 434-217c28-159-121-318-333-355z" transform="matrix(1 0 0 -1 0 850)"></path>
        	</svg> : Permette di evidenziare alcuni dati a mano libera
        </li>
    	<li>
        	<svg viewBox="0 0 875 1000" class="icon" height="1em" width="1em">
            	<path d="m1 787l0-875 875 0 0 875-875 0z m687-500l-187 0 0-187-125 0 0 187-188 0 0 125 188 0 0 187 125 0 0-187 187 0 0-125z" transform="matrix(1 0 0 -1 0 850)"></path>
        	</svg> : Aumenta lo zoom del grafico
        </li>
    	<li>
        	<svg viewBox="0 0 875 1000" class="icon" height="1em" width="1em">
            	<path d="m0 788l0-876 875 0 0 876-875 0z m688-500l-500 0 0 125 500 0 0-125z" transform="matrix(1 0 0 -1 0 850)"></path>
        	</svg> : Diminuisce lo zoom del grafico
        </li>
    	<li>
        	<svg viewBox="0 0 1000 1000" class="icon" height="1em" width="1em">
            	<path d="m250 850l-187 0-63 0 0-62 0-188 63 0 0 188 187 0 0 62z m688 0l-188 0 0-62 188 0 0-188 62 0 0 188 0 62-62 0z m-875-938l0 188-63 0 0-188 0-62 63 0 187 0 0 62-187 0z m875 188l0-188-188 0 0-62 188 0 62 0 0 62 0 188-62 0z m-125 188l-1 0-93-94-156 156 156 156 92-93 2 0 0 250-250 0 0-2 93-92-156-156-156 156 94 92 0 2-250 0 0-250 0 0 93 93 157-156-157-156-93 94 0 0 0-250 250 0 0 0-94 93 156 157 156-157-93-93 0 0 250 0 0 250z" transform="matrix(1 0 0 -1 0 850)"></path>
        	</svg> e <svg viewBox="0 0 928.6 1000" class="icon" height="1em" width="1em">
            	<path d="m786 296v-267q0-15-11-26t-25-10h-214v214h-143v-214h-214q-15 0-25 10t-11 26v267q0 1 0 2t0 2l321 264 321-264q1-1 1-4z m124 39l-34-41q-5-5-12-6h-2q-7 0-12 3l-386 322-386-322q-7-4-13-4-7 2-12 7l-35 41q-4 5-3 13t6 12l401 334q18 15 42 15t43-15l136-114v109q0 8 5 13t13 5h107q8 0 13-5t5-13v-227l122-102q5-5 6-12t-4-13z" transform="matrix(1 0 0 -1 0 850)"></path>
        	</svg> : Ripristinano la posizione degli assi e lo zoom 
        </li>
    	<li>
        	<svg viewBox="0 0 1000 1000" class="icon" height="1em" width="1em">
            	<path d="M512 409c0-57-46-104-103-104-57 0-104 47-104 104 0 57 47 103 104 103 57 0 103-46 103-103z m-327-39l92 0 0 92-92 0z m-185 0l92 0 0 92-92 0z m370-186l92 0 0 93-92 0z m0-184l92 0 0 92-92 0z" transform="matrix(1.5 0 0 -1.5 0 850)"></path>
        	</svg> : Se attivo mostra il livello dei dati sugli assi
        </li>
    	<li>
        	<svg viewBox="0 0 1500 1000" class="icon" height="1em" width="1em">
            	<path d="m375 725l0 0-375-375 375-374 0-1 1125 0 0 750-1125 0z" transform="matrix(1 0 0 -1 0 850)"></path>
        	</svg> e <svg viewBox="0 0 1125 1000" class="icon" height="1em" width="1em">
        		<path d="m187 786l0 2-187-188 188-187 0 0 937 0 0 373-938 0z m0-499l0 1-187-188 188-188 0 0 937 0 0 376-938-1z" transform="matrix(1 0 0 -1 0 850)"></path>
        	</svg> : Modalità di visualizzazione dei dati
        </li>
	</ul>

	<h3 id="profilo">Profilo</h3>
  	Una volta effettuato l'accesso è possibile cliccare sul proprio nome utente (in alto a sinistra), quindi <span style="color:#eea236">Profilo</span>.
 	Da questa pagina è possibile aggiornare informazioni personali quali utente o password.
	
	<h3 id="info">Informazioni e contatti</h3>
	Il Progetto RAM (Ricerca Attivit&agrave; Motorie) è un'applicazione sviluppata nell'A.S. 2016/2017 all'ITIS G. Fauser di Novara come progetto di maturit&agrave;. &Egrave; stata poi integrata successivamente per migliorarne
	l'usabilit&agrave; e permettere calcoli statistici pi&ugrave; potenti ed efficienti.<br>
	<h4>Contatti</h4>
</div>
<?php show_postmain(); ?>