<?php
include_once 'db.php';
if(isset($_POST['submit']))
{    
     $email = $_POST['email'];
     $password = $_POST['password'];
     $sql = "SELECT password FROM accounts WHERE email = ?";

     $stmt = $conn->prepare($sql);
     $stmt->bind_param("s", $email);
     $stmt->execute();
     $result = $stmt->get_result();

       if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                 if($password == $row["password"]) {
                    session_start();
                      $_SESSION['email'] = $email;
                    header( 'Location: http://localhost/test/UETchecklist/login/loginsuccess.html' );
                    exit();
                 } else {
                    header( 'Location: http://localhost/test/UETchecklist/login/loginfail.html' );
                    exit();
                 }
                 echo $row["password"]. "<br>";
                 echo $password. "<br>";
                 
            }
       } else {
            echo "0 result";
       }
       
     mysqli_close($conn);
}
?>