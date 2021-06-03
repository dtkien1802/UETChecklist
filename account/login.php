<?php
    session_start();
    $message="";
    if(count($_POST)>0) {
        $con = mysqli_connect('localhost','root','','K64CCLC_CTDT') or die('Unable To connect');
        $result = mysqli_query($con,"SELECT * FROM accounts WHERE email='". $_POST["email"]. "' and password = '" . $_POST["password"]. "'");
        $row  = mysqli_fetch_array($result);
        if(is_array($row)) {
        $_SESSION["email"] = $row['email'];
        $_SESSION["name"] = $row['name'];
        } else {
         $message = "Sai Email hoặc Mật khẩu! ";
        }
    }
    if(isset($_SESSION["email"])) {
    header("Location:../index.php");
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập</title>
    <link rel="stylesheet" href="-../styles/debug.css">
    <link rel="stylesheet" href="../styles/accform.css">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>
<body>
    <h1>UET</h1>
    <h1>Studying Process Tracker</h1>
    <div class="wrapper">
        <h2>Đăng nhập</h2>
        <p>Điền thông tin tài khoản</p>
        
        <form method="post" action="">
            
            <div class="form">
                <label for="email">Email</label>
                <input class="text-box" type="email" name="email" id="email" required>
            </div>
            <div class="form">
                <label for = "password">Mật khẩu</label>
                <input class="text-box" type="password" name="password" id="password" required>
            </div>
            <div class="message"><?php if($message!="") { echo $message; } ?></div>
            <input class="btn" type="submit" name="submit" value="Đăng nhập">
        </form>
        
        <form action="createAccount/createacc.html">
            <input class="btn" type="submit" value="Tạo tài khoản">
        </form>
    </div>
</body>
</html>