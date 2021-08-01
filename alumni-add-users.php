<?php
    include 'includes/admin-config.php';
    include_once 'auth.php';
	$dbobj = new GetCommomOperation();
    $loginSessionId = $dbobj->getSessionId();
    if(isset($_POST['btnUser'])){
        $userTd = $dbobj->generateRandomUserId($dbh);
         $val = array(trim($_POST['txtEmail']),trim($_POST['txtmobile']));
        $exists = $dbobj->selectData($dbh, TBL_PRIFIX.'user_login_details', array('user_id'), 'email_id = ? AND mobile_number = ?', $val);
        if($exists['total'] > 0){
            $_SESSION['error'] = 'Duplicate email id/maobile number.';
            header('location:alumni-add-users');
            exit(0);
        }else{
            $data = array(trim($_POST['txtRegNo']),trim($_POST['slctUserType']));
            $validUser = $dbobj->selectData($dbh, TBL_PRIFIX.'student_registration_no', array('reg_id'), 'register_number = ? AND user_type = ?', $data);
            if($validUser['total'] > 0){
                $validUserRegCheck = $dbobj->selectData($dbh, TBL_PRIFIX.'user_login_details', array('user_id'), 'registration_id = ? AND user_type = ?', $data);
                if($validUserRegCheck['total'] == 0){
                    $password  = $dbobj->getRandPass();
                    $values = array($userTd,trim($_POST['txtRegNo']),trim($_POST['txtName']),trim($_POST['txtlname']),
                                    trim($_POST['gender']),SystemDate::date(trim($_POST['txtDob'])),'approved',
                                    trim($_POST['txtmobile']),trim($_POST['txtEmail']),$password['encrypted'],
                                    trim($_POST['txtProfession']),trim($_POST['sltblood']),trim($_POST['slctUserType']),
                                    trim($_POST['sltCourse']),implode(',', $_POST['sltSkills']),
                                    trim($_POST['txtPresent']),trim($_POST['txtPermanent']),
                                    trim($_POST['txtCity']),trim($_POST['txtState']),trim($_POST['txtCountry']),
                                    trim($_POST['txtPincode']),trim($_POST['txtMaritalStatus']),trim($_POST['txtGraduate']),
                                    CURRENT_DT,$loginSessionId,$_SERVER['REMOTE_ADDR']);
                    $result = $dbobj->InsertQuery($dbh, TBL_PRIFIX.'user_login_details', 
                                    array('user_id','registration_id','fname','lname','gender','dob','approval_status','mobile_number','email_id','password','profession','blood_group','user_type','course_id','skills_id','present_address','permanent_address','city','state','country','pincode','merital_status','year_of_graduation','date_added','added_by','added_ip'), $values,true);
                    if($result['status']){
                        include_once ALUM_ETEMPLATES.'user-login-credential-tpl.php';
                        $_SESSION['success'] = 'User has been added successfully. Login credentials has been sent to '.trim($_POST['txtEmail']).'.';
                        header('location:alumni-add-users');
                        exit(0);
                    }else{
                         $_SESSION['warning'] = 'Something went to wrong.';
                        header('location:alumni-add-users');
                        exit(0);
                    }
                }else{
                    $_SESSION['error'] = 'User already exists.';
                    header('location:alumni-add-users');
                    exit(0);  
                }
            }else{
                $_SESSION['error'] = 'Invalid registration id.';
                header('location:alumni-add-users');
                exit(0); 
            }
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
                                    <h2>Add User</h2>
                                    <ul class="header-dropdown m-r--5">
                                        <li class="dropdown">
                                            <button class="btn btn-primary waves-effect" name="btnUser" type="submit">SUBMIT</button>
                                        </li>
                                    </ul>
                                    <div class="clearfix"></div>
                                </div><br><br>
                                <div class="col-sm-4">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <input type="text" class="form-control" name="txtRegNo" required title="Registration Number" maxlength="30">
                                            <label class="form-label">Registration Number</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <input type="text" class="form-control" name="txtName" title="First Name" required maxlength="30">
                                            <label class="form-label">First Name</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <input type="text" class="form-control" name="txtlname" required title="Last Name" maxlength="30">
                                            <label class="form-label">Last Name</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group  form-float">
                                        <div class="form-line">
                                            <input type="radio" name="gender" id="male" value="male" class="with-gap" checked>
                                            <label for="male">Male</label>
                                            <input type="radio" name="gender" id="female" value="female" class="with-gap">
                                            <label for="female" class="m-l-20">Female</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group form-float">
                                        <div class="form-line" id="bs_datepicker_container">
                                            <input type="text" Placeholder="DOB" class="form-control" required readonly title="Date Of Birth" name="txtDob">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <input type="email" class="form-control" title="Email" name="txtEmail" required maxlength="35">
                                            <label class="form-label">Mail Id</label>
                                        </div>
                                    </div>
                                </div><div class="clearfix"></div>
                                <div class="col-sm-4">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <input type="number" class="form-control" title="Mobile Number" name="txtmobile" required maxlength="15">
                                            <label class="form-label">Mobile Number</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <input type="text" class="form-control" title="Profession" name="txtProfession" required maxlength="30">
                                            <label class="form-label">Profession</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                        <select class="form-control" name="sltblood" title="Select Blood Group" required>
                                            <optgroup label="Select Blood Group">
                                            <!-- <option >Mustard</option> -->
                                            <option value="O+">O Positive</option>
                                            <option value="A+">A Positive</option>
                                            <option value="B+">B Positive</option>
                                            <option value="AB+">AB Positive</option>
                                            <option value="O-">O Negative</option>
                                            <option value="A-">A Negative</option>
                                            <option value="B-">B Negative</option>
                                            <option value="AB-">AB Negative</option>
                                            </optgroup>
                                        </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                        <select class="form-control" name="slctUserType" required>
                                            <option value="no-select">Select User Type</option>
                                            <option value="alumni">Alumni</option>
                                            <option value="student">Student</option>
                                            <option value="staff">Faculty</option>
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
                                                        echo '<option value="'.$rows['course_id'].'">'.$rows['course'].'</option>';
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
                                                $SkillIds = explode(',',$getLoggedData['skills_id']);
                                                $getSkillsData = $dbobj->selectData($dbh, TBL_PRIFIX.'skill_master', array('skill_id','skill'), 'status != ?', array(0));
                                                if($getSkillsData['total'] > 0){
                                                    foreach($getSkillsData['values'] as $rows){
                                                        echo '<option value="'.$rows['skill_id'].'">'.$rows['skill'].'</option>';
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
                                            <textarea class="form-control" title="Present Address" name="txtPresent" required maxlength="300"></textarea>
                                            <label class="form-label">Present Address</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <textarea class="form-control" title="Permanent Address" required name="txtPermanent" maxlength="300"></textarea>
                                            <label class="form-label">Permanent Address</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <input type="text" class="form-control" title="City" name="txtCity" required maxlength="60">
                                             <label class="form-label">City</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <input type="text" class="form-control" title="State" name="txtState" required maxlength="60">
                                            <label class="form-label">State</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <input type="text" class="form-control" title="Country" name="txtCountry" required maxlength="60">
                                            <label class="form-label">Country</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <input type="text" class="form-control" title="Pincode" name="txtPincode" required maxlength="6">
											<label class="form-label">Pin Code</label>
										</div>
                                    </div>
                                </div>
                                <div class="col-sm-4" id="bs_year">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <input type="text" class="form-control" readonly required placeholder="Year Of Graduation" title="Year Of Graduation" name="txtGraduate">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group form-float">
                                        <div class="form-line">
                                            <input type="radio" name="txtMaritalStatus" id="Married" value="married" checked class="with-gap">
                                            <label for="Married">Married</label>
                                            <input type="radio" name="txtMaritalStatus" id="UnMarried" value="single" class="with-gap">
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
