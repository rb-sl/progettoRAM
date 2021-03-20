<?php
// Frontend page to show a user's details and change their permission
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
chk_access(0);
connect();

$user_st = prepare_stmt("SELECT * FROM PROFESSORI 
    LEFT JOIN SCUOLE ON fk_scuola=id_scuola 
    WHERE id_prof=?");
$user_st->bind_param("i", $_GET['id']);
$ret = execute_stmt($user_st);
$user_st->close();

$user = $ret->fetch_assoc();

$l0 = "";
$l1 = "";
$l2 = "";
$l3 = "";
$l5 = "";
switch($user['priv'])
{
    case 0:
        $l0 = " selected";
        break;
    case 1:
        $l1 = " selected";
        break;
    case 2:
        $l2 = " selected";
        break;
    case 3:
        $l3 = " selected";
        break;
    default:
        $l5 = " selected";
        break;    
}

show_premain("Profilo di ".$user['user']);
?>

<h2>
    Profilo di <?=$user['user']?>
    <a href="/admin/users.php" class="btn btn-warning">Indietro</a>
</h2>

<form action="user_update.php?id=<?=$_GET['id']?>" method="POST" class="tdiv">
    <div class="inner">
        <table class="table table-striped marginunder">
            <tr>
                <td class="col">Cognome</td>
                <td class="col"><?=$user['cogp']?></td>
            </tr>
            <tr>
                <td class="col">Nome</td>
                <td class="col"><?=$user['nomp']?></td>
            </tr>
            <tr>
                <td class="col">E-mail</td>
                <td class="col"><?=$user['email']?></td>
            </tr>
            <tr>
                <td class="col">Scuola</td>
                <td class="col"><?=$user['nomescuola']?></td>
            </tr>
            <tr>
                <td class="col">Privilegi</td>
                <td class="col">
                    <select class="form-control" name="priv">
                        <option value="0"<?=$l0?>>Amministratore</option>
                        <option value="1"<?=$l1?>>Professore (Con modifica test)</option>
                        <option value="2"<?=$l2?>>Professore</option>
                        <option value="3"<?=$l3?>>Ricerca</option>
                        <option value="5"<?=$l5?>>Nessuno</option>
                    </select>
                </td>
            </tr>
        </table>
        <input type="submit" class="btn btn-primary" value="Aggiorna privilegi">
    </div>
</form>

<?php show_postmain(); ?>