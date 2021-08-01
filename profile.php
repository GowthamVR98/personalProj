<?php
    include 'includes/admin-config.php';
    include_once 'auth.php';
	$dbobj = new GetCommomOperation();
    $loginSessionId = $dbobj->getSessionId();
    if(isset($_POST['txtUpdate'])){
        $usrData = $dbobj->getUserData($dbh, $loginSessionId);
        if(!empty($_FILES['empProfilepic']['name'])){
            $fileUpload = $dbobj->fileUpload(ALUM_ABS_PATH.ALUM_COMMON_UPLOADS.ALUM_PROFILE, $_FILES['empProfilepic']);
        }else{
             $fileUpload = $usrData['values'][0]['profile_pic'];
        }
        $values = array(trim($_POST['txtfname']),trim($_POST['txtlname']),
                        trim($_POST['gender']),SystemDate::date(trim($_POST['txtDob'])),
                        trim($_POST['txtmobile']),$fileUpload,
                        trim($_POST['txtProfession']),trim($_POST['sltblood']),
                        trim($_POST['sltCourse']),implode(',', $_POST['sltSkills']),
                        trim($_POST['txtPresent']),trim($_POST['txtPermanent']),
                        trim($_POST['txtCity']),trim($_POST['txtState']),trim($_POST['txtCountry']),
                        trim($_POST['txtPincode']),trim($_POST['txtMaritalStatus']),
                        CURRENT_DT,$loginSessionId,$_SERVER['REMOTE_ADDR'],$loginSessionId);
        $result = $dbobj->UpdatetData($dbh, TBL_PRIFIX.'user_login_details', 
                        'fname = ?,lname = ?,gender = ?,dob = ?,mobile_number = ?,
                        profile_pic = ?,profession = ?,blood_group = ?,course_id = ?,
                        skills_id = ?,present_address = ?,permanent_address = ?,city = ?,
                        state = ?,country = ?,pincode = ?,merital_status = ?,date_modified = ?,
                        modified_by = ?,modified_ip = ?','user_id = ?', $values);
        if($result){
            $_SESSION['success'] = 'Profile details has been updated successfully.';
            header('location:profile.php');
            exit(0);
        }else{
             $_SESSION['warning'] = 'No changes Done.';
            header('location:profile.php');
            exit(0);
        }
    }else if(isset($_POST['UpdatePassword'])){
        $val = array($loginSessionId);
        $OldPassword = $dbobj->selectData($dbh, TBL_PRIFIX.'user_login_details', array('password'), 'user_id = ?', $val);
        if($OldPassword['total'] > 0){
            $verify = password_verify(trim($_POST['OldPassword']),$OldPassword['values'][0]['password']);
            if($verify == true){
                if(trim($_POST['NewPasswordConfirm']) == trim($_POST['NewPassword'])){
                    $password  = $dbobj->getRandPass(trim($_POST['NewPasswordConfirm']));
                    $values = array($password['encrypted'],$loginSessionId);
                    $result = $dbobj->UpdatetData($dbh, TBL_PRIFIX.'user_login_details','password = ?','user_id = ?' , $values);
                    if($result){
                        $_SESSION['success'] = 'Password has been updated successfully.';
                        header('location:profile');
                        exit(0);
                    }else{
                        $_SESSION['error'] = 'Password could not be updated.';
                        header('location:profile');
                        exit(0);
                    }
                }else{
                     $_SESSION['error'] = 'New password and confirm password not match.';
                    header('location:profile');
                    exit(0);
                }
            }else{
                $_SESSION['error'] = 'Entered wrong old password.';
                header('location:profile');
                exit(0);
            }
            
        }else{

        }
    }
?>
<!DOCTYPE html>
<html>
<head>
    <?php include (ALUM_TEMPLATES.'metatag.php');?>
   <!-- Bootstrap Select Css -->
   <link href="plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" />
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
                            <div>
                                <ul class="nav nav-tabs" role="tablist">
                                    <li role="presentation" class="active"><a href="#profile_settings" aria-controls="settings" role="tab" data-toggle="tab"><strong>Profile Details</strong></a></li>
                                    <li role="presentation"><a href="#change_password_settings" aria-controls="settings" role="tab" data-toggle="tab"><strong>Change Password</strong></a></li>
                                </ul>
                                <div class="tab-content">
                                    <div role="tabpanel" class="tab-pane fade in active" id="profile_settings">
                                        <form class="form-horizontal" method="post" enctype="multipart/form-data">
                                            <div class="col-sm-12">
                                            <div class="profile-image hover">
                                                <p class="upload">Upload</p>
                                                <img id="yourBtn" src="<?php echo (!empty($getLoggedData['profile_pic']) && $getLoggedData['profile_pic'] != NULL ? ALUM_WS_UPLOADS.ALUM_PROFILE.$getLoggedData['profile_pic'] : ALUM_WS_IMAGES.'user.png');?>" class="image-view" alt="Invalid Image" />
                                                <input class="my-images" value="" id="upfile" name="empProfilepic" mandatory="false" type="file" onchange="readURL(this);">
                                            </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group form-float">
                                                    <div class="form-line">
                                                        <input type="text" class="form-control" name="txtfname" value="<?php echo $getLoggedData['fname'];?>" title="First Name" required maxlength="30">
                                                        <label class="form-label">First Name</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group form-float">
                                                    <div class="form-line">
                                                        <input type="text" class="form-control" name="txtlname" value="<?php echo $getLoggedData['lname'];?>" title="Last Name" maxlength="30">
                                                        <label class="form-label">Last Name</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group  form-float">
                                                    <div class="form-line">
                                                        <input type="radio" name="gender" <?php echo ($getLoggedData['gender'] == 'male' ? 'checked' : '');?> id="male" value="male" class="with-gap" checked>
                                                        <label for="male">Male</label>
                                                        <input type="radio" name="gender" id="female" value="female" <?php echo ($getLoggedData['gender'] == 'female' ? 'checked' : '');?> class="with-gap">
                                                        <label for="female" class="m-l-20">Female</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="clearfix"></div>
                                            <div class="col-sm-4">
                                                <div class="form-group form-float">
                                                    <div class="form-line" id="bs_datepicker_container">
                                                        <input type="text" class="form-control" readonly value="<?php echo (!empty($getLoggedData['dob']) ? AppDate::date($getLoggedData['dob']) : '');?>" title="Date Of Birth" name="txtDob" required>
                                                        <label class="form-label">DOB</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group form-float">
                                                    <div class="form-line">
                                                        <input type="email" class="form-control" title="Email" disabled value="<?php echo (!empty($getLoggedData['email_id']) ? $getLoggedData['email_id'] : '');?>" name="txtEmail" required maxlength="35">
                                                        <label class="form-label">Mail Id</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group form-float">
                                                    <div class="form-line">
                                                        <input type="number" class="form-control" title="Mobile Number" value="<?php echo (!empty($getLoggedData['mobile_number']) ? $getLoggedData['mobile_number'] : '');?>" name="txtmobile" required maxlength="15">
                                                        <label class="form-label">Mobile Number</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group form-float">
                                                    <div class="form-line">
                                                        <input type="text" class="form-control" title="Profession" name="txtProfession" value="<?php echo (!empty($getLoggedData['profession']) ? $getLoggedData['profession'] : '');?>" required maxlength="30">
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
                                                        <option  <?php echo ($getLoggedData['blood_group'] == 'O+' ? 'selected' : '');?> value="O+">O Positive</option>
                                                        <option  <?php echo ($getLoggedData['blood_group'] == 'A+' ? 'selected' : '');?> value="A+">A Positive</option>
                                                        <option  <?php echo ($getLoggedData['blood_group'] == 'B+' ? 'selected' : '');?> value="B+">B Positive</option>
                                                        <option  <?php echo ($getLoggedData['blood_group'] == 'AB+' ? 'selected' : '');?> value="AB+">AB Positive</option>
                                                        <option  <?php echo ($getLoggedData['blood_group'] == 'O-' ? 'selected' : '');?> value="O-">O Negative</option>
                                                        <option  <?php echo ($getLoggedData['blood_group'] == 'A-' ? 'selected' : '');?> value="A-">A Negative</option>
                                                        <option  <?php echo ($getLoggedData['blood_group'] == 'B-' ? 'selected' : '');?> value="B-">B Negative</option>
                                                        <option  <?php echo ($getLoggedData['blood_group'] == 'AB-' ? 'selected' : '');?> value="AB-">AB Negative</option>
                                                        </optgroup>
                                                    </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group form-float">
                                                    <div class="form-line">
                                                    <select class="form-control" name="sltCourse" title="Select Course">
                                                        <optgroup label="Select Course ">
                                                        <?php 
                                                            $getSkillsData = $dbobj->selectData($dbh, TBL_PRIFIX.'course_master', array('course_id','course'), 'status != ?', array(0));
                                                            if($getSkillsData['total'] > 0){
                                                                foreach($getSkillsData['values'] as $rows){
                                                                    if($rows['course_id'] == $getLoggedData['course_id']){
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
                                                    <select class="form-control" name="sltSkills[]" multiple title="Select Skills">
                                                        <optgroup label="Select Skills">
                                                        <?php 
                                                            $SkillIds = explode(',',$getLoggedData['skills_id']);
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
                                                        <textarea class="form-control" title="Present Address" name="txtPresent" placeholder="Present Address " required maxlength="300"><?php echo (!empty($getLoggedData['present_address']) ? $getLoggedData['present_address'] : '');?></textarea>
                                                        <label class="form-label">Present Address</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group form-float">
                                                    <div class="form-line">
                                                        <textarea class="form-control" title="Permanent Address" name="txtPermanent" placeholder="Permanent Address " maxlength="300"><?php echo (!empty($getLoggedData['permanent_address']) ? $getLoggedData['permanent_address'] : '');?></textarea>
                                                        <label class="form-label">Permanent Address</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group form-float">
                                                    <div class="form-line">
                                                        <input type="text" class="form-control" value="<?php echo (!empty($getLoggedData['city']) ? $getLoggedData['city'] : '');?>" title="City" name="txtCity" placeholder="City" required maxlength="60">
                                                         <label class="form-label">City</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group form-float">
                                                    <div class="form-line">
                                                        <input type="text" class="form-control" title="State" name="txtState" value="<?php echo (!empty($getLoggedData['state']) ? $getLoggedData['state'] : '');?>" placeholder="State" required maxlength="60">
                                                        <label class="form-label">State</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group form-float">
                                                    <div class="form-line">
                                                        <input type="text" class="form-control" title="Country" value="<?php echo (!empty($getLoggedData['country']) ? $getLoggedData['country'] : '');?>" name="txtCountry" placeholder="Country" required maxlength="60">
                                                        <label class="form-label">Country</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group form-float">
                                                    <div class="form-line">
                                                        <input type="text" class="form-control" title="Pincode" value="<?php echo (!empty($getLoggedData['pincode']) ? $getLoggedData['pincode'] : '');?>" name="txtPincode" placeholder="Pincode" required maxlength="6">
														<label class="form-label">Pin Code</label>
													</div>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group form-float">
                                                    <div class="form-line">
                                                    <select class="form-control" name="txtGraduate" multiple title="Select Academic Year">
                                                        <optgroup label="Select Academic Year">
                                                         <?php 
                                                            $getAcademic = $dbobj->selectData($dbh, TBL_PRIFIX.'academic_year_master', array('academic_id','academic_name'), 'status != ?', array(0));
                                                            if($getAcademic['total'] > 0){
                                                                foreach($getAcademic['values'] as $rows){
                                                                    if($rows['academic_id'] == $getLoggedData['year_of_graduation']){
                                                                        echo '<option selected value="'.$rows['academic_id'].'">'.$rows['academic_name'].'</option>';
                                                                    }else{
                                                                        echo '<option value="'.$rows['academic_id'].'">'.$rows['academic_name'].'</option>';
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
                                                        <input type="radio" name="txtMaritalStatus" <?php echo ($getLoggedData['merital_status'] == 'married' ? 'checked' : '');?> id="Married" value="married" class="with-gap">
                                                        <label for="Married">Married</label>
                                                        <input type="radio" name="txtMaritalStatus" <?php echo ($getLoggedData['merital_status'] == 'single' ? 'checked' : '');?> id="UnMarried" value="single" class="with-gap">
                                                        <label for="UnMarried" class="m-l-20">Single</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-sm-offset-2 col-sm-10">
                                                    <button type="submit" name="txtUpdate"class="btn btn-danger pull-right">UPDATE</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div role="tabpanel" class="tab-pane fade in" id="change_password_settings">
                                        <form class="form-horizontal" method="post">
                                            <div class="form-group">
                                                <label for="OldPassword" class="col-sm-3 control-label">Old Password</label>
                                                <div class="col-sm-9">
                                                    <div class="form-line">
                                                        <input type="password" class="form-control" id="OldPassword" name="OldPassword" placeholder="Old Password" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="passwordChange">
                                                <div class="form-group">
                                                    <label for="NewPassword" class="col-sm-3 control-label">New Password</label>
                                                    <div class="col-sm-9">
                                                        <div class="form-line">
                                                            <input type="password" class="form-control" id="NewPassword" name="NewPassword" placeholder="New Password" required>
                                                        </div>
                                                        <span class="hideShowEye" style="position: absolute; top: 0;right: 10px;">
                                                            <i class="material-icons NotVisiblePaassword" style="cursor: pointer; color: rgb(179, 174, 174);">visibility_off</i>
                                                            <i class="material-icons VisiblePaassword" style="cursor: pointer; display:none;color: rgb(179, 174, 174);">visibility</i>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="NewPasswordConfirm" class="col-sm-3 control-label">Confirm Password</label>
                                                    <div class="col-sm-9">
                                                        <div class="form-line">
                                                            <input type="password" class="form-control" id="NewPasswordConfirm" name="NewPasswordConfirm" placeholder="Confirm Password" required>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-sm-offset-3 col-sm-9">
                                                    <button type="submit" class="btn btn-danger pull-right" name="UpdatePassword">SUBMIT</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
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
    <script>
        function readURL(input) {
            if (input.files && input.files[0]) {
                var regex = /^([a-zA-Z0-9\-\)\(\}\s_\\.\-:])+(.jpg|.png|.jpeg)$/;
                console.log(input.files);
                var fileSizeValue = 10485760;
                if (regex.test(input.files[0].name.toLowerCase())) {
                    if (input.files[0].size < fileSizeValue) {
                        var reader = new FileReader();
                        reader.onload = function (e) {
                            $('#yourBtn').attr('src', e.target.result);
                        };
                        reader.readAsDataURL(input.files[0]);
                    }
                }
            }
        }
        $('#yourBtn').click(function(){
            $('#upfile').trigger('click');
        });
        $(function () {
            $('[name="sltSkills"]').selectpicker();
            $('[name="sltblood"]').selectpicker();
            $('[name="sltCourse"]').selectpicker();
            $('#bs_datepicker_container input').datepicker({
                format: 'dd-mm-yyyy',
                autoclose: true,
                container: '#bs_datepicker_container'
            });
            $('.NotVisiblePaassword').click(function(){
                $(this).hide().parent().parent().find('.VisiblePaassword').show().parent().parent().parent().parent().find('input').attr('type','text');
            });
            $('.VisiblePaassword').click(function(){
                $(this).hide().parent().parent().find('.NotVisiblePaassword').show().parent().parent().parent().parent().find('input').attr('type','password');
            });
            form.validate({
                highlight: function (input) {
                    $(input).parents('.form-line').addClass('error');
                },
                unhighlight: function (input) {
                    $(input).parents('.form-line').removeClass('error');
                },
                errorPlacement: function (error, element) {
                    $(element).parents('.form-group').append(error);
                },
                rules: {
                    'confirm': {
                        equalTo: '#password'
                    }
                }
            });
        });
    </script>
</body>

</html>
