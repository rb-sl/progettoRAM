# File in /statistics/
In questa cartella sono presenti gli script che permettono di aggregare e visualizzare i risultati dei test presenti nel sistema, appoggiandosi in larga parte al file [lib_stat.php](../libraries/lib_stat.php)

Le pagine frontend di questa sezione sono dotate di menu statistico per ridurre la popolazione considerata nei calcoli, a cui sono associati script AJAX per richiedere al server, maniera asincrona, i dati aggiornati.

## [statistics.php](statistics.php)
Pagina iniziale della sezione di statistica. Permette di visualizzare alcuni dati generali del sistema (numero studenti, prove...) e di raggiungere le pagine relative alle statistiche per ogni test e di correlazione campionaria.

## [statistics_ajax.php](statistics_ajax.php)
Script utilizzato per rispondere alle query ajax della pagina `statistics.php`.

## [test_stats.php](test_stats.php)
Pagina frontend utilizzata per mostrare le statistiche di un test. Permette di disegnare i grafici per:
* Valori
* Box plot
    * Normale
    * Per anno
    * Per classe
    * Per sesso
* Valori percentili 

## [test_stats_ajax.php](test_stats_ajax.php)
Pagina utile a rispondere alle richieste asincrone di `test_stats.php`; calcola le statistiche e i valori dei grafici in base alle condizioni del menu statistico.

## [correlation.php](correlation.php)
Pagina per le statistiche di correlazione campionaria tra i test. Permette di mostrare la matrice degli indici r di correlazione la matrice dei diagrammi di dispersione. Cliccando su una casella della matrice viene generato e mostrato su un overlay il grafico di correlazione dei due test selezionati.

## [correlation_ajax.php](correlation_ajax.php)
Pagina per l'aggiornamento asincrono dei valori in `correlation.php`.

# File in /statistics/js/
Questa sottocartella contiene alcuni file JavaScript utili alle pagine della sezione.

## [statistics.js](js/statistics.js)
Contiene funzioni utili a `statistics.php` e permette di:
* Creare e aggiornare i grafici a torta
* Gestire le richieste ajax di aggiornamento dal menu e aggiornare i dati mostrati

## [test_stats.js](js/test_stats.js)
Contiene funzioni utili a `test_stats.php` e permette di:
* Gestire le richieste ajax di aggiornamento dal menu statistico o dal tipo di grafico
* Creare e aggiornare i grafici e i dati mostrati

## [correlation.js](js/correlation.js)
Contiene funzioni utili a `correlation.php` e permette di:
* Attivare o disattivare i colori della tabella
* Mostrare i grafici in overlay e le opzioni di uscita
* Creare e aggiornare la matrice dei grafici e la tabella dei coefficienti
* Gestire le richieste ajax di aggiornamento dal menu
