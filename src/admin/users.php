<?php
// Page to display and manage users
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
chk_access(0);
connect();

show_premain("Gestione utenti");
?>

<h2>Utenti dell'applicazione</h2>

<div>
    <a href="user_add.php" class="btn btn-primary marginunder">Aggiungi nuovo</a>
</div>

<div class="tdiv">
    <div class="inner">
        <table class="table table-striped">
            <tr>
                <th class="col">Username</th>
                <th class="col">Nome</th>
                <th class="col">Cognome</th>
                <th class="col">E-mail</th>
                <th class="col">Scuola</th>
                <th class="col">Ultimo accesso</th>
                <th class="col">Privilegi</th>
            </tr>
<?php
$user_st = prepare_stmt("SELECT * FROM PROFESSORI LEFT JOIN SCUOLE ON fk_scuola=id_scuola ORDER BY priv, last_login DESC, user");
$ret = execute_stmt($user_st);
$user_st->close();

while($row = $ret->fetch_assoc())
{
    echo "<tr>
            <td class='col'>".$row['user']."</td>
            <td class='col'>".$row['nomp']."</td>
            <td class='col'>".$row['cogp']."</td>
            <td class='col'>".$row['email']."</td>
            <td class='col'>".$row['nomescuola']."</td>
            <td class='col'>".$row['last_login']."</td>
            <td class='col'>";
    switch($row['priv'])
    {
        case 0: 
            echo "Amministratore";
            break;
        case 1: 
            echo "Professore (modifica test)";
            break;
        case 2: 
            echo "Professore";
            break;
        case 3: 
            echo "Ricerca";
            break;
    }
    echo "</td>
        </tr>";
}
?>
        </table>
    </div>
</div>

<?php show_postmain(); ?>
