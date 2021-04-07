<?php 
// Script to insert test results
include $_SERVER['DOCUMENT_ROOT']."/libraries/general.php";
include $_SERVER['DOCUMENT_ROOT']."/libraries/lib_reg.php";
chk_access(PROFESSOR);
connect();

// Results insert or update
$in_st = prepare_stmt("INSERT INTO PROVE(fk_test, fk_ist, valore, data) 
    VALUES(?, ?, ?, CURDATE()) ON DUPLICATE KEY UPDATE valore=?, data=CURDATE()");
$in_st->bind_param("iidd", $test, $instance, $value, $value);

// Deletion of empty results
$del_st = prepare_stmt("DELETE FROM PROVE WHERE fk_test=? AND fk_ist=?");
$del_st->bind_param("ii", $test, $instance);

$insert = 0;
$modify = 0;
$delete = 0;

// New test's results insert
if(isset($_POST['ntest']))
{
    $test = $_POST['test'];
    foreach($_POST['ntest'] as $instance => $value)
        if($value)
        {
            execute_stmt($in_st);
            $insert++;
        }
}

// Old results updates - if a value is empty it is deleted
if(isset($_POST['pr']))
    foreach($_POST['pr'] as $test => $s)
        foreach($s as $instance => $value)
            if(is_numeric($value))
            {
                execute_stmt($in_st);
                $modify++;
            }
            else
            {
                execute_stmt($del_st);
                $delete++;
            }

$in_st->close();
$del_st->close();

writelog("[prove] [classe: ".$_GET['cl']."] Prove da nuovo test: $insert; Prove modificate: $modify; Prove cancellate: $delete");

header("Location: ".$_SERVER['HTTP_REFERER']);
?>
