# File in /libraries/

## [general.php](general.php)
Libreria principale dell'applicazione, contiene funzioni utili a diversi script.

### Costanti
A inizio file sono presenti una serie di costanti:
* Per indicare i percorsi ai file necessari all'applicazione
* Per il controllo accessi
* Per gestire gli errori

Segue una lista delle funzioni

### `errors()`
La funzione errors permette di visualizzare tutti gli errori generati da php per fini di debugging. Viene attivata se l'attributo `e` del GET è settato.

### `chk_access($privileges)`
Funzione di controllo accessi. Se necessario, il caricamento viene bloccato se il livello non è corretto.

### `writelog($action)`
Permette di scrivere `$action` nel log dell'applicazione, aggiungendo utente e indirizzo IP.

### `chk_auth($privileges)`
Restituisce una booleana per indicare se i privilegi sono sufficienti rispetto al parametro passato.

### `chk_prof($user_fk)`
Funzione di controllo per il privilegio di accesso di un professore a una classe. Un amministratore può sempre visualizzare le informazioni.

### `get_server_conf()`
Funzione per ottenere la configurazione del database in un oggetto php. 

### `connect()`
Funzione di connessione al database MySQL e controllo errori. 

### `confirm($quest)`
Mostra una richiesta di procedere all'utente.

### `set_alert($msg)`
Aggiunge un messaggio da mostrare all'utente nella successiva pagina

### `display_alerts()`
Mostra i messaggi salvati con `set_alert`

### `set_error($error)`
Imposta l'errore da mostrare all'utente.

### `display_errors()`
Mostra gli errori definiti da `set_error`

### `maiuscolo($stringa)`
Stampa la versione maiuscola di una parola, comprese le vocali accentate.

### `year_span()`
Ottiene il range di anni delle classi presenti nel sistema.

### `get_current_year()`
Ritorna l'annp scolastico attuale (la suddivisione `e effettuata ad agosto)

### `prepare_stmt($query)`
Data una query, crea il prepared statement.

### `execute_stmt($stmt)`
Esecuzione di un prepared statement e controllo errori.

### `query_error($stage, $query)`
Funzione per la stampa degli errori MySQL.

### `show_premain($title, $stat, $fullwidth)`
Funzione base per ogni pagina front-end, stampa le informazioni statiche della pagina. Se `$stat` è settato mostra le opzioni statistiche, mentre `$fullwidth` controlla la larghezza del `main`.

### `show_postmain()`
Mostra le parti statiche finali delle pagine front-end.

## [lib_admin.php](lib_admin.php)
Questa libreria contiene funzioni utili nella sezione amministrativa. Le costanti a inizio file indicano alcuni elementi di markup per l'input di testi.

### `get_privilege($privileges)`
Funzione per ottenere il testo e il colore associati a un livello di permessi

### `can_downgrade($id)`
Ritorna un valore positivo se l'amministratore corrente può diminuire il livello di privilegi dell'utente `$id`.

### `downgrade_rec($cur_id)`
Funzione di ricerca ricorsiva nell'albero di permessi concessi dall'utente attuale per rispondere alla funzione `can_downgrade`.

### `compile_text($text)`
Funzione di traduzione dal linguaggio di markup semplificato a HTML. Permette una gestione ricorsiva delle liste.

## [lib_reg.php](lib_reg.php)
Questo file è una collezione di funzioni utilizzate nelle pagine della sezione di registro dell'applicazione.

### `get_class_info($id)`
Ottiene le informazioni della classe `$id`.

### `build_chk_table($class, $prom)`
Costruisce la lista di studenti della classe per inserimento o modifica. Se `$prom` è attiva viene proposta la creazione della successiva importando gli studenti da promuovere.

### `col_stud()`
Ritorna la lista di studenti da mostrare nelle pagine di registro.

### `col_class($stud)`
Ritorna la colonna delle classi da mostrare nella pagina di profilo dello studente `$stud`, controllando che l'utente sia professore di almeno una (o amministratore).

### `get_test($id, $forstud)`
Ottiene la lista di test; se `$forstud` è `false`, `$id` indica la classe, altrimenti si riferisce allo studente.

### `class_students($isupdate, $class, $precedent, $newln, $newfn, $gnd, $external)`
Funzione per ottenere la lista delgi id degli studenti di una classe. I parametri indicano:
* `$isupdate`: se la classe viene inserita o modificata
* `$class`: l'id della classe
* `$precedent`: lista degli studenti già presenti nella classe
* `$newln`: lista dei cognomi dei nuovi studenti
* `$newfn`: lista dei nomi dei nuovi studenti
* `$gnd`: lista dei sessi dei nuovi studenti
* `$external`: lista degli studenti proposti come possibili già registrati

