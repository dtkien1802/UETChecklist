<!--hello-->
<!--hello-->
<!--hello-->
<!--hello-->
<!--hello-->
<!--hello-->
<!--hello-->
<!--hello-->
<?php
include_once 'db.php';
include_once 'login.php';
$query="SELECT * FROM subject";
//a
$result = mysqli_query($conn, $query);
session_start();
$email = $_SESSION['email'];

$subjectGroupListsql = "SELECT name FROM subjectgroup";
$subjectGroupList = mysqli_query($conn, $subjectGroupListsql);

?>

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
                    <tr>
                        <th colspan="5" id="group">Khối kiến thức chung</th>
                    </tr>

                        <?php while($rows=mysqli_fetch_assoc($result)) 
                        { 
                    ?> 
                            <tr> <td><?php echo $rows['code']; ?></td> 
                                <td><?php echo $rows['name']; ?></td> 
                                <td><?php echo $rows['credit']; ?></td> 
                                <td>
                                    <?php
                                    
                                    $query2="SELECT * FROM subjectfinished WHERE email = ? AND subject = ?";
                                    $stmt = $conn->prepare($query2);
                                    $stmt->bind_param("ss", $email, $rows['code']);
                                    $stmt->execute();
                                    $result1 = $stmt->get_result();
        
                                    if ($result1->num_rows > 0) {
                                        echo "<input name=\"". $rows['code']. "\" type='checkbox' checked>";
                                    } else {
                                        echo "<input name=\"". $rows['code']. "\" type='checkbox'>";
                                    }
                                    ?>
                                    <!--id=<//?php echo $rows['code']; ?>-->
                                </td>
                                <td><?php echo $rows['presubject']; ?></td>
                            </tr> 
                        <?php 
                        }
                        //mysqli_close($conn); 
                    ?> 
                    
                </tbody>
            </table>
            <input type="submit" class="btn btn-primary" name="save" value="Lưu">
        </form>

        <form action="http://localhost/test/UETchecklist/add.php" method="post">
            <div class="form-group">
                <label>Mã học phần</label>
                <input name="subjectCode" class="form-control">
            </div>
            <div class="form-group">
                <label>Học phần</label>
                <input name="subjectName" class="form-control">
            </div>
            <div class="form-group">
                <label>Số tín chỉ</label>
                <input name="subjectCredit" class="form-control">
            </div>
            <div class="form-group">
                <label>Học phần tiên quyết</label>
                <input name="preSubject" class="form-control" value="">
            </div>
            <div class="form-group">
                <label>Nhóm học phần</label>
                <input list="subjectGroup" name="subjectGroup" class="form-control">
                <datalist id="subjectGroup">
                    <?php
                        while($subjectGroup=mysqli_fetch_assoc($subjectGroupList)) {
                            echo "<option value=\"". $subjectGroup['name']. "\">";
                        }
                    ?>
                </datalist>
            </div>
            <input type="submit" class="btn btn-primary" name="add" value="Thêm">
        </form>
    </body>
</html>