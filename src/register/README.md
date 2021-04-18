# File in /register/
Script utili a realizzare la sezione di registro.

## [register.php](register.php)
Pagina iniziale della sezione, mostra la lista di classi dell'utente (o tutte se amministratore).

## [class_add.php](class_add.php)
Pagina di creazione di una nuova classe. Permette di importare e promuovere una classe precedente.

## [class_promote_ajax.php](class_promote_ajax.php)
Script per inviare i dati di una classe in seguito a richieste ajax in `class_add.php`.

## [class_insert.php](class_insert.php)
Script backend per l'inserimento di una nuova classe; controlla l'unicità per sezione e anno all'interno della scuola dell'utente.

## [class_modify.php](class_modify.php)
Pagina frontend per la modifica di una classe.

## [class_update.php](class_update.php)
Script per l'aggiornamento di una classe modificata.

## [student_duplicate_ajax.php](student_duplicate_ajax.php)
Script di controllo asincrono per sapere se esistono già studenti con dati uguali ai nuovi inseriti in `class_add.php` o `class_modify.php` e proporli all'utente.

## [class_delete.php](class_delete.php)
Script backend di rimozione di una classe.

## [class_show.php](class_show.php)
Pagina frontend per mostrare il registro di una classe. Permette l'inserimento di nuovi test o la modifica di prove precedenti.

## [testlist_ajax.php](testlist_ajax.php)
Script per inviare la lista di test preferiti non ancora effettuati nella classe.

## [unit_ajax.php](unit_ajax.php)
Script asincrono per l'invio dell'unità di misura di un test.

## [result_check_ajax.php](result_check_ajax.php)
Script di controllo dei valori inseriti in `class_show.php`; impedisce l'aggiornamento in caso di valori non coerenti.

## [result_insert.php](result_insert.php)
Script per l'inserimento di nuove prove.

## [class_show_stat.php](class_show_stat.php)
Pagina frontend di visualizzazione dei dati elaborati di una classe. Permette la selezione di valori percentili, standard o voti su una popolazione modificabile con il menu statistico.

## [class_stat_ajax.php](class_stat_ajax.php)
Script backend per inviare in modo asincrono i dati aggiornati delle statistiche di una classe.

## [student_show.php](student_show.php)
Pagina di registro di uno studente; permette la visualizzazione delle prove divise per anno.

## [student_modify.php](student_modify.php)
Pagina per permettere a un'utente che possieda almeno una classe dello studente di modificarne i dati.

## [student_update.php](student_update.php)
Script di aggiornamento delle informazioni di uno studente.

## [student_show_stat.php](student_show_stat.php)
Pagina per visualizzare le statistiche delle prove associate a uno studente e un grafico radar per visualizzarne i risultati.

# File in /register/js/
File Javascript per la sezione di registro.

## [class_input.js](js/class_input.js)
Funzioni utilizzate in `class_add.php` e `class_modify.php`. Permettono di:
* Aggiornare l'anno scolastico su modifica dell'utente
* Mostrare il form di promozione
* Richiedere la classe e la lista di studenti da promuovere (tramite ajax)
* Aggiungere o rimuovere una riga per i nuovi studenti
* Richiedere tramite ajax se esistono studenti già nel sistema indicati come nuovi dall'utente

## [class_show.js](js/class_show.js)
Script per `class_show.php`; permettono di:
* Ottenere con richiesta ajax la lista di test da effettuare
* Ripristinare la pagina in caso di annullamento dell'input
* Ottenere l'unità di misura del test selezionato
* Abilitare la modifica delle prove già presenti
* Controllare l'input prima dell'inserimento

## [common_register.js](js/common_register.js)
Script utilizzati nelle pagine di registro per mostrare classi o studenti. Permette di:
* Mostare e nascondere le statistiche delle pagine
* Mostrare e nascondere le classi non dell'utente

## [show_stat.js](js/show_stat.js)
Funzioni utilizzate nelle pagine di registro con statistiche. Permette di:
* Mostrare i colori delle tabelle
* Ottenere valori e colori aggiornati su richiesta dell'utente (tramite richieste ajax)
* Aggiornare il grafico radar (se nella pagina degli studenti)

## [student_show_stat.js](js/student_show_stat.js)
Integrazione di `show_stats.js` per la pagina di registro degli studenti. Permette di disegnare il grafico radar.
