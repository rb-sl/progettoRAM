<?php
// Frontend page to show a user's details and change their permission
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
include $_SERVER['DOCUMENT_ROOT']."/libraries/lib_admin.php";
chk_access(0);
connect();

$user_st = prepare_stmt("SELECT * FROM PROFESSORI 
    LEFT JOIN SCUOLE ON fk_scuola=id_scuola 
    WHERE id_prof=?");
$user_st->bind_param("i", $_GET['id']);
$ret = execute_stmt($user_st);
$user_st->close();

$user = $ret->fetch_assoc();

$dg = can_downgrade($user['id_prof']);

show_premain("Profilo di ".$user['user']);
?>

<h2>
    Profilo di <?=$user['user']?>
    <a href="/admin/user/users.php" class="btn btn-warning">Indietro</a>
</h2>

<form action="user_update.php?id=<?=$_GET['id']?>" method="POST" class="tdiv">
    <div class="inner">
        <table class="table table-light table-striped marginunder">
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
<?php
// Options to change the privilege level are shown only in 
// upgrade if the user is not the original granter
$end = $dg ? NONE : $user['priv'];
for($i = 0; $i <= $end; $i++)
{
    $priv = get_privilege($i);
    if($priv != null)
    {
        echo "<option value='$i'";
        if($user['priv'] == $i)
            echo " selected";
        echo ">".$priv['text']."</option>";
    }
}
?>
                    </select>
                </td>
            </tr>
        </table>
        <input type="submit" class="btn btn-primary" value="Aggiorna privilegi">
    </div>
</form>

<?php show_postmain(); ?>
