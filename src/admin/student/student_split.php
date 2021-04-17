<?php
// Back end page to split student instances
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
chk_access(ADMINISTRATOR);
connect();

$stud1 = $_POST['stud0'];

// Creation of the new student
$in_st = prepare_stmt("INSERT INTO STUDENTI(noms, cogs, sesso)  
	SELECT noms, cogs, sesso FROM STUDENTI WHERE id_stud=?");
$in_st->bind_param("i", $stud1);
execute_stmt($in_st);
$in_st->close();

$stud2 = $mysqli->insert_id;

// Update for instances to be moved to the new student
$up_st = prepare_stmt("UPDATE ISTANZE SET fk_stud=? WHERE fk_stud=? AND fk_cl=?");
$up_st->bind_param("iii", $stud2, $stud1, $class);

foreach($_POST['split'] as $class => $val)
	if($val == 2)
		execute_stmt($up_st);

$up_st->close();
		
writelog("Studente $stud1 separato in $stud1 e $stud2");
$_SESSION['alert'] = "Studente $stud1 separato in $stud1 e $stud2";
header("Location: /admin/student/student_correction.php");
?>