### `color_from_val($val, $color, $isperc)`
Funzione per ottenere il colore associato a `$val` dalla lista `$color` per i valori percentili o standard (a seconda del flag `$isperc`).

### `color_from_grade($val, $color)`
Funzione per ottenere il colore associato al voto `$val` dalla lista `$color`.

### `get_avgmed($class, $vals, $isperc, $forstud)`
Dati l'id della classe (`$id`) e i valori dei suoi test (`$vals`) ritorna la lista di medie e mediane per ogni test, per ogni studente e generali. `$isperc` indica se i valori sono percentili o standard e `$forstud` permette di calcolare i valori per le classi o per gli studenti.

### `get_avgmed_grades($class, $rstud, $forstud)`
Funzione analoga a `get_avgmed` per i voti.

### `get_color_prc()`
Funzione per ottenere dal database i colori associati ai percentili

### `get_color_std()`
Funzione per ottenere dal database i colori associati ai valori standard.

### `get_color_gr()`
Funzione per ottenere dal database i colori associati ai voti.

### `get_tests($id, $forstud)`
Ottiene la lista di test fatti da una classe o da uno studente, a seconda del flag `$forstud`.

### `get_perc($class, $cond, $forstud)`
Funzione per ottenere i valori percentili di una classe. `$cond` indica possibili restrizioni aggiuntive per la selezione dei valori, mentre `$forstud` consente di ottenere i valori per la pagina di una classe o di uno studente.

### `get_std($class, $cond, $forstud)`
Funzione analoga a `get_perc` per l'ottenimento dei valori standard.

### `get_grades($class, $cond, $forstud)`
Funzione per ottenere i voti, si appoggia a `get_perc`.

### `class_in_years($class, $year1, $year2)`
Funzione per controllare che una classe appartenga all'intervallo definito da `$year1` e `$year2`. 

### `is_accettable`
Funzione per controllare l'appartenenza di un valore all'intervallo di accettabilità di un test.
Viene usata la funzione avg +- 10std poiché, per la disuguaglianza di Chebychev,
per ogni k almeno la frazione (1-1/(k^2))-esima dei dati cade nell'intervallo avg +- k*std.
Con k=10 il 99% dei dati viene accettato.

### `show_cl_form($cl, $section, $year)`
Funzione per mostrare il form di inserimento o modifica di una classe.

## [lib_stat.php](lib_stat.php)
Libreria contenente funzioni utili alla sezione statistica. 

Le costanti a inizio file permettono di impostare il numero di valori ritenuti sufficienti per gli studi statistici e gestire i grafici.

Molte funzioni accettano un parametro `$cond`, utilizzato per applicare restrizioni sui dati.

### `arr_avg($vals, $dec)`
Funzione per il calcolo della media dei valori contenuti in un array; ritorna un risultato con `$dec` cifre decimali.

### `arr_med($vals, $dec)`
Funzione per il calcolo della mediana dei valori contenuti in un array; ritorna un risultato con `$dec` cifre decimali.

### `cond_builder()`
Costruisce le condizioni per la restrizione dei risultati

### `get_general_stats($cond)`
Calcola le statistiche mostrate nella pagina principale della sezione statistica

### `misc_graph($cond)`
Ottiene i valori per i grafici a torta nella pagina principale della sezione statistica.

### `get_records($id, $cond)`
Funzione per ottenere i record di un test.

### `get_stats($idtest, $cond, $get_median)`
Ottiene le statistiche da mostrare per un test. Se non necessaria, la mediana non viene calcolata.

### graph_vals($id, $cond)
Ottiene i valori per disegnare un istogramma.

### `graph_prc($id, $cond)`
Ottiene i valori per disegnare il grafico dei percentili.

### `graph_multibox($id, $group, $cond)`
Funzione per ottenere i dati necessari a disegnare un box plot suddiviso per `$group`.

### `open_rvals_stmt($cond)`
Crea la query per ottenere i valori di due test.

### `calc_r($id1, $stat1, $id2, $stat2, $cond)`
Funzione per il calcolo del coefficiente di correlazione campionaria tra due test, dati i loro id, il numero, la media e la mediana.

### `get_test_correlation($cond)`
Ottiene la lista di test con un numero sufficiente di prove e le loro statistiche.

### `test_graph($testlist, $cond)`
Funzione per estrarre i valoriutili a disegnare la matrice di dispersione.

### `correlation_color($r)`
Ritorna un colore basato sul valore di correlazione `$r`.

## [stat_menu.js](stat_menu.js)
File JavaScript contenente funzioni relative al menu statistico, permette:
* Gestione dei bottoni e degli input del menu
* Costruzione delle condizioni per richieste ajax
* Controllo di coerenza degli anni
* Abilitazione e disabilitazione degli elementi che permettono richieste asincrone

## [ui/custom.css](ui/custom.css)
File di stile per il progetto
