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
                <th class="col">Cognome</th>
                <th class="col">Scuola</th>
                <th class="col">Ultimo accesso</th>
                <th class="col">Privilegi</th>
                <th class="col"></th>
            </tr>
<?php
$user_st = prepare_stmt("SELECT * FROM PROFESSORI 
    LEFT JOIN SCUOLE ON fk_scuola=id_scuola 
    ORDER BY priv, last_login DESC, user");
$ret = execute_stmt($user_st);
$user_st->close();

while($row = $ret->fetch_assoc())
{
    switch($row['priv'])
    {
        case 0: 
            $privilege = "Amministratore";
            $color = "primarybg";
            break;
        case 1: 
            $privilege = "Professore (modifica test)";
            $color = "infobg";
            break;
        case 2: 
            $privilege = "Professore";
            $color = "successbg";
            break;
        case 3: 
            $privilege = "Ricerca";
            $color = "warningbg";
            break;
        default:
            $privilege = "Nessuno";
            $color = "dangerbg";
            break;
    }

    echo "<tr>
            <td class='col'>".$row['user']."</td>
            <td class='col'>".$row['cogp']."</td>
            <td class='col'>".$row['nomescuola']."</td>
            <td class='col'>".$row['last_login']."</td>
            <td class='col'>
                <div class='boxdiv'><div class='colorbox $color'></div></div>
                <div class='privdiv'>$privilege</div>
            </td>
            <td class='col'>";

    if($row['id_prof'] != $_SESSION['id'])
        echo "<a href='user_details.php?id=".$row['id_prof']."' class='btn btn-info'>Dettagli</a>";
            
    echo "</td>
        </tr>";
}
?>
        </table>
    </div>
</div>

<?php show_postmain(); ?>
