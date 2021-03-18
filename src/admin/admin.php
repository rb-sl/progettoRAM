<?php
// Front end page to display administrative functions
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
chk_access(0);
show_premain();
?>

<h2>Strumenti amministrativi</h2>

<p>
	<a href="/admin/users.php" class="btn btn-primary btn-primary marginunder">Gestione utenti</a><br>
	<a href="/admin/log.php" class="btn btn-primary btn-warning marginunder">Log di utilizzo</a><br>
	<a href="/admin/student_correction.php" class="btn btn-primary btn-warning marginunder">Correggi istanze di studenti</a>
</p>

<?php show_postmain(); ?>