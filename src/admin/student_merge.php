<?php
// Back end page to merge student instances
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
chk_access(ADMINISTRATOR);
connect();

if($_POST['keep'] == 1)
{
    $keepid = $_POST['merge1'];
    $delid = $_POST['merge2'];
}
else
{
    $keepid = $_POST['merge2'];
    $delid = $_POST['merge1'];
}

// Instances are moved to the student to be kept, while the other
// will be deleted by the trigger
$up_st = prepare_stmt("UPDATE ISTANZE SET fk_stud=? WHERE fk_stud=?");
$up_st->bind_param("ii", $keepid, $delid);
execute_stmt($up_st);
$up_st->close();

writelog("Studenti $keepid e $delid uniti in $keepid");
$_SESSION['alert'] = "Studenti $keepid e $delid uniti in $keepid";
header("Location: /admin/student_correction.php");
?>
