<?php
// Guide for tests, to be included in guide.php
chk_access(3);

// Statements to retrieve from the database the lists of elements
$tstty_st = prepare_stmt("SELECT nomet FROM TIPOTEST ORDER BY nomet");
$tstt = execute_stmt($tstty_st);
$tstty_st->close();

$stscl_st = prepare_stmt("SELECT nomec FROM CLTEST ORDER BY nomec");
$ctst = execute_stmt($stscl_st);
$stscl_st->close();

$unit_st = prepare_stmt("SELECT udm FROM UNITA ORDER BY udm");
$unit = execute_stmt($unit_st);
$unit_st->close();
?>

<div id="testdiv" class="section">
    <h3 id="test">Test e valutazioni</h3>
    <p>
        La sezione di test raccoglie funzioni per la gestione dei test in un contesto 
        generale (aggiunta, modifica ed eliminazione) e personale (gestione
        dei preferiti e della tabella di valutazione).<br>
        È possibile:
        <ul class="nobul">
            <li><a href="#vtest">Visualizzare le informazioni dei test</a></li>
<?php
if($_SESSION['priv'] <= PROFESSOR_GRANTS)
    echo "<li><a href='#modtst'>Aggiungere e modificare test</a></li>";
if($_SESSION['priv'] <= PROFESSOR)
    echo "<li><a href='#grades'>Modificare i parametri di valutazione</a></li>
        <li><a href='#fav'>Modificare la lista di test preferiti</a></li>";
?>
        </ul>
    </p>

    <h4 id="vtest">Visualizzare le informazioni dei test</h4>
    <p>
        Per visualizzare tutti i dati relativi a un test, dalla sezione Test e valutazioni 
        cliccare sul link corrispondente nella lista a inizio pagina. I parametri 
        visualizzati sono:<br>
        <ul>
            <li>
                <b>Classe del test</b>: è la classificazione del test, che ricade in una 
                delle seguenti:
                <ul>
<?php
while($row = $ctst->fetch_assoc())
	echo "<li>".$row['nomec']."</li>\n";
?>
                </ul>
            </li>
            <li>
                <b>Unità di misura</b>: le unità utilizzabili sono
                <ul>
<?php
while($row = $unit->fetch_assoc())
	echo "<li>".$row['udm']."</li>\n";
?>              
                </ul>
            </li>
            <li>
                <b>Valori migliori</b>: determina se i risultati positivi sono quelli
                maggiori o minori
            </li>
            <li>
                <b>Tipo di valori</b>: serve a determinare alcune statistiche e agevolare 
                l'inserimento delle prove. I valori possibili sono:
                <ul>
<?php
while($row = $tstt->fetch_assoc())
	echo "<li>".$row['nomet']."</li>\n";
?>                 	
                </ul>
            </li>
            <li>
                <b>Sensibilità</b>: indica la più piccola variazione accettata per le 
                misurazioni
            </li>
        </ul>

        Sono poi visualizzate alcune informazioni aggiuntive, in particolare:
        <ul>
            <li>
                <b>Posizione</b>: la posizione che lo studente deve mantenere per 
                iniziare o eseguire il test
            </li>
            <li>
                <b>Materiale aggiuntivo</b>: nel caso siano richiesti strumenti particolari
            </li>
            <li>
                <b>Esecuzione</b>: istruzioni per gli studenti
            </li>
            <li>
                <b>Consigli</b>: istruzioni aggiuntive per il docente
            </li>
            <li>
                <b>Limite</b>: indica la durata della prova
            </li>
            <li>
                <b>Valutazione</b>: indica il valore da inserire nell'applicazione a prova 
                terminata
            </li>
        </ul>
        Queste informazioni sono facoltative (eccetto la valutazione).
    </p>
<?php
if($_SESSION['priv'] <= PROFESSOR)
{
?>
    <h4 id="modtst">Aggiungere e modificare test</h4>
    <p>
        Per creare un nuovo test accedere alla sezione Test e Valutazioni e premere su 
        <span class="primarycolor">Aggiungi nuovo</span>, inserendo le informazioni richieste;
        i campi testuali, a parte nome e valutazione, possono essere lasciati vuoti se non 
        necessari. Al termine dell'inserimento premere <span class="warningcolor">Inserisci test</span>.
    </p>
    <p>
        La procedura di modifica è analoga, ed è accessibile dalla pagina di un test premendo su 
        <span class="warningcolor">Modifica test</span>; le modifiche saranno effettive dopo aver 
        confermato con <span class="warningcolor">Aggiorna valori test</span>.
        <b>NB</b>: Modificare i parametri di un test non modifica i valori inseriti nel database.
    </p>
    <p>
        È infine possibile eliminare un test con il bottone <span class="dangercolor">Elimina test</span> 
        nella pagina di modifica, ma solo se non esistono prove associate.
    </p>

    <h4 id="grades">Modificare i parametri di valutazione</h4>
    <p>
        La tabella di valutazione in fondo alla pagina permette di creare una corrispondenza tra il 
        percentile raggiunto da uno studente in una prova e il voto assegnato dal sistema. Per 
        alterarne i valori, modificare le percentuali di assegnamento di un voto (assicurandosi 
        che la somma sia 100); l'ultima colonna della tabella mostra il range di percentili cui 
        viene assegnato ogni voto. Se si desidera non assegnarne una valutazione è sufficiente 
        portare a 0 la sua percentuale.
    </p>
    <p>
        Per salvare le nuove impostazioni premere <span class="warningcolor">Aggiorna tabella voti</span>. 
        Il <a href="graph">grafico</a> dà una rappresentazione visuale della distribuzione dei voti.
    </p>

    <h4 id="fav">Modificare la lista di test preferiti</h4>
    <p>
        Dalla pagina di Test e valutazioni è possibile premere su <span class="warningcolor">Modifica 
        preferiti</span> per accedere a una lista di selezione dei test (in cui sono riportati alcuni 
        dettagli premendo su <span class="primarycolor">Mostra informazioni</span>); se non si è
        interessati a condurne uno se ne può deselezionare la casella e premere su 
        <span class="primarycolor">Aggiorna</span>. A questo punto il nome del test apparirà 
        disabilitato e non sarà più proposto al momento dell'inserimento di nuove prove nel registro.
    </p>
    <p>
        Per riabilitare un test è sufficiente riattivare la sua casella e salvare.
    </p>
<?php
}
?>
</div>
