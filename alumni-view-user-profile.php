<?php
include 'includes/admin-config.php';
include_once 'auth.php';
$dbobj = new GetCommomOperation();
    if(isset($_GET['UserId'])){
        include 'includes/emp-crypto-graphy.php';
        $SafeCrypto = new SafeCrypto();
        $id = $SafeCrypto->decrypt($_GET['UserId']);
        $usrData = $dbobj->getUserData($dbh, $id);
        if($usrData['total'] > 0){
            $usrData = $usrData['values'][0];
        }else{
            $_SESSION['warning'] = 'No record found.';
            header('location:alumni-users-list');
            exit(0);
        }
    }else{
        $_SESSION['warning'] = 'Invalid url.';
        header('location:alumni-users-list');
        exit(0);
    }
?>
<!DOCTYPE html>
<html>
<head>
    <?php include (ALUM_TEMPLATES.'metatag.php');?>
</head>
<body class="theme-red">
    <?php
        // <!-- Page Loader -->
        include ALUM_TEMPLATES.'loader.php';
        // <!-- #END# Page Loader -->
        // <!-- Top Bar -->
            include ALUM_TEMPLATES.'top-navigation.php';
        // <!-- #Top Bar -->
        // <!-- Left Sidebar -->
            include ALUM_TEMPLATES.'left-links.php';
        // <!-- #Left Sidebar -->
    ?>
    <section class="content">
        <div class="container-fluid">
            <div class="row clearfix">
                <div class="col-xs-12 col-sm-6">
                    <div class="card profile-card">
                        <div class="profile-header">&nbsp;</div>
                        <div class="profile-body">
                            <div class="image-area">
                                <img src="<?php echo (!empty($usrData['profile_pic']) && $usrData['profile_pic'] != NULL ? ALUM_WS_UPLOADS.ALUM_PROFILE.$usrData['profile_pic'] : ALUM_WS_IMAGES.'user.png');?>" alt="Profile Image" />
                            </div>
                            <div class="content-area">
                                <h3><?php echo ucwords($usrData['fname'].' '.$usrData['lname']);?></h3>
                                <p><?php echo ucwords($usrData['profession']);?></p>
                                <p><?php echo ucwords($usrData['user_type']);?></p>
                            </div>
                        </div>
                        <div class="profile-footer">
                            <ul>
                                <li>
                                    <span>Email Id</span>
                                    <span><?php echo ucwords($usrData['email_id']);?></span>
                                </li>
                                <li>
                                    <span>Mobile Number</span>
                                    <span><?php echo ucwords($usrData['mobile_number']);?></span>
                                </li>
                                <li>
                                    <span>Year Of Graduation</span>
                                    <span><?php echo ucwords($usrData['year_of_graduation']);?></span>
                                </li>
                                <li>
                                    <span>Date Of Birth</span>
                                    <span><?php echo AppDate::date($usrData['dob']);?></span>
                                </li>
                            </ul>
                            <!-- <button class="btn btn-primary btn-lg waves-effect btn-block">FOLLOW</button> -->
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6">
                      <div class="card card-about-me">
                        <div class="header">
                            <h2>About <span style="color:#ad1455;font-weight:600;"><?php echo ucwords($usrData['fname'].' '.$usrData['lname']);?></span></h2>
                        </div>
                        <div class="body">
                            <?php 
                                 $sql_select = "SELECT  skill FROM alumni_skill_master WHERE  skill_id IN (".$usrData['skills_id'].")";
                                $stmt = $dbh->prepare($sql_select);
                                $stmt->execute();
                                $rowCount = $stmt->rowCount();
                                if ($rowCount > 0) {
                                    for ($i = 1; $i <= $rowCount; $i++) {
                                        $skillsResult['values'][] = $stmt->fetch(PDO::FETCH_ASSOC);
                                    }
                                }
                                $course = $dbobj->selectData($dbh, TBL_PRIFIX.'course_master', array('course'), 'course_id = ?', array($usrData['course_id']));
                            ?>
                            <ul>
                                <li>
                                    <div class="title">
                                        <i class="material-icons">library_books</i>
                                        Education
                                    </div>
                                    <?php 
                                        if($course['total'] >0){
                                            echo "<div class='content'>
                                               ".$course['values'][0]['course']."
                                            </div>";
                                        }
                                    ?>
                                </li>
                                <li>
                                    <div class="title">
                                        <i class="material-icons">location_on</i>
                                        Location
                                    </div>
                                    <div class="content">
                                       <?php echo ucwords($usrData['city']);?>
                                    </div>
                                </li>
                                <?php 
                                    if ($rowCount > 0) {
                                ?>
                                <li>
                                    <div class="title">
                                        <i class="material-icons">edit</i>
                                        Skills
                                    </div>
                                    <div class="content">
                                        <?php 
                                            foreach ($skillsResult['values'] as $value) {
                                                echo ' <span class="label bg-red">'.$value['skill'].'</span>';
                                            }
                                        ?>
                                    </div>
                                </li>
                            <?php } ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php include ALUM_TEMPLATES.'footer.php';?> 
</body>

</html>
