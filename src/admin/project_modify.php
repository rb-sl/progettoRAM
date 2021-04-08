<?php
// Front end page to modify the application's description
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
chk_access(ADMINISTRATOR);
connect();
show_premain("Modifica descrizione");

// Construction of the form based on the session
if(!isset($_SESSION['project_text']))
{
    $text_st = prepare_stmt("SELECT project_text FROM ADMINDATA");
    $ret = execute_stmt($text_st);
    $text_st->close();

    $row = $ret->fetch_assoc();
    $text = $row['project_text'];
}
else
    $text = $_SESSION['project_text'];
?>

<form action="project_update.php" method="POST">
    <h2>
        Descrizione nella pagina Il progetto
        <a href="/admin/admin.php" class="btn btn-warning marginunder">Indietro</a>
        <input type="submit" class="btn btn-primary marginunder" value="Aggiorna">
    </h2>

<?php
if(isset($_SESSION['syntax_error']))
{
    echo "<div class='dangercolor'>".$_SESSION['syntax_error']."</div>";
    unset($_SESSION['syntax_error']);
}
?>
    
    <textarea class="bigtextarea" name="project"><?=$text?></textarea>
</form>

<?php show_postmain(); ?>
