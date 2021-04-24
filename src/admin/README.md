# File in /admin/
Insieme di file utili a svolgere funzioni amministrative.

## [admin.php](admin.php)
Pagina che contiene i link ai diversi strumenti amministrativi.

## [log.php](log.php)
Pagina per mostrare i log dell'applicazione all'utente; mostra la lista di file all'interno della cartella selezionata per i log.

## [log_reader.php](log_reader.php)
Script per rispondere alla richiesta ajax di un file di log.

## [announcement_modify.php](announcement_modify.php)
Pagina per l'inserimento di un annuncio da mostrare in `index.php`. Permette l'uso dei simboli di markup.

## [announcement_update.php](announcement_update.php)
Script di aggiornamento del messaggio in home. Traduce dal linguaggio di markup a HTML e salva nel database.

## [project_modify.php](project_modify.php)
Pagina analoga a `announcement_modify.php` per la pagina `project.php`.

## [project_update.php](project_update.php)
Aggiorna e salva nel DB il testo del progetto in HTML.

# File in /admin/student/
Strumenti per correggere i profili degli studenti.

## [student_correction.php](student/student_correction.php)
Pagina di ricerca e modifica dei profili degli studenti; permette di unire due profili che appartengono allo stesso studente o dividere un profilo che appartiene a più studenti.

## [student_info_ajax.php](student/student_info_ajax.php)
Script per ottenere in modo asincrono le informazioni da mostrare in `student_correction.php`.

## [student_merge.php](student/student_merge.php)
Script backend per l'unione di due profili.

## [student_split.php](student/student_split.php)
Script backend per la separazione di un profilo in due.

# File in /admin/test/
Strumenti per modificare le informazioni relative ai test nel sistema. Le pagine funzionano in modo analogo per unità, tipo dei dati e classe dei test e si suddividono nei seguenti tipi.

## File di modifica
Le pagine [unit.php](test/unit.php), [testtype.php](test/testtype.php) e [datatype.php](test/datatype.php) mostrano una tabella in cui è possibile aggiungere, eliminare o modificare un elemento del rispettivo tipo.

## File di aggiornamento
Gli script backend [unit_update.php](test/unit_update.php), [testtype_update.php](test/testtype_update.php) e [datatype_update.php](test/datatype_update.php) permettono di salvare le modifiche apportate nelle rispettive pagine di modifica.

## File di eliminazione
Gli script backend [unit_delete.php](test/unit_delete.php), [testtype_delete.php](test/testtype_delete.php) e [datatype_delete.php](test/datatype_delete.php) servono a rimuovere dal database un elemento delle rispettive tabelle.

# File in /admin/user/
Strumenti per la visualizzazione degli utenti e la gestione dei permessi.

## [users.php](user/users.php)
Pagina per la visualizzazione degli utenti dell'applicazione, ordinati per privilegio e ultimo accesso.

## [user_add.php](user/user_add.php)
Pagina per l'aggiunta di un nuovo utente.

## [user_insert.php](user/user_insert.php)
Script backend per l'inserimento di un nuovo utente, della sua tabella di valutazione (analoga a quella dell'utente che effettua l'inserimento) e dei test preferiti (di default tutti quelli presenti nel sistema).

## [user_details.php](user/user_details.php)
Pagina per la visualizzazione dei dati di un utente e modifica dei suoi permessi

## [user_update.php](user/user_update.php)
Pagina di aggiornamento dei privilegi di un utente, controlla che l'utente attivo sia superiore in gerarchia all'utente da modificare se i privilegi vengono abbassati.

# File in /admin/js/
Insieme di file JavaScript per getsire la sezione amministrativa.

## [log.js](js/log.js)
File utile a richiedere i log per `log.php`.

## [student.js](js/student.js)
File utilizzato in `student_correction.php`; permette di:
* Gestire le scelte dell'utente per il metodo di ricerca
* Cercare gli studenti da unire o modificare tramite richiesta ajax
* Mostrare i dati degli studenti
* Impedire la selezione dello stesso studente in unione
* Ripulire i form

## [tables.js](js/tables.js)
File contenente funzioni per i file di modifica in `/admin/test/`.





