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

// Administrative guide, to be included in guide.php
chk_access(ADMINISTRATOR); 
include $_SERVER['DOCUMENT_ROOT']."/libraries/lib_admin.php";

$admin = get_privilege(ADMINISTRATOR);
$prof_g = get_privilege(PROFESSOR_GRANTS);
$user = get_privilege(PROFESSOR);
$research = get_privilege(RESEARCH);
$none = get_privilege(NONE);
?>

<div id="admindiv" class="section">
	<h3 id="admin">Amministrazione</h3>

	<p>
		La sezione amministrativa presenta una lista di funzioni utili a gestire
		l'applicazione; è raggiungibile cliccando sul proprio nome utente in 
		ogni pagina.<br>
		È possibile:
		<ul class="nobul">
			<li><a href="#log">Visualizzare i log di utilizzo</a></li>
			<li><a href="#user">Visualizzare, aggiungere e modificare gli utenti</a></li>
			<li><a href="#testadm">Gestire le scuole, le unità di misura, i tipi di dati e le classi dei test</a></li>
			<li><a href="#proj">Modificare il testo della pagina Il progetto</a></li>
			<li><a href="#motd">Modificare il testo mostrato in home page</a></li>
			<li><a href="#studfix">Unire o separare profili degli studenti</a></li>
		</ul>
	</p>

	<h4 id="log">Visualizzare i log di utilizzo</h4>
	<p>
		Dalla pagina di amministrazione si può premere il pulsante <span class="secondarycolor">
		Log di utilizzo</span> per visualizzare una serie di file contenenti la lista di azioni
		compiute dagli utenti divise per data. Cliccando su un file ne viene mostrato il contenuto,
		nella forma<samp> Orario [Indirizzo IP dell'utente] - [Utente] Azione</samp>.
	</p>
	<p>
		Nel log vengono riportati anche gli errori dell'applicazione, che è possibile segnalare
		nella <a href="https://github.com/rb-sl/progettoRAM/issues" target="_blank">repository su Github&#128279;</a>.
	</p>

	<h4 id="user">Visualizzare, aggiungere e modificare gli utenti</h4>
	<p>
		Premendo <span class="primarycolor">Gestione utenti</span> si accede a una pagina contenente
		la lista degli utenti; selezionando <span class="infocolor">Dettagli</span> vengono
		visualizzate tutte le informazioni del profilo ed è possibile aggiornarne i privilegi:
		<ul>
			<li>
				<div class="boxdiv">
					<div class="colorbox <?=$admin['color']?>">
					</div>
				</div>
				<b><?=$admin['text']?></b>: l'utente può accedere a tutte le funzioni 
				dell'applicazione e visualizzare i dati degli altri utenti; gli amministratori 
				possono modificare i privilegi secondo una gerarchia definita dall'ordine di 
				concessione degli stessi
			</li>
			<li>
				<div class="boxdiv">
					<div class="colorbox <?=$prof_g['color']?>">
					</div>
				</div>
				<b><?=$prof_g['text']?></b>: l'utente può accedere alle sole 
				funzioni di registro e statistica ma può aggiungere e modificare i test motori
			</li>
			<li>
				<div class="boxdiv">
					<div class="colorbox <?=$user['color']?>">
					</div>
				</div>
				<b><?=$user['text']?></b>: l'utente può accedere alle funzioni di 
				registro e statistica
			</li>
			<li>
				<div class="boxdiv">
					<div class="colorbox <?=$research['color']?>">
					</div>
				</div>
				<b><?=$research['text']?></b>: l'utente può accedere alla sezione
				statistica
			</li>
			<li>
				<div class="boxdiv">
					<div class="colorbox <?=$none['color']?>">
					</div>
				</div>
				<b><?=$none['text']?></b>: l'utente è registrato ma non può accedere alle
				funzioni dell'applicazione
			</li>
		</ul>
	</p>
	<p>
		Per creare un nuovo utente cliccare su <span class="primarycolor">Aggiungi nuovo</span> e inserire le 
		informazioni richieste, quindi premere su <span class="warningcolor">Aggiungi utente</span>. I voti 
		assegnati all'utente equivalgono a quelli impostati per l'amministratore. <b>NB: 
		questa azione aggiunge solamente l'utente al sistema ma non invia notifiche; le credenziali andranno 
		comunicate all'utente</b> che sarà invitato a modificarle al primo accesso.
	</p>

	<h4 id="testadm">Gestire le scuole, le unità di misura, i tipi di dati e le classi dei test</h4>
	<p>
		Cliccando su <span class="infocolor">Gestione scuole</span> si accede alla lista delle
		scuole presenti nel sistema. È possibile premere su <span class="primarycolor">Aggiungi nuova</span>
		per creare un'altra scuola o <span class="warningcolor">Modifica</span> per cambiarne una già
		esistente. Al termine delle modifiche premere su <span class="primarycolor">Salva</span>.
	</p>
	<p>
		Una scuola che non è collegata a nessuna classe può essere cancellata tramite il
		pulsante <span class="dangercolor">Elimina</span>.
	</p>	
	<p>
		È anche possibile cliccare su <span class="primarycolor">Gestione unità di misura</span>,
		<span class="primarycolor">Gestione tipi dei dati dei test</span> o <span class="primarycolor">Gestione 
		classi dei test</span> per modificare queste informazioni; è importante notare che <b>modificare 
		queste informazioni non modifica le prove presenti nel sistema</b>. Le procedure sono analoghe 
		a quelle descritte per le scuole.
	</p>

	<h4 id="proj">Modificare il testo della pagina Il progetto</h4>
	<p>
		Accedendo alla sezione <span class="infocolor">Cambia descrizione del progetto</span> si può 
		modificare il testo presente nella pagina <a href="/project.php">Il progetto</a>. È possibile
		immettere il testo desiderato secondo le seguenti regole di formattazione:
		<ul>
			<li>
				Racchiudendo una riga tra i simboli<samp> < > </samp>viene generato un titolo; es.
				<div class="flexrow">
					<div class="ulflexdiv">&#60;Titolo></div>
					<div>&rarr;</div>
					<div class="ulflexdiv"><h3 class="nomargin">Titolo</h3></div>
				</div>
			</li>
			<li>
				Il simbolo<samp> - </samp>permette di creare una lista non numerata; es.
				<div class="flexrow">
					<div class="ulflexdiv">
						- Elemento 1<br>
						- Elemento 2
					</div>
					<div>&rarr;</div>
					<div class="ulflexdiv">
						<ul class="nomargin">
							<li>Elemento 1</li>
							<li>Elemento 2</li>
						</ul>
					</div>
				</div>
			</li>
			<li>
				Il simbolo<samp> # </samp>permette di creare una lista numerata; es.
				<div class="flexrow">
					<div class="ulflexdiv">
						# Elemento 1<br>
						# Elemento 2
					</div>
					<div>&rarr;</div>
					<div class="ulflexdiv">
						<ol class="nomargin">
							<li>Elemento 1</li>
							<li>Elemento 2</li>
						</ol>
					</div>
				</div>
			</li>
			<li>
				I simboli<samp> - </samp>e<samp> # </samp>possono essere combinati per creare 
				liste annidate; es.
				<div class="flexrow">
					<div class="ulflexdiv">
						- Elemento 1<br>
						-# Elemento 2<br>
						- Elemento 3
					</div>
					<div>&rarr;</div>
					<div class="ulflexdiv">
						<ul class="nomargin">
							<li>Elemento 1</li>
							<ol class="nomargin">
								<li>Elemento 2</li>
							</ol>
							<li>Elemento 3</li>
						</ul>
					</div>
				</div>
			</li>
			<li>
				Un simbolo<samp> \ </samp>a inizio riga permette di ignorare le regole 
				precedenti e stampare il testo normalmente; es.
				<div class="flexrow">
					<div class="ulflexdiv">
						\&#60;Titolo><br>
						\- Elemento <br>
						\Elemento
					</div>
					<div>&rarr;</div>
					<div class="ulflexdiv">
						&#60;Titolo><br>
						- Elemento<br>
						Elemento
					</div>
				</div>
			</li>
		</ul>
	</p>
	<p>
		In caso di simboli non riconosciuti viene mostrato un messaggio
		e viene chiesto di modificare il testo inserito. <b>NB:</b> è possibile
		aggiungere tag HTML, ma questi non vengono controllati; si consiglia quindi di
		prestarvi attenzione.
	</p>

	<h4 id="motd">Modificare il testo mostrato in home page</h4>
	<p>
		È possibile mostrare un messaggio di benvenuto nella pagina principale; premendo
		<span class="infocolor">Cambia annuncio in home page</span> si raggiunge una pagina
		analoga a quella di modifica del progetto, in cui è possibile inserire il testo
		desiderato con le stesse regole di formattazione. Selezionando la casella Importante
		l'annuncio sarà reso più visibile.
	</p>

	<h4 id="studfix">Unire o separare profili degli studenti</h4>
	<p>
		Premendo <span class="warningcolor">Correzione profili degli studenti</span> si accede
		a un form che permette di unire profili di studenti erroneamente separati o, viceversa,
		di separare un profilo erroneamente assegnato a più studenti.
	</p>
	<p>
		Entrambe le procedure permettono di cercare uno studente nel sistema per id o per nome e
		cognome; selezionando gli studenti desiderati e premendo <span class="primarycolor">Continua</span>
		vengono proposte le opzioni per correggere i profili. Premendo <span class="primarycolor">Unisci</span>
		o <span class="primarycolor">Separa</span> le modifiche vengono salvate e vengono mostrati i nuovi
		id (il messaggio viene anche salvato nel log).
	</p>
</div>
