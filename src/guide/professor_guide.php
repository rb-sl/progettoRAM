<?php
// Guide for professors, to be included in guide.php, if needed
chk_access(PROFESSOR);
?>

<div id="regdiv" class="section">
    <h3 id="reg">Registro</h3>

    <p>
        La sezione di registro presenta una lista delle classi dell'utente e permette
        di effettuare inserimenti di prove e elaborare i dati delle classi o degli
        studenti.<br>
        È possibile:
        <ul class="nobul">
            <li><a href="#addcl">Aggiungere una classe</a></li>
            <li><a href="#vcl">Visualizzare e modificare le prove di una classe</a></li>
            <li><a href="#stcl">Elaborare i dati della classe</a></li>
            <li><a href="#modcl">Modificare le informazioni di una classe</a></li>
            <li><a href="#visst">Visualizzare le prove di uno studente</a></li>
            <li><a href="#modst">Modificare le informazioni di uno studente</a></li>
        </ul>
    </p>

    <h4 id="addcl">Aggiungere una classe</h4>
    <p>
        Dalla pagina di registro, premendo il pulsante <span class="primarycolor">Aggiungi classe</span>, 
        è possibile registrare nel database una nuova classe. Premendo <span class="primarycolor">Promuovi 
        classe precedente</span> e selezionando la classe da promuovere vengono compilati automaticamente i 
        dati della classe e viene importata la lista degli studenti (<em>Studenti promossi nella nuova classe</em>). 
        Per non importare uno studente deselezionarne la voce. 
    </p>
    <p>
        Nella stessa pagina si possono aggiungere nuovi studenti, inserendo le informazioni richieste 
        nella tabella Nuovi studenti. Se uno di questi presentasse dati congruenti con quelli già presenti 
        nel sistema viene data la possibilità di selezionarne la provenienza (<em>Possibili studenti già 
        registrati</em>).
    </p>
    <p>
        Premendo su <span class="warningcolor">Inserisci classe</span> si raggiungerà la nuova pagina di registro.
    </p>

    <h4 id="vcl">Visualizzare e modificare le prove di una classe</h4>
    <p>
        Cliccando sul pulsante di una classe viene mostrata la sua pagina di registro. La tabella 
        contiene l'elenco degli studenti della classe e le loro prove; passando il puntatore su una prova 
        è possibile visualizzare in sovrimpressione la data in cui è stata effettuata.
        Premere il pulsante <span class="primarycolor">Medie e mediane</span> per visualizzare o nascondere
        alcune statistiche relative ai test.
    </p>
    <p>
        Sono possibili le seguenti operazioni sui dati:
        <ul>
            <li>
                <b>Aggiungere prove</b>: premendo <span class="warningcolor">Aggiungi test</span> è possibile 
                insrerire le prove di un test. Selezionare dal menu a tendina il test desiderato e inserire 
                i valori (Viene caricata anche l'unità di misura da utilizzare nell'inserimento). Facendo 
                doppio click su una cella vuota è possibile aggiungere una singola prova a test già registrati. 
            </li>
            <li>
                <b>Modificare prove</b>: facendo doppio click sul valore di una prova è possibile modificarlo. 
            </li>
            <li>
                <b>Eliminare prove</b>: fare doppio click sul valore della prova da eliminare e cancellarne il 
                contenuto.
            </li>
        </ul>

        Premere <span class="warningcolor">Salva</span> per finalizzare l'inserimento o <span class="dangercolor">
        Annulla</span> per scartare le modifiche.
    </p>

    <h4 id="stcl">Elaborare i dati di una classe</h4>
    <p>
        Dalla pagina di registro di una classe è possibile premere il pulsante <span class="primarycolor">
        Elaborazione dati della classe</span> per cambiare modalità di visualizzazione dei dati; in particolare 
        si possono selezionare:
        <ul>
            <li><a href="https://it.wikipedia.org/wiki/Centile" target="_blank">Valori percentili&#128279;</a></li>
            <li><a href="https://it.wikipedia.org/wiki/Standardizzazione_(statistica)" target="_blank">Valori standard&#128279;</a></li>
            <li>Voti</li>
        </ul>
	
        Cliccando sui pulsanti <span class="primarycolor">Medie e mediane</span> o <span class="primarycolor">
        Colori</span> si possono visualizzare statistiche sulla classe e codificare per colore i risultati dei 
        test. &Egrave; presente il <a href="#menustat">Sottomenu statistico</a> per effettuare calcoli su 
        popolazioni differenti.
    </p>

    <h4 id="modcl">Modificare le informazioni una classe</h4>
    <p>
        Premendo il pulsante <span class="warningcolor">Modifica</span> è possibile cambiare le informazioni 
        della classe e gestirne gli studenti. Le modalità sono analoghe a quelle 
        dell'<a href="#addcl">inserimento della classe</a>.
        Premere <span class="warningcolor">Aggiorna classe</span> per salvare le modifiche, 
        <span class="warningcolor">Annulla</span> per annullarle o <span class="dangercolor">Elimina classe</span> 
        per rimuoverla dal database; per rimuovere specifici studenti dalla classe deselezionarne la voce 
        e salvare le modifiche.
    </p>
	<p>
        <b>NB</b>: Eliminando la classe o un suo studente saranno persi tutti i dati delle prove collegate.
    </p>

	<h4 id="visst">Visualizzare le prove di uno studente</h4>
    <p>
        Dalla pagina di registro di una classe è possibile cliccare sul nome di uno studente per raggiungere 
        una pagina analoga al registro della classe, ma che riporta i test effettuati dallo studente per 
        ogni anno. È possibile premere il pulsante <span class="primarycolor">Medie e mediane</span> per mostare 
        questi dati, o premere su <span class="primarycolor">Elaborazione dati</span> per visualizzare valori 
        percentili, standard o voti delle prove.
    </p>
	<p>
        In questa pagina è presente un <a href="#radar">grafico di tipo radaro</a> per visualizzare i risultati 
        ottenuti per ogni classe; è possibile modificare il campione attraverso 
        <a href="#menustat">il menu statistico</a>.
    </p>

    <h4 id="modst">Modificare le informazioni di uno studente</h4>
    <p>
        Premendo il pulsante <span class="warningcolor">Modifica</span> nella pagina di uno studente si possono 
        aggiornare cognome, nome e sesso.
    </p>
</div>
