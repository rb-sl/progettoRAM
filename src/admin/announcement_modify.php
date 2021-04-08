<?php
// Front end page to modify the index's text
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
chk_access(ADMINISTRATOR);
connect();
show_premain("Modifica annuncio");

// Construction of the form based on the session
if(!isset($_SESSION['index_text']))
{
    $text_st = prepare_stmt("SELECT index_text FROM ADMINDATA");
    $ret = execute_stmt($text_st);
    $text_st->close();

    $row = $ret->fetch_assoc();
    $text = $row['index_text'];
    $chk = "";
}
else
{
    $text = $_SESSION['index_text'];
    if($_SESSION['important'])
        $chk = " checked";
    else
        $chk = "";
}   
?>

<h2>
    Annuncio in home page
    <a href="/admin/admin.php" class="btn btn-warning marginunder">Indietro</a>
</h2>

<form action="announcement_update.php" method="POST">
    <div class="containerflex">
        <div class="form-check">
            <input type="checkbox" id="important" class="form-check-input" name="important"<?=$chk?>>

            <label class="form-check-label" for="important">
                Importante
            </label>
        </div>
    </div>

<?php
if(isset($_SESSION['syntax_error']))
{
    echo "<div class='dangercolor'>".$_SESSION['syntax_error']."</div>";
    unset($_SESSION['syntax_error']);
}
?>
    
    <textarea class="txt" name="announcement"><?=$text?></textarea>
    <br>
    <input type="submit" class="btn btn-primary marginunder" value="Aggiorna">
</form>

<?php show_postmain(); ?>
