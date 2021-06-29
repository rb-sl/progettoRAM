<?php
// Copyright 2021 Roberto Basla

// This file is part of progettoRAM.

// progettoRAM is free software: you can redistribute it and/or modify
// it under the terms of the GNU Affero General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

// progettoRAM is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU Affero General Public License for more details.

// You should have received a copy of the GNU Affero General Public License
// along with progettoRAM.  If not, see <http://www.gnu.org/licenses/>.

// Guide for tests, to be included in guide.php
chk_access(RESEARCH);

// Statements to retrieve from the database the lists of elements
$tstty_st = prepare_stmt("SELECT datatype_name FROM datatype ORDER BY datatype_name");
$tstt = execute_stmt($tstty_st);
$tstty_st->close();

$stscl_st = prepare_stmt("SELECT testtype_name FROM testtype ORDER BY testtype_name");
$ctst = execute_stmt($stscl_st);
$stscl_st->close();

$unit_st = prepare_stmt("SELECT unit_name FROM unit ORDER BY unit_name");
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
if(chk_auth(PROFESSOR_GRANTS))
	echo "<li><a href='#modtst'>Aggiungere e modificare test</a></li>";
if(chk_auth(PROFESSOR))
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
				<b>Tipo di test</b>: è la classificazione del test, che ricade in una 
				delle seguenti:
				<ul>
<?php
while($row = $ctst->fetch_assoc())
	echo "<li>".htmlentities($row['testtype_name'])."</li>\n";
?>
				</ul>
			</li>
			<li>
				<b>Unità di misura</b>: le unità utilizzabili sono
				<ul>
<?php
while($row = $unit->fetch_assoc())
	echo "<li>".htmlentities($row['unit_name'])."</li>\n";
?>              
				</ul>
			</li>
			<li>
				<b>Valori migliori</b>: determina se i risultati positivi sono quelli
				maggiori o minori
			</li>
			<li>
				<b>Tipo di dati</b>: serve a classificare meglio i test e agevolare 
				l'inserimento delle prove. I valori possibili sono:
				<ul>
<?php
while($row = $tstt->fetch_assoc())
	echo "<li>".$row['datatype_name']."</li>\n";
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
if(chk_auth(PROFESSOR))
{
?>
	<h4 id="modtst">Aggiungere e modificare test</h4>
	<p>
		Per creare un nuovo test accedere alla sezione Test e Valutazioni e premere su 
		<span class="primarycolor">Aggiungi nuovo</span>;
		i campi testuali, a parte nome e valutazione, possono essere lasciati vuoti se non 
		necessari. Al termine dell'inserimento premere su <span class="warningcolor">Inserisci test</span>.
	</p>
	<p>
		La procedura di modifica è analoga, ed è accessibile dalla pagina di un test premendo su 
		<span class="warningcolor">Modifica test</span>; le modifiche saranno effettive dopo aver 
		confermato con <span class="warningcolor">Aggiorna test</span>.
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
		preferiti</span> per accedere a una lista dei test (in cui sono riportati alcuni 
		dettagli premendo su <span class="primarycolor">Mostra informazioni</span>); se non si è
		interessati a condurne uno se ne può deselezionare la casella. Dopo aver premuto su 
		<span class="primarycolor">Aggiorna</span>, il nome del test apparirà disabilitato e non 
		sarà più proposto al momento dell'inserimento di nuove prove nel registro.
	</p>
	<p>
		Per riabilitare un test è sufficiente riattivare la sua casella e salvare.
	</p>
<?php
}
?>
</div>
