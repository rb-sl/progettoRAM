<?php
include $_SERVER['DOCUMENT_ROOT']."/librerie/general.php";
chk_access(0);
show_premain();
?>

<h2>Strumenti amministrativi</h2>

<p>
  <a href='/adm/add_prof.php' class='btn btn-primary btn-warning' style="margin-bottom:10px">Aggiungi utente</a><br>
  <a href='/adm/logger.php' class='btn btn-primary btn-warning' style="margin-bottom:10px">Logger</a><br>
  <a href='/adm/ip.php' class='btn btn-primary btn-warning'>Controllo accessi</a>
</p>

<?php show_postmain(); ?>