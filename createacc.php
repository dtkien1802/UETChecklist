<?php
include_once 'db.php';
if(isset($_POST['submit']))
{    
   $name = $_POST['name'];
   $email = $_POST['email'];
   $password = $_POST['password'];
   $password2 = $_POST['password2'];
   
   //Check already have acc
   $checkAccsql = "SELECT * FROM accounts WHERE email = ?";
   $checkAccstmt = $conn->prepare($checkAccsql);
   $checkAccstmt->bind_param("s", $email);
   $checkAccstmt->execute();
   $checkAcc = $checkAccstmt->get_result();
   if ($checkAcc->num_rows > 0) {
      header( 'Location: http://127.0.0.1:5500/login/accalreadyhave.html' );
      exit();
   }
   //Check 2 password match
   elseif ($password != $password2) {
      header( 'Location: http://127.0.0.1:5500/login/passwordnotmatch.html' );
      exit();
   }
   else {
      $sql = "INSERT INTO accounts (name,email,password)
      VALUES ('$name','$email','$password')";
      if (mysqli_query($conn, $sql)) {
         echo "Tao tai khoan thanh cong !";
      } else {
         echo "Error: " . $sql . ":-" . mysqli_error($conn);
      }

      $sql2 = "INSERT INTO subjectfinished (email,subject)
      VALUES ('$email',NULL)";
      if (mysqli_query($conn, $sql2)) {
         echo "Tao record thanh cong !";
         header( 'Location: http://127.0.0.1:5500/login/createaccsuccess.html' );
         exit();
      } else {
         echo "Error: " . $sql2 . ":-" . mysqli_error($conn);
      }
   }
     mysqli_close($conn);
}
?>