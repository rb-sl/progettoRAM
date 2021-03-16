<?php
// Front end page to show the application's logs
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
chk_access(0);
connect();
show_premain("Log dell'applicazione");
?>

<h2>Log di utilizzo</h2>

<div class="logdiv">
	<div class="loglist">
<?php
$cont = array_diff(scandir(LOG_PATH, SCANDIR_SORT_DESCENDING), array("..", "."));
$i = 0;
foreach($cont as $g)
{
	echo "<span id='sp$i' class='splog'>$g<br></span>";
	$i++;
}
?>
	</div>
  
	<div class="logcontainer">
		<textarea id="txt" class="logtxt"></textarea><br>
		<button id="del" class="btn btn-danger delbutton" disabled>Elimina</button>
	</div>
</div>

<script src="log.js"></script>

<?php show_postmain(); ?>