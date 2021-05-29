<?php
include_once 'db.php';
include_once 'login.php';

session_start();
$email = $_SESSION['email'];

// Create a list of subject group's name
$subjectGroupListsql = "SELECT name FROM subjectgroup";
$subjectGroupListquery = mysqli_query($conn, $subjectGroupListsql);
$subjectGroupList = [];
while($subjectGroup=mysqli_fetch_assoc($subjectGroupListquery)) {
    $subjectGroupList[] = $subjectGroup['name'];
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
        <link rel="stylesheet" href="styles/-debug.css">
        <link rel="stylesheet" href="styles/table.css">
        <link rel="stylesheet" href="styles/index.css">
        <script src="index.js"></script>
	</head>

    <body>
        <!--Display table of subjects-->
        <form action="http://localhost/test/UETchecklist/save.php" method="post">
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
                        <th colspan="5" id="group"><?php echo $subjectGroupName; ?></th>
                    </tr>

                    <?php
                        //List of subject name, each subject a row
                        $subjectListsql="SELECT * FROM subject WHERE subjectgroup = ?";
                        $subjectListstmt = $conn->prepare($subjectListsql);
                        $subjectListstmt->bind_param("s", $subjectGroupName);
                        $subjectListstmt->execute();
                        $subjectList = $subjectListstmt->get_result();
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
                                    echo "<input name=\"". $rows['code']. "\" type='checkbox' checked>";
                                } else {
                                    echo "<input name=\"". $rows['code']. "\" type='checkbox'>";
                                }
                            ?>
                        </td>
                        <td><?php echo $rows['presubject']; ?></td>
                    </tr>

                    <?php 
                        }
                        }
                    ?> 
                    
                </tbody>
            </table>

            <!--Save changes-->
            <input type="submit" class="btn btn-primary" name="save" value="Lưu">
        </form>
        <!--Add new subject-->
        <form action="http://localhost/test/UETchecklist/add.php" method="post">
            <div class="form-group">
                <label>Mã học phần</label>
                <input name="subjectCode" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Học phần</label>
                <input name="subjectName" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Số tín chỉ</label>
                <input name="subjectCredit" class="form-control" required type="number" min="1" max="10">
            </div>
            <div class="form-group">
                <label>Học phần tiên quyết</label>
                <input name="preSubject" class="form-control" value="" required>
            </div>
            <div class="form-group">
                <label>Nhóm học phần</label>
                <input list="subjectGroup" name="subjectGroup" class="form-control" required>
                <datalist id="subjectGroup">
                    <?php
                        foreach ($subjectGroupList as $subjectGroupName) {
                            echo "<option value=\"". $subjectGroupName. "\">";
                        }
                    ?>
                </datalist>
            </div>
            <input type="submit" class="btn btn-primary" name="add" value="Thêm">
        </form>
    </body>
</html>