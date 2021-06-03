<?php
include_once 'db.php';
if(isset($_POST['add']))
{    
    $code = $_POST['subjectCode'];
    $name = $_POST['subjectName'];
    $credit = $_POST['subjectCredit'];
    $presubject = $_POST['preSubject'];
    $subjectgroup = $_POST['subjectGroup'];
    
    $checkSubjectAlreadyHavesql = mysqli_query($conn, "SELECT * FROM subject WHERE code = '". $code. "'");
    $row = mysqli_fetch_array($checkSubjectAlreadyHavesql);
    if(is_array($row)) {
        echo "Môn học đã tồn tại";
    }
    elseif($presubject === "") {
        $sql = "INSERT INTO subject (code,name,credit,presubject,subjectgroup)
        VALUES ('$code','$name','$credit',NULL,'$subjectgroup')";
        mysqli_query($conn, $sql);
        echo "Đã thêm môn học";
    } else {
        $sql = "INSERT INTO subject (code,name,credit,presubject,subjectgroup)
        VALUES ('$code','$name','$credit','$presubject','$subjectgroup')";
        mysqli_query($conn, $sql);
        echo "Đã thêm môn học";
    }
    
    //header("Location:index.php");
}
?>