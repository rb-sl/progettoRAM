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

// Guide for the statistics section, to be included in guide.php
chk_access(RESEARCH);
include $_SERVER['DOCUMENT_ROOT']."/libraries/lib_stat.php";
?>

<div id="statdiv" class="section">
	<h3 id="stat">Statistica</h3>
	
	<p>
		La sezione statistica permette di visualizzare ed elaborare dati aggregati sulle
		prove dei test ed è caratterizzata dalla presenza del <a href="#menustat">menu 
		statistico</a> (per modificare la popolazione i cui dati vengono elaborati)
		e da diversi tipi di <a href="#graph">grafici</a>.<br>
		È possibile:
		<ul class="nobul">
			<li><a href="#genstat">Visualizzare statistiche generali</a></li>
			<li><a href="#statt">Visualizzare le statistiche dei test</a></li>
			<li><a href="#correlation">Studiare la correlazione dei test</a></li>
		</ul>
	</p>
	
	<h4 id="genstat">Visualizzare statistiche generali</h4>
	<p>
		Nella pagina principale della sezione sono disponibili informazioni generali 
		sull'applicazione; i grafici a torta descrivono la suddivisione delle prove 
		in base a:
		<ul>
			<li>Test effettuati</li>
			<li>Sesso degli studenti</li>
			<li>Classe di appartenenza</li>
			<li>Anno</li>
		</ul>
	</p>

	<h4 id="statt">Visualizzare le statistiche dei test</h4>
	<p>
		Selezionando il nome di un test dalla lista nella pagina principale è possibile 
		visualizzarne alcune statistiche. In particolare:
		<ul>
			<li>
				Numero totale di prove
				</li>
			<li>
				<a href="https://it.wikipedia.org/wiki/Media_(statistica)#Media_aritmetica" 
					target="_blank">Media&#128279;</a>
			</li>
			<li>
				<a href="https://it.wikipedia.org/wiki/Mediana_(statistica)" 
					target="_blank">Mediana&#128279;</a>
			</li>
			<li>
				<a href="https://it.wikipedia.org/wiki/Scarto_quadratico_medio" 
					target="_blank">Deviazione standard&#128279;</a>
			</li>
			<li>
				Record positivi (con classe e anno) e negativi
			</li>
		</ul>
		È poi possibile visualizzare alcuni grafici secondo le opzioni:
		<ul>
			<li>
				<b>Valori</b>: genera un <a href="#histogram">istogramma</a> in base ai dati
			</li>
			<li>
				<b>Box plot</b>: mostra alcune statistiche aggregate in un <a href="#boxplot">diagramma 
				a scatole e baffi</a>.
			</li>
			<li>
				<b>Box plot (Anni)</b>: un box plot con i dati suddivisi per anno
			</li>
			<li>
				<b>Box plot (Classi)</b>: un box plot con i dati suddivisi per classe
			</li>
			<li>
				<b>Box plot (Sesso)</b>: un box plot con i dati suddivisi per sesso
			</li>
			<li>
				<b>Valori percentili</b>: diagramma che mostra l'andamento dei percentili dei dati
			</li>
		</ul>
	</p>

	<h4 id="correlation">Studiare la correlazione dei test</h4>
	<p>
		Premendo il pulsante <span class="primarycolor">Correlazione campionaria</span> nella pagina principale
		della sezione si raggiunge una pagina dedicata allo studio della 
		<a href="https://it.wikipedia.org/wiki/Correlazione_(statistica)" target="_blank">correlazione&#128279;</a> 
		tra i test in base alle prove.
	</p>
	<p>
		La prima parte della pagina mostra una tabella che riporta gli 
		<a href="https://it.wikipedia.org/wiki/Indice_di_correlazione_di_Pearson" target="_blank">indici di 
		correlazione di Pearson</a> per ogni coppia di test. Per evidenziare i rapporti di correlazione tra i test
		premere il pulsante <span class="primarycolor">Colori</span>; a colore più scuro corrisponde una maggiore
		correlazione (positiva o negativa a seconda del segno).
	</p>
	<p>
		È poi possibile, per ogni coppia di test, visualizzare il <a href="#scatter">diagramma di dispersione</a>
		delle prove, premendo su una cella contenente un dato. I diagrammi sono riportati anche nella matrice 
		di dispersione nella seconda parte della pagina (in caso sia troppo grande per la visualizzazione si
		consiglia di scaricare l'immagine).
	</p>
	<p>
		<b>NB</b>: Vengono visualizzati solo i valori e i grafici dei test con almeno <?=CORRELATION_THRESH?>
		valori affinché i risultati siano significativi.
	</p>
</div>

<div class="section">
	<h3 id="menustat">Sottomenu statistico</h3>
	In alcune pagine è presente un menu statistico per permettere diverse selezioni dei dati. Per modificare 
	la popolazione è sufficiente modificare:
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
			<b>Solo personali</b>: se attivato permette di scartare i dati registrati da altri utenti
		</li>    
	</ul>
	Il pulsante <span class="primarycolor">Aggiorna</span> diventa arancione dopo che uno di questi elementi 
	viene modificato per segnalare che i dati visualizzati non corrispondono alla selezione corrente. 
	Premendolo vengono richiesti i nuovi dati e ritornerà blu dopo l'aggiornamento.
</div>

<div class="section">
	<h3 id="graph">Grafici</h3>
	I grafici dell'applicazione sono realizzati grazie alla <a href="https://plot.ly/javascript/" 
	target="_blank">Plotly JavaScript open source graphing library&#128279;</a>. 
	Questi grafici permettono di visualizzare informazioni aggiuntive passando il cursore su un insieme di 
	dati e presentano alcune opzioni nella parte destra del grafico:
	<ul>
		<li>
			<svg viewBox="0 0 1000 1000" class="icon" height="1em" width="1em">
				<path d="m500 450c-83 0-150-67-150-150 0-83 67-150 150-150 83 0 150 67 150 150 0 83-67 150-150 
				150z m400 150h-120c-16 0-34 13-39 29l-31 93c-6 15-23 28-40 28h-340c-16 
				0-34-13-39-28l-31-94c-6-15-23-28-40-28h-120c-55 0-100-45-100-100v-450c0-55 45-100 100-100h800c55 
				0 100 45 100 100v450c0 55-45 100-100 100z m-400-550c-138 0-250 112-250 250 0 138 112 250 250 250 
				138 0 250-112 250-250 0-138-112-250-250-250z m365 380c-19 0-35 16-35 35 0 19 16 35 35 35 19 0
				35-16 35-35 0-19-16-35-35-35z" transform="matrix(1 0 0 -1 0 850)"></path>
			</svg> : Premendo l'icona è possibile scaricare un'immagine del grafico sul proprio dispositivo
		</li>
		<li>
			<svg viewBox="0 0 1000 1000" class="icon" height="1em" width="1em">
				<path d="m1000-25l-250 251c40 63 63 138 63 218 0 224-182 406-407 406-224 0-406-182-406-406s183-406 
				407-406c80 0 155 22 218 62l250-250 125 125z m-812 250l0 438 437 0 0-438-437 0z m62 375l313 0 
				0-312-313 0 0 312z" transform="matrix(1 0 0 -1 0 850)"></path>
			</svg> : Se attivo permette di selezionare una porzione del grafico per vederla in dettaglio
		</li>
		<li>
			<svg viewBox="0 0 1000 1000" class="icon" height="1em" width="1em">
				<path d="m1000 350l-187 188 0-125-250 0 0 250 125 0-188 187-187-187 125 0 0-250-250 0 0 125-188-188 
				186-187 0 125 252 0 0-250-125 0 187-188 188 188-125 0 0 250 250 0 0-126 187 188z"
				transform="matrix(1 0 0 -1 0 850)"></path>
			</svg> : Selezionando questa opzione è possibile muovere il grafico
		</li>
		<li>
			<svg viewBox="0 0 1000 1000" class="icon" height="1em" width="1em">
				<path d="m0 850l0-143 143 0 0 143-143 0z m286 0l0-143 143 0 0 143-143 0z m285 0l0-143 143 0 0 
				143-143 0z m286 0l0-143 143 0 0 143-143 0z m-857-286l0-143 143 0 0 143-143 0z m857 0l0-143 143 0 
				0 143-143 0z m-857-285l0-143 143 0 0 143-143 0z m857 0l0-143 143 0 0 143-143 0z m-857-286l0-143 
				143 0 0 143-143 0z m286 0l0-143 143 0 0 143-143 0z m285 0l0-143 143 0 0 143-143 0z m286 0l0-143 
				143 0 0 143-143 0z" transform="matrix(1 0 0 -1 0 850)"></path>
			</svg> : Permette di selezionare i dati dati in un rettangolo
		</li>
		<li>
			<svg viewBox="0 0 1031 1000" class="icon" height="1em" width="1em">
				<path d="m1018 538c-36 207-290 336-568 286-277-48-473-256-436-463 10-57 36-108 76-151-13-66 
				11-137 68-183 34-28 75-41 114-42l-55-70 0 0c-2-1-3-2-4-3-10-14-8-34 5-45 14-11 34-8 45 4 1 1 
				2 3 2 5l0 0 113 140c16 11 31 24 45 40 4 3 6 7 8 11 48-3 100 0 151 9 278 48 473 255 436 462z 
				m-624-379c-80 14-149 48-197 96 42 42 109 47 156 9 33-26 47-66 41-105z m-187-74c-19 16-33 37-39 
				60 50-32 109-55 174-68-42-25-95-24-135 8z m360 75c-34-7-69-9-102-8 8 62-16 128-68 170-73 59-175 
				54-244-5-9 20-16 40-20 61-28 159 121 317 333 354s407-60 434-217c28-159-121-318-333-355z" 
				transform="matrix(1 0 0 -1 0 850)"></path>
			</svg> : Permette di selezionare i dati a mano libera
		</li>
		<li>
			<svg viewBox="0 0 875 1000" class="icon" height="1em" width="1em">
				<path d="m1 787l0-875 875 0 0 875-875 0z m687-500l-187 0 0-187-125 0 0 187-188 0 0 125 188 0 0 
				187 125 0 0-187 187 0 0-125z" transform="matrix(1 0 0 -1 0 850)"></path>
			</svg> : Aumenta lo zoom del grafico
		</li>
		<li>
			<svg viewBox="0 0 875 1000" class="icon" height="1em" width="1em">
				<path d="m0 788l0-876 875 0 0 876-875 0z m688-500l-500 0 0 125 500 0 0-125z" 
				transform="matrix(1 0 0 -1 0 850)"></path>
			</svg> : Diminuisce lo zoom del grafico
		</li>
		<li>
			<svg viewBox="0 0 1000 1000" class="icon" height="1em" width="1em">
				<path d="m250 850l-187 0-63 0 0-62 0-188 63 0 0 188 187 0 0 62z m688 0l-188 0 0-62 188 0 0-188 
				62 0 0 188 0 62-62 0z m-875-938l0 188-63 0 0-188 0-62 63 0 187 0 0 62-187 0z m875 188l0-188-188 
				0 0-62 188 0 62 0 0 62 0 188-62 0z m-125 188l-1 0-93-94-156 156 156 156 92-93 2 0 0 250-250 0 
				0-2 93-92-156-156-156 156 94 92 0 2-250 0 0-250 0 0 93 93 157-156-157-156-93 94 0 0 0-250 250 
				0 0 0-94 93 156 157 156-157-93-93 0 0 250 0 0 250z" transform="matrix(1 0 0 -1 0 850)"></path>
			</svg> e <svg viewBox="0 0 928.6 1000" class="icon" height="1em" width="1em">
				<path d="m786 296v-267q0-15-11-26t-25-10h-214v214h-143v-214h-214q-15 0-25 10t-11 26v267q0 1 0 
				2t0 2l321 264 321-264q1-1 1-4z m124 39l-34-41q-5-5-12-6h-2q-7 0-12 3l-386 322-386-322q-7-4-13-4-7 
				2-12 7l-35 41q-4 5-3 13t6 12l401 334q18 15 42 15t43-15l136-114v109q0 8 5 13t13 5h107q8 0 
				13-5t5-13v-227l122-102q5-5 6-12t-4-13z" transform="matrix(1 0 0 -1 0 850)"></path>
			</svg> : Ripristinano la posizione degli assi e lo zoom 
		</li>
		<li>
			<svg viewBox="0 0 1000 1000" class="icon" height="1em" width="1em">
				<path d="M512 409c0-57-46-104-103-104-57 0-104 47-104 104 0 57 47 103 104 103 57 0 103-46 
				103-103z m-327-39l92 0 0 92-92 0z m-185 0l92 0 0 92-92 0z m370-186l92 0 0 93-92 0z m0-184l92 
				0 0 92-92 0z" transform="matrix(1.5 0 0 -1.5 0 850)"></path>
			</svg> : Se attivo mostra il livello dei dati sugli assi
		</li>
		<li>
			<svg viewBox="0 0 1500 1000" class="icon" height="1em" width="1em">
				<path d="m375 725l0 0-375-375 375-374 0-1 1125 0 0 750-1125 0z" 
				transform="matrix(1 0 0 -1 0 850)"></path>
			</svg> e <svg viewBox="0 0 1125 1000" class="icon" height="1em" width="1em">
				<path d="m187 786l0 2-187-188 188-187 0 0 937 0 0 373-938 0z m0-499l0 1-187-188 188-188 0 
				0 937 0 0 376-938-1z" transform="matrix(1 0 0 -1 0 850)"></path>
			</svg> : Modalità di visualizzazione dei dati
		</li>
	</ul>

	<h4>Tipi di grafici</h4>
	I possibili grafici disegnati dall'applicazione sono:
	<ul>
		<li>
			<a href="https://it.wikipedia.org/wiki/Diagramma_a_torta"
				target="_blank">Grafico a torta&#128279;</a>:
			visualizzato nella sezione statistica per mostrare la suddivisione della popolazione
		</li>
		<li>
			<a id="histogram" href="https://it.wikipedia.org/wiki/Istogramma" 
				target="_blank">Istogramma&#128279;</a>: utilizzato nelle statistiche dei test
			per mostrare la distribuzione dei dati
		</li>
		<li>
			<a id="boxplot" href="https://it.wikipedia.org/wiki/Diagramma_a_scatola_e_baffi"
				target="_blank">Box plot o Diagramma a scatola e baffi&#128279;</a>: utilizzato per 
			dare una visualizzazione aggregata dei dati nelle statistiche dei test. Le voci mostrate
			sono:
			<ul>
				<li><b>Min</b>: il minimo assoluto dei dati</li>
				<li><b>Q1</b>: il valore del primo quartile (o 25° percentile)</li>
				<li><b>Median</b>: la mediana (o 50° percentile)</li>
				<li><b>Mean</b>: la media</li>
				<li><b>Q3</b>: il valore del terzo quartile (o 75° percentile)</li>
				<li><b>Max</b>: il massimo assoluto dei dati</li>
			</ul>
		</li>
		<li>
			<span id="line">Grafico a linea</span>: mostra l'andamento dei dati in 
			funzione di una variabile; è utilizzato nella sezione di statistica dei test per 
			mostrare i percentili
		</li>
		<li>
			<a id="radar" href="https://it.wikipedia.org/wiki/Diagramma_di_Kiviat" 
			target="_blank">Grafico radar&#128279;</a>: mostra i risultati degli studenti 
			nelle loro pagine di registro
		</li>
		<li>
			<a id="scatter" href="https://it.wikipedia.org/wiki/Grafico_di_dispersione" 
				target="_blank">Diagramma di dispersione&#128279;</a>: utilizzato per mostrare la correlazione
			tra i test
		</li>
	</ul>
</div>
