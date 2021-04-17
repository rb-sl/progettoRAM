<?php
// Backend script to add a new user
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
chk_access(ADMINISTRATOR);
connect();

$in_st = prepare_stmt("INSERT INTO PROFESSORI(user, psw, priv, granted_by, nomp, cogp, email, fk_scuola) 
    VALUES (?, MD5(?), ?, ?, ?, ?, ?, ?)");
$in_st->bind_param("ssiisssi", $_POST['usr'], $_POST['psw'], $_POST['priv'], $_SESSION['id'], 
    $_POST['nom'], $_POST['cog'], $_POST['mail'], $_POST['sc']);
execute_stmt($in_st);
$in_st->close();
$id = $mysqli->insert_id;

// The default grades of the new user are equal to the ones of the administrator
$val_st = prepare_stmt("INSERT INTO VALUTAZIONI(fk_prof, fk_voto, perc) 
    SELECT ? AS fk_prof, fk_voto, perc
    FROM VALUTAZIONI
    WHERE fk_prof=?");
$val_st->bind_param("ii", $id, $_SESSION['id']);
execute_stmt($val_st);
$val_st->close();

// Adds all available tests to favourites
$fav_st = prepare_stmt("INSERT INTO PROF_TEST(fk_prof, fk_test) 
    SELECT ? AS fk_prof, id_test
    FROM TEST");
$fav_st->bind_param("i", $id);
execute_stmt($fav_st);
$fav_st->close();

$_SESSION['alert'] = "Utente inserito correttamente";
header("Location: /admin/user/users.php");
?>
