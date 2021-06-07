<?php

session_start();



$conn = mysqli_connect('localhost','root','','K64CCLC_CTDT') or die('Unable To connect');
$groupListsql = "SELECT * FROM subjectgroup";
$groupListquery = mysqli_query($conn, $groupListsql);
$creditFinished = 0;

//Check if subject is finished or not (checked or not checked)
function checkSubject($conn, $count, $rows, $email) {
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
    return $count;
}                                  



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
            $savesql = "INSERT INTO subjectfinished VALUES(?, ?)";
            $savestmt = $conn->prepare($savesql);
            $savestmt->bind_param("ss", $email, $rows['code']);
            $savestmt->execute();
        } 
        // If in db and unchecked in page
        elseif (!isset($_POST[$rows['code']]) && $checkSave->num_rows == 1) {
            $savesql = "DELETE FROM subjectfinished WHERE email = ? AND subject = ?";
            $savestmt = $conn->prepare($savesql);
            $savestmt->bind_param("ss", $email, $rows['code']);
            $savestmt->execute();
        }
    }
    header("Location:savesucess.php");
}

// Create a list of subject group's name
$groupList = [];
while($group=mysqli_fetch_assoc($groupListquery)) {
    $new_arr = array($group['name'], $group['credit']);
    $groupList[] = $new_arr;
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

        <link rel="stylesheet" href="collapse.css">

	</head>

    <body>

        <h1>UET</h1>
        <h1>Studying Process Tracker</h1>
        <p><?php

        //Ignore warning
        error_reporting(E_ERROR | E_PARSE);

        // welcome message
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

        ?>

        <!--Display table of subjects-->
        <form action="" method="post">

            <!-- table header -->
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
            </table>

            <!--Loop through each subject group-->
            <?php foreach ($groupList as $groupName) { ?>
                
            <table>
                <tbody>
                    
                    <!--Name of subject group-->
                    <tr>
                        <th colspan="5" class="subjectGroup"><?php echo $groupName[0]; ?></th>
                    </tr>

                    <?php
                        //List of subject name, each subject a row
                        $subjectListsql="SELECT * FROM subject WHERE subjectgroup = ?";
                        $subjectListstmt = $conn->prepare($subjectListsql);
                        $subjectListstmt->bind_param("s", $groupName[0]);
                        $subjectListstmt->execute();
                        $subjectList = $subjectListstmt->get_result();
                        $count = 0;
                        $subjectSelection = [];
                        while($rows = $subjectList->fetch_assoc()) {
                            //if has subgroup, ignore to last
                            if($rows['subgroup']) {
                                continue;
                            }
                    ?> 
                    
                    <!--rows of subject-->
                    <tr>
                        <td><?php echo $rows['code']; ?></td> 
                        <td><?php echo $rows['name']; ?></td> 
                        <td><?php echo $rows['credit']; ?></td>
                        <!-- checkbox --> 
                        <td><?php $count = checkSubject($conn, $count, $rows, $email); ?></td>
                        <!-- presubject -->
                        <td><?php echo $rows['presubject']; if($rows['presubject2']){echo ", ". $rows['presubject2'];}?></td>
                    </tr>

                    <?php 
                        }
                        //subject subgroup
                        $subgroupListquery = mysqli_query($conn, "SELECT * FROM subject WHERE subjectgroup = '". $groupName[0]. "' AND subgroup IS NOT NULL ORDER BY subgroup, subsubgroup");
                        
                        //check if subject group has subgroup
                        if(mysqli_num_rows($subgroupListquery) > 0) {
                            $countsub = 0;
                            $currentSubgroup = "";
                            $currentSubsubgroup = "";

                            //loop through all subject in all subject subgroup
                            //until the end of subgroup, total credit will show
                            while($rows = mysqli_fetch_assoc($subgroupListquery)) {
                                if($currentSubgroup != $rows['subgroup']) {
                                    
                                    //check if subgroup change, if change and not the first loop, show total credit of subgroup
                                    if($currentSubgroup != "") {
                                        $subgroupCreditquery = mysqli_query($conn, "SELECT * FROM subgroup WHERE name = '". $currentSubgroup. "'");
                                        $subgroupCredit = mysqli_fetch_assoc($subgroupCreditquery);
                                        echo "<tr><td></td><td></td><td>".$countsub. "/". $subgroupCredit['credit']."</td><td></td><td></td></tr>";
                                        $count+=$countsub;
                                        $countsub = 0;
                                    }

                                    //update current subgroup
                                    $currentSubgroup = $rows['subgroup'];
                                    $currentSubsubgroup = $rows['subsubgroup'];
                                    ?>
                                    <tr><td colspan=5><?php echo $currentSubgroup ?></td></tr>
                                    <?php if($rows['subsubgroup'] != NULL) { ?>
                                        <tr><td colspan=5 class="subsubgroup"><?php echo $currentSubsubgroup ?></td></tr>
                                    <?php }
                                }
                                if($currentSubsubgroup != $rows['subsubgroup']) {
                                    $currentSubsubgroup = $rows['subsubgroup'];?>
                                    <tr><td colspan=5 class="subsubgroup"><?php echo $currentSubsubgroup ?></td></tr><?php
                                }

                                //show row of subject
                                ?>
                                <tr><td><?php echo $rows['code']; ?></td>
                                    <td><?php echo $rows['name']; ?></td>
                                    <td><?php echo $rows['credit']; ?></td>
                                    <td><?php $countsub = checkSubject($conn, $countsub, $rows, $email); ?></td>
                                    <td><?php echo $rows['presubject']; if($rows['presubject2']){echo ", ". $rows['presubject2']; }?></td></tr>
                                <?php
                            }

                            //until the end of subgroup, total credit will show
                            $subgroupCreditquery = mysqli_query($conn, "SELECT * FROM subgroup WHERE name = '". $currentSubgroup. "'");
                            $subgroupCredit = mysqli_fetch_assoc($subgroupCreditquery);
                            echo "<tr><td></td><td></td><td>".$countsub. "/". $subgroupCredit['credit']."</td><td></td><td></td></tr>";
                            $count += $countsub;
                            $countsub = 0;
                            echo $rows['subgroup'];
                        }
                        echo "<tr><td><td></td></td><td>".$count. "/". $groupName[1]. "</td><td></td><td></td></tr>";
                        $creditFinished += $count;

                        ?> 
                </tbody>
            </table>
                        <?php

                        }
                        //show total finished credit
                        $totalCreditquery = mysqli_query($conn, "SELECT SUM(credit) AS total FROM subjectgroup");
                        $totalCredit = mysqli_fetch_assoc($totalCreditquery);
                        echo "<h1>Bạn đã hoàn thành ". $creditFinished. "/". $totalCredit['total']. "</h1>";
                    ?> 

            

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
                            foreach ($groupList as $groupName) {
                                echo "<option value=\"". $groupName[0]. "\">";
                            }
                        ?>
                    </datalist>
                </div>
                <input type="submit" class="btn" name="add" value="Thêm">
            </form>
        </div>
        

        <!-- collapse code -->
        <script>
            var coll = document.getElementsByClassName("collapsible");
            var i;

            for (i = 0; i < coll.length; i++) {
            coll[i].addEventListener("click", function() {
                this.classList.toggle("active");
                var content = this.nextElementSibling;
                if (content.style.maxHeight){
                content.style.maxHeight = null;
                } else {
                content.style.maxHeight = content.scrollHeight + "px";
                } 
                console.log("ok");
            });
            }
        </script>



    </body>
</html>