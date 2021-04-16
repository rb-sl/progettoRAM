# File in /test/
Questa cartella contiene i file che formano la sezione di statistica.

## [test.php](test.php)
Pagina iniziale della sezione di gestione dei test. La prima parte della pagina permette di visualizzare un elenco dei test nel sistema e raggiungere le rispettive pagine; se alcuni test non sono nella lista di preferiti dell'utente vengono mostrati più chiari.

La seconda parte della pagina permette di visualizzare e modificare la tabella di valutazione dell'utente (o anche degli altri se l'utente è amministratore) in base ai valori percentili delle prove.

## [grades_ajax.php](grades_ajax.php)
Script utile agli amministratori per ottenere i voti degli altri utenti.

## [grades_update.php](grades_update.php)
Funzione per l'aggiornamento della tabella di valutazione di un utente.

## [test_show.php](test_show.php)
Pagina frontend che permette la visualizzazione dei parametri di un test e dà indicazioni ai docenti su come svolgerli.

## [test_add.php](test_add.php)
Pagina frontend che permette ad un utente con sufficienti privilegi di aggiungere un nuovo test.

## [test_insert.php](test_insert.php)
Pagina di backend per l'inserimento di un nuovo test.

## [test_modify.php](test_modify.php)
Pagina frontend, analoga a `test_add.php`, che permette ad un utente con sufficienti privilegi di aggiungere un nuovo test.

## [test_update.php](test_update.php)
Pagina di backend per l'aggiornamento di un test.

## [test_delete.php](test_delete.php)
Script backend per la cancellazione di un test (abilitata solo se non ci sono prove associate).

## [favourites_modify.php](favourites_modify.php)
Pagina frontend che permette agli utenti di selezionare quali test sono di loro interesse (e di visualizzarne la descrizione) che saranno i soli proposti nelle pagine di registro.

## [favourites_update.php](favourites_update.php)
Funzione di aggiornamento dei test preferiti nel database.

# File in /test/js/
Sottocartella di file JavaScript per le pagine di test.

## [test.js](js/test.js)
Script utilizzati in test.php, permettono di:
* Impredire l'aggiornamento dei voti se i percentili non sommano a 100
* Scegliere l'utente di cui visualizzare i voti (solo amministratore)
* Creare il grafico dei voti
* Aggiornare grafico e tabella quando viene modificato un percentile

## [favourites.js](js/favourites.js)
Script utilizzato in favourites.php che permette di attivare o disattivare i test in base alla selezione
