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

        ?>

            

        <!--Display table of subjects-->
        <form action="" method="post">

            <!-- table header -->
            <table class="table table-bordered">
                
            </table>
                    <!--Loop through each subject group-->
                    <?php $groupIndex=0; $groupName = "subjectgroup"; foreach ($groupList as $groupName) { ?>
                        
                        <!--Name of subject group-->
                        <h1 class="collapsible" id=<?php echo "\"".$groupName. $groupIndex. "\""; ?>><?php echo $groupName[0]; ?></h1>
                        <div class="content" style="background-color: black">
                            <table>
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
                                <td id = <?php echo "\"". $rows['code']. "\""; ?>><?php echo $rows['code']; ?></td> 
                                <td><?php echo $rows['name']; ?></td> 
                                <td><?php echo $rows['credit']; ?></td>
                                <!-- checkbox --> 
                                <td><?php $count = checkSubject($conn, $count, $rows, $email); ?></td>
                                <!-- presubject -->
                                <td><a title="a" href = <?php echo "\"#". $rows['presubject']. "\""; ?>><?php echo $rows['presubject']; ?></a>
                                <?php if($rows['presubject2']){echo ", <a href = \"#". $rows['presubject2']. "\">". $rows['presubject2']. "</a>";}?></td>
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
                                            <td><a title="a" href = <?php echo "\"#". $rows['presubject']. "\""; ?>><?php echo $rows['presubject']; ?></a>
                                            <?php if($rows['presubject2']){echo ", <a href = \"#". $rows['presubject2']. "\">". $rows['presubject2']. "</a>";}?></td>
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
                            </table>
                        </div>
                        <script>
                            document.getElementById(<?php echo "\"". $groupName. $groupIndex. "\"" ?>).innerHTML = "<?php echo $groupName[0]. ' '. $count. '/'. $groupName[1]. '  '. $groupID; $groupIndex++;?>"
                        </script>
                    <?php } ?> 
                        

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
                            foreach ($groupList as $groupName) {
                                echo "<option value=\"". $groupName[0]. "\">";
                            }
                        ?>
                    </datalist>
                </div>
                <input type="submit" class="btn" name="add" value="Th??m">
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
    <footer>
        <?php 
        //show total finished credit
        $totalCreditquery = mysqli_query($conn, "SELECT SUM(credit) AS total FROM subjectgroup");
        $totalCredit = mysqli_fetch_assoc($totalCreditquery);
        echo "<h1>B???n ???? ho??n th??nh ". $creditFinished. "/". $totalCredit['total']. "</h1>";
        ?>
    </footer>
</html>