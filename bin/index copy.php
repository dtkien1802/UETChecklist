<?php

session_start();



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

// Create a list of subject group's name
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
            echo "Ch??o ". $email. "
            <form action=\"account/logout.php\">
            <input class=\"btn\" type=\"submit\" value=\"????ng xu???t\">
            </form>
            ";
        } else {
            echo "B???n ch??a ????ng nh???p". $email. " 
            <form action=\"account/login.php\">
            <input class=\"btn\" type=\"submit\" value=\"????ng nh???p\">
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
                    <th>M?? h???c ph???n</th>
                    <th>H???c ph???n</th>
                    <th>S??? t??n ch???</th>
                    <th>???? h???c</th>
                    <th>H???c ph???n ti??n quy???t</th>
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
                        $subjectSelection = [];
                        while($rows = $subjectList->fetch_assoc()) {
                            if($rows['subjectsubgroup']) {
                                $subjectSelection[] = $rows;
                                continue;
                            }
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
                        <td><?php 
                                echo $rows['presubject'];
                                if($rows['presubject2']){
                                    echo ", ". $rows['presubject2'];
                                }
                            ?></td>
                    </tr>

                    <?php 
                        }
                        if($subjectSelection) {
                            echo "<tr><td colspan=5>". $subjectSelection[0]['subjectsubgroup']. "</td></tr>";
                            $finished = "";

                            // find finished subject in selection group, stored code in $finished
                            $check=mysqli_query($conn, "SELECT * FROM subjectfinished WHERE email = '". $email. "' AND (subject = '". $subjectSelection[0]['code']. "' OR subject = '". $subjectSelection[1]['code']. "')");
                            echo $email. " - ". $subjectSelection[0]['code']. " - ". $subjectSelection[1]['code'];
                            while($row  = $check->fetch_assoc()) {
                                $finished = $row['subject'];
                            }
                            
                            // if a subject is finished or all is not
                            foreach ($subjectSelection as $subjectSelectionName) {
                                echo "  <tr><td>". $subjectSelectionName['code']. "</td>
                                            <td>". $subjectSelectionName['name']. "</td></td>
                                            <td>". $subjectSelectionName['credit']. "</td></td>
                                        ";
                                if($finished) {
                                    if($subjectSelectionName['code'] == $finished) {
                                        echo "<td><input class=\"cbox\" name=\"". $subjectSelectionName['code']. "\" type='checkbox' checked></td>";
                                        $count+=$subjectSelectionName['credit'];
                                    } else {
                                        echo "<td><input class=\"cbox\" name=\"". $subjectSelectionName['code']. "\" type='checkbox' checked disabled></td>";
                                    } 
                                }
                                else {      
                                        echo "<td><input class=\"cbox\" name=\"". $subjectSelectionName['code']. "\" type='checkbox'></td>";
                                }
                                echo"   <td>". $subjectSelectionName['presubject']. "</td></tr>";
                            }
                        }
                        echo "<tr><td><td></td></td><td>".$count. "/". $subjectGroupName[1]. "</td><td></td><td></td></tr>";
                        }
                    ?> 
                </tbody>
            </table>

            <!--Save changes-->
            <input type="submit" name="save" value="L??u" class="btn">
        </form>
        <!--Add new subject-->
        <div class="wrapper" style="margin-top: 40px;">
            <h2>Th??m h???c ph???n</h2>
            <form action="http://localhost/test/UETchecklist/addSubject.php" method="post" autocomplete="off">
                <div class="form">
                    <label>M?? h???c ph???n</label>
                    <input name="subjectCode" class="text-box" required placeholder="V?? d???: ABC_1234">
                </div>
                <div class="form">
                    <label>H???c ph???n</label>
                    <input name="subjectName" class="text-box" required>
                </div>
                <div class="form">
                    <label>S??? t??n ch???</label>
                    <input name="subjectCredit" class="text-box" required type="number" min="1" max="10">
                </div>
                <div class="form">
                    <label>H???c ph???n ti??n quy???t</label>
                    <input name="preSubject" class="text-box" value="" placeholder="V?? d???: ABC_1234/????? tr???ng n???u kh??ng c??">
                </div>
                <div class="form">
                    <label>Nh??m h???c ph???n</label>
                    <input list="subjectGroup" name="subjectGroup" class="text-box" required>
                    <datalist id="subjectGroup">
                        <?php
                            foreach ($subjectGroupList as $subjectGroupName) {
                                echo "<option value=\"". $subjectGroupName[0]. "\">";
                            }
                        ?>
                    </datalist>
                </div>
                <input type="submit" class="btn" name="add" value="Th??m">
            </form>
        </div>
    </body>
</html>