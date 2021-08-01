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
    $loginSessionId = $dbobj->getSessionId();
    if(isset($_POST['btnUser'])){
        $values = array(trim($_POST['txtfname']),trim($_POST['txtlname']),
                        trim($_POST['gender']),SystemDate::date(trim($_POST['txtDob'])),
                        trim($_POST['txtmobile']),trim($_POST['txtGraduate']),
                        trim($_POST['txtProfession']),trim($_POST['sltblood']),
                        trim($_POST['sltCourse']),implode(',', $_POST['sltSkills']),
                        trim($_POST['txtPresent']),trim($_POST['txtPermanent']),
                        trim($_POST['txtCity']),trim($_POST['txtState']),trim($_POST['txtCountry']),
                        trim($_POST['txtPincode']),trim($_POST['txtMaritalStatus']),
                        CURRENT_DT,$loginSessionId,$_SERVER['REMOTE_ADDR'],$id);
        $result = $dbobj->UpdatetData($dbh, TBL_PRIFIX.'user_login_details', 
                        'fname = ?,lname = ?,gender = ?,dob = ?,mobile_number = ?,year_of_graduation = ?,
                        profession = ?,blood_group = ?,course_id = ?,
                        skills_id = ?,present_address = ?,permanent_address = ?,city = ?,
                        state = ?,country = ?,pincode = ?,merital_status = ?,date_modified = ?,
                        modified_by = ?,modified_ip = ?','user_id = ?', $values);
        if($result){
            $_SESSION['success'] = 'User details has been updated successfully.';
            header('location:alumni-user-edit.php?UserId='.$_GET['UserId']);
            exit(0);
        }else{
            $_SESSION['warning'] = 'No changes Done.';
            header('location:alumni-user-edit.php?UserId='.$_GET['UserId']);
            exit(0);
        }
    } 
?>
<!DOCTYPE html>
<html>
<head>
    <?php include (ALUM_TEMPLATES.'metatag.php');?>
   <!-- Bootstrap Select Css -->
   <link href="plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" />
   <!-- Bootstrap DatePicker Css -->
    <link href="plugins/bootstrap-datepicker/css/bootstrap-datepicker.css" rel="stylesheet" />
    <style>
        .image-view {
        height: 90px;
        width: 100%;
        border-radius: 50px;
        }
        .profile-image {
        border-radius: 50px;
        border: 5px solid rgba(224, 233, 239, 0.4);
        height: 100px;
        width: 100px;
        background-position: center center;
        background-size: cover;
        margin: 0 auto 5px auto;
        }
        .profile-image:hover .upload{
            display: block;
        }
        .hover {
        border: 5px solid rgba(224, 233, 239, 0.9);
        width: 100px;
        border-radius: 50px;
        position: relative;
        overflow: hidden;
        }
        .upload {
        opacity: 0.8;
        background: gainsboro;
        height: 25px;
        margin: 65px auto;
        text-align: center;
        color: black;
        position: absolute;
        display: none;
        width: 100%;
        }
   </style>
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
                <div class="col-xs-12 col-sm-12">
                    <div class="card">
                        <div class="body">
                            <form class="form-horizontal" method="post" enctype="multipart/form-data">
                                <div class="header">
                                    <h2>Edit User</h2>
                                    <ul class="header-dropdown m-r--5">
                                        <li class="dropdown">
                                            <button class="btn btn-warning waves-effect" onclick="location.href='alumni-users-list'" type="button">Back</button>
                                            <button class="btn btn-primary waves-effect" name="btnUser" type="submit">Update</button>
                                        </li>
                                    </ul>
                                    <div class="clearfix"></div>
                                </div><br>
                                <div class="col-sm-12">
                                    <div class="profile-image hover">
                                        <img id="yourBtn" src="<?php echo (!empty($usrData['profile_pic']) && $usrData['profile_pic'] != NULL ? ALUM_WS_UPLOADS.ALUM_PROFILE.$usrData['profile_pic'] : ALUM_WS_IMAGES.'user.png');?>" class="image-view" alt="Invalid Image" />
                                        <input class="my-images" value="" id="upfile" name="empProfilepic" mandatory="false" type="file" onchange="readURL(this);">
                                    </div>
                                </div><br>
                                <div class="col-sm-4">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <input type="text" class="form-control" name="txtRegNo" disabled value="<?php echo $usrData['registration_id'];?>" required title="Registration Number" maxlength="30">
                                            <label class="form-label">Registration Number</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <input type="text" class="form-control" name="txtfname" value="<?php echo $usrData['fname'];?>" title="First Name" required maxlength="30">
                                            <label class="form-label">First Name</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <input type="text" class="form-control" name="txtlname" value="<?php echo $usrData['lname'];?>" title="Last Name" maxlength="30">
                                            <label class="form-label">Last Name</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group  form-float">
                                        <div class="form-line">
                                            <input type="radio" name="gender" <?php echo ($usrData['gender'] == 'male' ? 'checked' : '');?> id="male" value="male" class="with-gap" checked>
                                            <label for="male">Male</label>
                                            <input type="radio" name="gender" id="female" value="female" <?php echo ($usrData['gender'] == 'female' ? 'checked' : '');?> class="with-gap">
                                            <label for="female" class="m-l-20">Female</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group form-float">
                                        <div class="form-line" id="bs_datepicker_container">
                                            <input type="text" class="form-control" readonly value="<?php echo (!empty($usrData['dob']) ? AppDate::date($usrData['dob']) : '');?>" title="Date Of Birth" name="txtDob" required>
                                            <label class="form-label">DOB</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <input type="email" class="form-control" title="Email" disabled value="<?php echo (!empty($usrData['email_id']) ? $usrData['email_id'] : '');?>" name="txtEmail" required maxlength="35">
                                            <label class="form-label">Mail Id</label>
                                        </div>
                                    </div>
                                </div><div class="clearfix"></div>
                                <div class="col-sm-4">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <input type="number" class="form-control" title="Mobile Number" value="<?php echo (!empty($usrData['mobile_number']) ? $usrData['mobile_number'] : '');?>" name="txtmobile" required maxlength="15">
                                            <label class="form-label">Mobile Number</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <input type="text" class="form-control" title="Profession" name="txtProfession" value="<?php echo (!empty($usrData['profession']) ? $usrData['profession'] : '');?>" required maxlength="30">
                                            <label class="form-label">Profession</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                        <select class="form-control" name="sltblood" title="Select Blood Group">
                                            <optgroup label="Select Blood Group">
                                            <!-- <option >Mustard</option> -->
                                            <option  <?php echo ($usrData['blood_group'] == 'O+' ? 'selected' : '');?> value="O+">O Positive</option>
                                            <option  <?php echo ($usrData['blood_group'] == 'A+' ? 'selected' : '');?> value="A+">A Positive</option>
                                            <option  <?php echo ($usrData['blood_group'] == 'B+' ? 'selected' : '');?> value="B+">B Positive</option>
                                            <option  <?php echo ($usrData['blood_group'] == 'AB+' ? 'selected' : '');?> value="AB+">AB Positive</option>
                                            <option  <?php echo ($usrData['blood_group'] == 'O-' ? 'selected' : '');?> value="O-">O Negative</option>
                                            <option  <?php echo ($usrData['blood_group'] == 'A-' ? 'selected' : '');?> value="A-">A Negative</option>
                                            <option  <?php echo ($usrData['blood_group'] == 'B-' ? 'selected' : '');?> value="B-">B Negative</option>
                                            <option  <?php echo ($usrData['blood_group'] == 'AB-' ? 'selected' : '');?> value="AB-">AB Negative</option>
                                            </optgroup>
                                        </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                        <select class="form-control" name="slctUserType" disabled>
                                            <option value="no-select">Select User Type</option>
                                            <option  <?php echo ($usrData['user_type'] == 'alumni' ? 'selected' : '');?> value="alumni">Alumni</option>
                                            <option  <?php echo ($usrData['user_type'] == 'student' ? 'selected' : '');?> value="student">Student</option>
                                            <option  <?php echo ($usrData['user_type'] == 'staff' ? 'selected' : '');?> value="staff">Faculty</option>
                                        </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                        <select class="form-control" name="sltCourse" title="Select Course" required>
                                            <optgroup label="Select Course ">
                                            <?php 
                                                $getSkillsData = $dbobj->selectData($dbh, TBL_PRIFIX.'course_master', array('course_id','course'), 'status != ?', array(0));
                                                if($getSkillsData['total'] > 0){
                                                    foreach($getSkillsData['values'] as $rows){
                                                        if($rows['course_id'] == $usrData['course_id']){
                                                                echo '<option value="'.$rows['course_id'].'" selected>'.$rows['course'].'</option>';
                                                        }else{
                                                                echo '<option value="'.$rows['course_id'].'">'.$rows['course'].'</option>';
                                                        }
                                                    }
                                                }else{
                                                    echo '<option value="no-select">No Record</option>';
                                                }
                                            ?>
                                            </optgroup>
                                        </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                        <select class="form-control" name="sltSkills[]" multiple title="Select Skills" required>
                                            <optgroup label="Select Skills">
                                            <?php 
                                                $SkillIds = explode(',',$usrData['skills_id']);
                                                $getSkillsData = $dbobj->selectData($dbh, TBL_PRIFIX.'skill_master', array('skill_id','skill'), 'status != ?', array(0));
                                                if($getSkillsData['total'] > 0){
                                                    foreach($getSkillsData['values'] as $rows){
                                                        if(in_array($rows['skill_id'],$SkillIds)){
                                                                echo '<option value="'.$rows['skill_id'].'" selected>'.$rows['skill'].'</option>';
                                                        }else{
                                                                echo '<option value="'.$rows['skill_id'].'">'.$rows['skill'].'</option>';
                                                        }
                                                    }
                                                }else{
                                                    echo '<option value="no-select">No Record</option>';
                                                }
                                            ?>
                                            </optgroup>
                                        </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <textarea class="form-control" title="Present Address" name="txtPresent" placeholder="Present Address " required maxlength="300"><?php echo (!empty($usrData['present_address']) ? $usrData['present_address'] : '');?></textarea>
                                            <label class="form-label">Present Address</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <textarea class="form-control" title="Permanent Address" name="txtPermanent" placeholder="Permanent Address " maxlength="300"><?php echo (!empty($usrData['permanent_address']) ? $usrData['permanent_address'] : '');?></textarea>
                                            <label class="form-label">Permanent Address</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <input type="text" class="form-control" value="<?php echo (!empty($usrData['city']) ? $usrData['city'] : '');?>" title="City" name="txtCity" placeholder="City" required maxlength="60">
                                             <label class="form-label">City</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <input type="text" class="form-control" title="State" name="txtState" value="<?php echo (!empty($usrData['state']) ? $usrData['state'] : '');?>" placeholder="State" required maxlength="60">
                                            <label class="form-label">State</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <input type="text" class="form-control" title="Country" value="<?php echo (!empty($usrData['country']) ? $usrData['country'] : '');?>" name="txtCountry" placeholder="Country" required maxlength="60">
                                            <label class="form-label">Country</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <input type="text" class="form-control" title="Pincode" value="<?php echo (!empty($usrData['pincode']) ? $usrData['pincode'] : '');?>" name="txtPincode" placeholder="Pincode" required maxlength="6">
											<label class="form-label">Pin Code</label>
										</div>
                                    </div>
                                </div>
                                <div class="col-sm-4" id="bs_year">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <input type="text" class="form-control" value="<?php echo (!empty($usrData['year_of_graduation']) ? $usrData['year_of_graduation'] : '');?>" required placeholder="Year Of Graduation" title="Year Of Graduation" name="txtGraduate">
                                            <label class="form-label">Year Of Graduation</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <input type="radio" name="txtMaritalStatus" <?php echo ($usrData['merital_status'] == 'married' ? 'checked' : '');?> id="Married" value="married" class="with-gap">
                                            <label for="Married">Married</label>
                                            <input type="radio" name="txtMaritalStatus" <?php echo ($usrData['merital_status'] == 'single' ? 'checked' : '');?> id="UnMarried" value="single" class="with-gap">
                                            <label for="UnMarried" class="m-l-20">Single</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                            </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php include ALUM_TEMPLATES.'footer.php';?>
    <!-- Select Plugin Js -->
    <script src="plugins/bootstrap-select/js/bootstrap-select.js"></script>
    <!-- Bootstrap Datepicker Plugin Js -->
    <script src="plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
    <script>
        $(function () {
            $('[name="sltSkills"]').selectpicker();
            $('[name="sltblood"]').selectpicker();
            $('[name="sltCourse"]').selectpicker();
            $('#bs_datepicker_container input').datepicker({
                format: 'dd-mm-yyyy',
                autoclose: true,
                container: '#bs_datepicker_container'
            });
             $('#bs_year input').datepicker({
                format: "yyyy",
                viewMode: "years", 
                minViewMode: "years",
                autoclose: true,
                container: '#bs_year'
            });
        });
    </script>
</body>

</html>
