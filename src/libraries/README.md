# File in /librerie/

## [general.php](general.php)
Libreria principale dell'applicazione, contiene funzioni utili a diversi script.

### `errors()`
La funzione errors permette di visualizzare tutti gli errori generati da php per fini di debugging. Viene attivata se l'attributo `e` del GET è settato.

### `writelog($action)`
Permette di scriverre `$action` nel log dell'applicazione, aggiungendo utente e indirizzo IP.

### `chk_access($priv)`
Funzione di controllo accessi. Il livello indica i privilegi:
* 0: Amministratore
* 1: Professore
* 2: Statistica
* 5: Visitatore
Il caricamento viene bloccato se il livello non è corretto.

### `chk_prof($fk_prof)`
Funzione di controllo per il privilegio di accesso di un professore a una classe. Un amministratore può sempre visualizzare le classi.

### `get_server_conf()`
Funzione per ottenere la configurazione del database in un oggetto php. 

### `connect()`
Funzione di connessione al database. 

### `function confirm($quest)`
Stampa una richiesta di procedere all'utente.

### `maiuscolo($stringa)`
Stampa la versione maiuscola di una parola, comprese le vocali accentate.

### `prepare_stmt($query)`
Data una query crea il prepared statement.

### `execute_stmt($stmt)`
Esecuzione di un prepared statement e controllo errori.

### `query_error($stage, $query)`
Funzione per la stampa degli errori di query.

### `show_premain($title, $stat)`
Funzione base per ogni pagina front-end, stampa le informazioni statiche della pagina. Se `$stat` è settato mostra le opzioni statistiche; i suoi campi sono:
* `anno1`: L'anno iniziale per le query
* `anno2`: L'anno finale per le query

### `show_postmain()`
Mostra le parti statiche finali delle pagine front-end

## lib_reg.php

### `is_accettable`
Funzione per controllare l'appartenenza di un valore all'intervallo di accetabilità di un test.
Viene usata la funzione avg +- 10std poiché, per la disuguaglianza di Chebychev,
per ogni k almeno la frazione (1-1/(k^2))-esima dei dati cade nell'intervallo avg +- k*std
k=10 => 99% dati viene accettato