<?php
include_once 'db.php';
include_once 'index.php';
$query="SELECT code FROM subject";
$result = mysqli_query($conn, $query);
//session_start();
if(isset($_POST['save']))
{    
    while($rows=mysqli_fetch_assoc($result)) {
        
        $email = $_SESSION['email'];
        $query2="SELECT * FROM subjectfinished WHERE email = ? AND subject = ?";
        $stmt = $conn->prepare($query2);
        $stmt->bind_param("ss", $email, $rows['code']);
        $stmt->execute();
        $result1 = $stmt->get_result();
        if(isset($_POST[$rows['code']]) && $result1->num_rows == 0) {
            $sql = "INSERT INTO subjectfinished VALUES(?, ?)";
            $stmt2 = $conn->prepare($sql);
            $stmt2->bind_param("ss", $email, $rows['code']);
            $stmt2->execute();
        } elseif (!isset($_POST[$rows['code']]) && $result1->num_rows == 1) {
            $sql = "DELETE FROM subjectfinished WHERE email = ? AND subject = ?";
            $stmt2 = $conn->prepare($sql);
            $stmt2->bind_param("ss", $email, $rows['code']);
            $stmt2->execute();
            
        }
        /*if(!isset($_POST[$rows['code']]) && $result1->num_rows == 0){
            echo "ok";
            echo "<br>";
        }
        if(isset($_POST[$rows['code']]) && $result1->num_rows == 0){
            echo "ok2";
            echo "<br>";
        }*/
    }
    

    //header( 'Location: http://localhost/test/UETchecklist/login/loginsuccess.html' );
    //exit();
    //$email = $_SESSION['email'];
    //echo $email;
    //mysqli_close($conn);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<a href='http://localhost/test/UETchecklist/index.php'>Tải lại</a>
</body>
</html>