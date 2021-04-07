<?php
// Page used to display and modify a user's favourite tests
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
chk_access(PROFESSOR);
connect();
show_premain("Modifica test preferiti");
?>

<h2>Modifica test preferiti</h2>

<form id="frm" method="POST" action="favourites_update.php" class="textleft containerflex flexform">
    <input type="submit" id="submit" class="btn btn-primary" value="Aggiorna" disabled> 
    <div class="fullwidth">
<?php
$test_st = prepare_stmt("SELECT id_test, nometest, posiz, equip, 
    esec, cons, limite, valut, fk_test AS favourite FROM TEST
    LEFT JOIN PROF_TEST ON fk_test=id_test
    AND (fk_prof=? OR fk_prof IS NULL)
    ORDER BY nometest");
$test_st->bind_param("i", $_SESSION['id']);
$rettest = execute_stmt($test_st);
$test_st->close();

while($rowt = $rettest->fetch_assoc())
{
	if(!$rowt['favourite'])
	{
        $chk = "";
        $class = " inactivetext";
        $btn = "btn-secondary";
    }
    else
	{
        $chk = " checked";
        $class = "";
        $btn = "btn-primary";
    }
	echo "<div class='testcard'>
            <h3 id='h".$rowt['id_test']."' class='card-header testrow'> 
                <div class='form-check'>
                    <input type='checkbox' id='fav".$rowt['id_test']."' 
                        class='form-check-input chkfav' name='fav[]' value='".$rowt['id_test']."'$chk>

                    <label id='lbl".$rowt['id_test']."' class='form-check-label$class' for='fav".$rowt['id_test']."'>
                        ".$rowt['nometest']."
                    </label>
                </div>
                <button type='button' id='btn".$rowt['id_test']."' 
                    class='btn $btn' data-bs-toggle='collapse' data-bs-target='#coll".$rowt['id_test']."' 
                    aria-expanded='false' aria-controls='#coll".$rowt['id_test']."'>Mostra informazioni</button>
            </h3>

            <div id='coll".$rowt['id_test']."' class='collapse textcenter'>
                <div class='card card-body'>
                    <h4><b>Posizione</b></h4>
                    <p>".($rowt['posiz'] == "" ? "-" : str_replace("\n", "<br>", $rowt['posiz']))."</p>
                    <h4><b>Equipaggiamento</b></h4>
                    <p>".($rowt['equip'] == "" ? "-" : str_replace("\n", "<br>", $rowt['equip']))."</p>
                    <h4><b>Esecuzione</b></h4>
                    <p>".($rowt['esec'] == "" ? "-" : str_replace("\n", "<br>", $rowt['esec']))."</p>
                    <h4><b>Consigli</b></h4>
                    <p>".($rowt['cons'] == "" ? "-" : str_replace("\n", "<br>", $rowt['cons']))."</p>
                    <h4><b>Limite</b></h4>
                    <p>".($rowt['limite'] == "" ? "-" : str_replace("\n", "<br>", $rowt['limite']))."</p>
                    <h4><b>Valutazione</b></h4>
                    <p>".($rowt['valut'] == "" ? "-" : str_replace("\n", "<br>", $rowt['valut']))."</p>
                </div>
            </div>

        </div>";
}
?>
    </div>

</form>

<script src="/test/js/favourites.js"></script>

<?php show_postmain(); ?>
