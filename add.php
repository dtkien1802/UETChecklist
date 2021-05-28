<?php
include_once 'db.php';
if(isset($_POST['add']))
{    
    $code = $_POST['subjectCode'];
    $name = $_POST['subjectName'];
    $credit = $_POST['subjectCredit'];
    $presubject = $_POST['preSubject'];
    $subjectgroup = $_POST['subjectGroup'];
    
    if($presubject === "") {
        $sql = "INSERT INTO subject (code,name,credit,presubject,subjectgroup)
        VALUES ('$code','$name','$credit',NULL,'$subjectgroup')";
    } else {
        $sql = "INSERT INTO subject (code,name,credit,presubject,subjectgroup)
        VALUES ('$code','$name','$credit','$presubject','$subjectgroup')";
    }
    if (mysqli_query($conn, $sql)) {
        echo "Thêm môn học thành công !";
    } else {
        echo "Error: " . $sql . ":-" . mysqli_error($conn);
    }
}
?>