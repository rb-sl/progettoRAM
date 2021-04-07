<?php 
// Page used to display the project's description
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
connect();
show_premain("Il progetto");

$text_st = prepare_stmt("SELECT project_compiled FROM ADMINDATA");
$ret = execute_stmt($text_st);
$text_st->close();

$row = $ret->fetch_assoc();
?>

<div class="textwall">	
	<h2>Il Progetto RAM</h2>
	<?=$row['project_compiled']?>
</div>

<?php show_postmain(); ?>
