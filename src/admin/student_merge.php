<?php
// Back end page to merge student instances
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
chk_access(ADMINISTRATOR);
connect();

if($_POST['keep'] == 1)
{
    $keepid = $_POST['stud1'];
    $delid = $_POST['stud2'];
}
else
{
    $keepid = $_POST['stud2'];
    $delid = $_POST['stud1'];
}

// Instances are moved to the student to be kept, while the other
// will be deleted by the trigger
$up_st = prepare_stmt("UPDATE ISTANZE SET fk_stud=? WHERE fk_stud=?");
$up_st->bind_param("ii", $keepid, $delid);
execute_stmt($up_st);

// If different classes for the same year are specified an error
// is returned by a trigger
if($up_st->error)
    $_SESSION['alert'] = $up_st->error;
else
{
    writelog("Studenti $keepid e $delid uniti in $keepid");
    $_SESSION['alert'] = "Studenti $keepid e $delid uniti in $keepid";
}

$up_st->close();

header("Location: /admin/student_correction.php");
?>
