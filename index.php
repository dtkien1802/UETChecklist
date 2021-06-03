<?php

session_start();


// Create a list of subject group's name
$conn = mysqli_connect('localhost','root','','K64CCLC_CTDT') or die('Unable To connect');
$subjectGroupListsql = "SELECT * FROM subjectgroup";
$subjectGroupListquery = mysqli_query($conn, $subjectGroupListsql);

// Save subject changes
if(isset($_POST['save'])) {
    $savesql="SELECT code FROM subject";
    $save = mysqli_query($conn, $savesql);
    
    // Loop through all subject
    while($rows=mysqli_fetch_assoc($save)) {
        $email = $_SESSION['email'];
        
        // Check if subject already in db or not
        $checkSavesql="SELECT * FROM subjectfinished WHERE email = ? AND subject = ?";
        $checkSavestmt = $conn->prepare($checkSavesql);
        $checkSavestmt->bind_param("ss", $email, $rows['code']);
        $checkSavestmt->execute();
        $checkSave = $checkSavestmt->get_result();
        
        // If not in db and checked in page
        if(isset($_POST[$rows['code']]) && $checkSave->num_rows == 0) {
            $saveSubjectsql = "INSERT INTO subjectfinished VALUES(?, ?)";
            $saveSubjectstmt = $conn->prepare($saveSubjectsql);
            $saveSubjectstmt->bind_param("ss", $email, $rows['code']);
            $saveSubjectstmt->execute();
        } 
        // If in db and unchecked in page
        elseif (!isset($_POST[$rows['code']]) && $checkSave->num_rows == 1) {
            $saveSubjectsql = "DELETE FROM subjectfinished WHERE email = ? AND subject = ?";
            $saveSubjectstmt = $conn->prepare($saveSubjectsql);
            $saveSubjectstmt->bind_param("ss", $email, $rows['code']);
            $saveSubjectstmt->execute();
        }
    }
    header("Location:savesucess.php");
}

$subjectGroupList = [];
while($subjectGroup=mysqli_fetch_assoc($subjectGroupListquery)) {
    $new_arr = array($subjectGroup['name'], $subjectGroup['credit']);
    $subjectGroupList[] = $new_arr;
}
?>

<!--page-->
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>UETCheckList</title>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:400,400i,500,500i,700,700i,900,900i">
        <link rel="stylesheet" href="styles/reset.css">
        <link rel="stylesheet" href="-styles/debug.css">
        <link rel="stylesheet" href="styles/indexx.css">
        <link rel="stylesheet" href="styles/table.css">
        
        <script src="index.js"></script>
	</head>

    <body>
        <h1>UET</h1>
        <h1>Studying Process Tracker</h1>
        <p><?php 
        //Ignore warning
        error_reporting(E_ERROR | E_PARSE);
        if($_SESSION["email"]) {
            $email = $_SESSION['email'];
            echo "Chào ". $email. "
            <form action=\"account/logout.php\">
            <input class=\"btn\" type=\"submit\" value=\"Đăng xuất\">
            </form>
            ";
        } else {
            echo "Bạn chưa đăng nhập". $email. " 
            <form action=\"account/login.php\">
            <input class=\"btn\" type=\"submit\" value=\"Đăng nhập\">
            </form>
            ";
        }
         
        
        ?></a></p>
        <!--Display table of subjects-->
        <form action="" method="post">
            <table class="table table-bordered">
                <colgroup>
                    <col class="frcol"/>
                    <col class="secol"/>
                    <col class="thcol"/>
                    <col class="focol"/>
                    <col class="ficol"/>
                </colgroup>
                <thead>
                <tr>
                    <th>Mã học phần</th>
                    <th>Học phần</th>
                    <th>Số tín chỉ</th>
                    <th>Đã học</th>
                    <th>Học phần tiên quyết</th>
                </tr>
                </thead>

                <tbody>
                    <?php
                        //Loop through each subject group
                        foreach ($subjectGroupList as $subjectGroupName) {
                            
                    ?>

                    <!--Name of subject group-->
                    <tr>
                        <th colspan="5" class="subjectGroup"><?php echo $subjectGroupName[0]; ?></th>
                    </tr>

                    <?php
                        //List of subject name, each subject a row
                        $subjectListsql="SELECT * FROM subject WHERE subjectgroup = ?";
                        $subjectListstmt = $conn->prepare($subjectListsql);
                        $subjectListstmt->bind_param("s", $subjectGroupName[0]);
                        $subjectListstmt->execute();
                        $subjectList = $subjectListstmt->get_result();
                        $count = 0;
                        while($rows = $subjectList->fetch_assoc()) {
                    ?> 
                    
                    <!--rows of subject-->
                    <tr>
                        <td><?php echo $rows['code']; ?></td> 
                        <td><?php echo $rows['name']; ?></td> 
                        <td><?php echo $rows['credit']; ?></td> 
                        <td>
                            <?php
                                //Check if subject is finished or not (checked or not checked)                                  
                                $checkSubjectsql="SELECT * FROM subjectfinished WHERE email = ? AND subject = ?";
                                $checkSubjectstmt = $conn->prepare($checkSubjectsql);
                                $checkSubjectstmt->bind_param("ss", $email, $rows['code']);
                                $checkSubjectstmt->execute();
                                $checkSubjectresult = $checkSubjectstmt->get_result();
                                if ($checkSubjectresult->num_rows > 0) {
                                    $count += $rows['credit'];
                                    echo "<input class=\"cbox\" name=\"". $rows['code']. "\" type='checkbox' checked>";
                                } else {
                                    echo "<input class=\"cbox\" name=\"". $rows['code']. "\" type='checkbox'>";
                                }
                            ?>
                        </td>
                        <td><?php echo $rows['presubject']; ?></td>
                    </tr>

                    <?php 
                        }
                        echo "<tr><td><td></td></td><td>".$count. "/". $subjectGroupName[1]. "</td><td></td><td></td></tr>";
                        }
                    ?> 
                </tbody>
            </table>

            <!--Save changes-->
            <input type="submit" name="save" value="Lưu" class="btn">
        </form>
        <!--Add new subject-->
        <div class="wrapper" style="margin-top: 40px;">
            <h2>Thêm học phần</h2>
            <form action="http://localhost/test/UETchecklist/addSubject.php" method="post" autocomplete="off">
                <div class="form">
                    <label>Mã học phần</label>
                    <input name="subjectCode" class="text-box" required placeholder="Ví dụ: ABC_1234">
                </div>
                <div class="form">
                    <label>Học phần</label>
                    <input name="subjectName" class="text-box" required>
                </div>
                <div class="form">
                    <label>Số tín chỉ</label>
                    <input name="subjectCredit" class="text-box" required type="number" min="1" max="10">
                </div>
                <div class="form">
                    <label>Học phần tiên quyết</label>
                    <input name="preSubject" class="text-box" value="" placeholder="Ví dụ: ABC_1234/Để trống nếu không có">
                </div>
                <div class="form">
                    <label>Nhóm học phần</label>
                    <input list="subjectGroup" name="subjectGroup" class="text-box" required>
                    <datalist id="subjectGroup">
                        <?php
                            foreach ($subjectGroupList as $subjectGroupName) {
                                echo "<option value=\"". $subjectGroupName[0]. "\">";
                            }
                        ?>
                    </datalist>
                </div>
                <input type="submit" class="btn" name="add" value="Thêm">
            </form>
        </div>
    </body>
</html>