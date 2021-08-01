<?php
    if (!session_id() ) {
		session_start();
	}
    if(isset($_SESSION['SESS_ADMIN_ID']) && (trim($_SESSION['SESS_ADMIN_ID']) != '')) {
        header("location: dashboard");
		exit(0);
    }
    include_once 'includes/admin-config.php';
    $dbobj = new GetCommomOperation();
    $password  = $dbobj->getRandPass();
    if(isset($_POST['sighUp'])){
        $userTd = $dbobj->generateRandomUserId($dbh);
        $val = array(trim($_POST['txtEmail']),trim($_POST['txtMobile']));
        $exists = $dbobj->selectData($dbh, TBL_PRIFIX.'user_login_details', array('user_id'), 'email_id = ? AND mobile_number = ?', $val);
        if($exists['total'] > 0){
            $_SESSION['error'] = 'User already exists.';
            header('location:alumni-registration');
            exit(0);
        }else{
            $data = array(trim($_POST['txtRegId']),trim($_POST['slctUserType']));
            $validUser = $dbobj->selectData($dbh, TBL_PRIFIX.'student_registration_no', array('reg_id'), 'register_number = ? AND user_type = ?', $data);
            if($validUser['total'] > 0){
                $data = array(trim($_POST['txtRegId']),trim($_POST['slctUserType']));
                $validUserRegCheck = $dbobj->selectData($dbh, TBL_PRIFIX.'user_login_details', array('user_id'), 'registration_id = ? AND user_type = ?', $data);
                if($validUserRegCheck['total'] == 0){
                    $password  = $dbobj->getRandPass();
                    $values = array($userTd,trim($_POST['txtRegId']),trim($_POST['txtName']),trim($_POST['txtEmail']),$password['encrypted'],trim($_POST['txtMobile']),trim($_POST['radioGender']),trim($_POST['slctUserType']),trim($_POST['txtTerms']),trim($_POST['txtGraduate']),CURRENT_DT,$userTd,$_SERVER['REMOTE_ADDR']);
                    $result = $dbobj->InsertQuery($dbh, TBL_PRIFIX.'user_login_details', array('user_id','registration_id','fname','email_id','password','mobile_number','gender','user_type','terms_and_condition','year_of_graduation','date_added','added_by','added_ip'), $values);
                    if($result['status'] == true){
                        include_once ALUM_ETEMPLATES.'user-login-credential-tpl.php';
                        $_SESSION['success'] = 'Registration has been successfully completed. Your login credentials has been sent to your registered mail id.';
                        header('location:alumni-registration');
                        exit(0);  
                    }else{
                        $_SESSION['error'] = 'Faild To Register user.';
                        header('location:alumni-registration');
                        exit(0);  
                    }
                }else{
                    $_SESSION['error'] = 'Not Registered User Contact Admin.';
                    header('location:alumni-registration');
                    exit(0);  
                    }
            }else{
                $_SESSION['error'] = 'Not Registered User Contact Admin.';
                header('location:alumni-registration');
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

<body class="signup-page">
    <div class="signup-box">
        <div class="logo">
            <a href="javascript:void(0);">Alumni<b>Portal</b></a>
            <small></small>
        </div>
        <div class="card">
            <div class="body">
                <form id="sign_up" method="POST">
                    <div class="msg">User Registration</div>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">person</i>
                        </span>
                        <div class="form-line">
                            <input type="text" class="form-control" name="txtName" placeholder="Name" required autofocus>
                        </div>
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">fingerprint</i>
                        </span>
                        <div class="form-line">
                            <input type="text" class="form-control" name="txtRegId" placeholder="Registration Number" required>
                        </div>
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">email</i>
                        </span>
                        <div class="form-line">
                            <input type="email" class="form-control" name="txtEmail" placeholder="Email Address" required>
                        </div>
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">phone_iphone</i>
                        </span>
                        <div class="form-line">
                        <input type="text" class="form-control mobile-phone-number" name="txtMobile" placeholder="Mobile Number" required>
                        </div>
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">face</i>
                        </span>
                        <div class="form-line">
                            <input type="radio" name="radioGender" value="male" id="radio-male" class="with-gap radio-col-cyan" required/>
                            <label for="radio-male">Male</label>
                            <input type="radio" name="radioGender" value="female" id="radio-female" class="with-gap radio-col-cyan" required/>
                            <label for="radio-female">Female</label>
                        </div>
                    </div>
                    <div class="input-group date">
                        <span class="input-group-addon">
                            <i class="material-icons">date_range</i>
                        </span>
                        <div class="form-line" style="z-index: 9999;">
                            <select class="form-control" name="txtGraduate" title="Select Academic Year" required>
                                <optgroup label="Select Academic Year ">
                                <?php 
                                    $getAcademic = $dbobj->selectData($dbh, TBL_PRIFIX.'academic_year_master', array('academic_id','academic_name'), 'status != ?', array(0));
                                    if($getAcademic['total'] > 0){
                                        foreach($getAcademic['values'] as $rows){
                                            echo '<option value="'.$rows['academic_id'].'">'.$rows['academic_name'].'</option>';
                                        }
                                    }else{
                                        echo '<option value="no-select">No Record</option>';
                                    }
                                ?>
                                </optgroup>
                            </select>
                        </div>
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">supervisor_account</i>
                        </span>
                        <div class="form-line" style="z-index: 9;">
                            <select class="form-control" name="slctUserType" required>
                                <option value="no-select">Select User Type</option>
                                <option value="alumni">Alumni</option>
                                <option value="student">Student</option>
                                <option value="staff">Faculty</option>
                            </select>
                        </div>
                    </div>  
                    <div class="form-group">
                        <input type="checkbox" name="txtTerms" value="accepted" id="terms" class="filled-in chk-col-pink" required>
                        <label for="terms">I read and agree to the <a href="javascript:void(0);">terms of usage</a>.</label>
                    </div>

                    <button class="btn btn-block btn-lg bg-pink waves-effect" name="sighUp" type="submit">SIGN UP</button>

                    <div class="m-t-25 m-b--5 align-center">
                        <a href="index">You already have a membership?</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Jquery Core Js -->
    <script src="plugins/jquery/jquery.min.js"></script>

    <!-- Bootstrap Core Js -->
    <script src="plugins/bootstrap/js/bootstrap.js"></script>

    <!-- Select Plugin Js -->
    <script src="plugins/bootstrap-select/js/bootstrap-select.js"></script>
    <!-- Toast Plugin Js -->
    <!-- <script type="text/javascript" src="js/jquery.toast.js"></script> -->
    <script src="plugins/bootstrap-notify/bootstrap-notify.js"></script>
    <!-- Waves Effect Plugin Js -->
    <script src="plugins/node-waves/waves.js"></script>

    <!-- Validation Plugin Js -->
    <script src="plugins/jquery-validation/jquery.validate.js"></script>

    <!-- Select Plugin Js -->
    <script src="plugins/bootstrap-select/js/bootstrap-select.js"></script>

    <!-- Moment Plugin Js -->
    <script src="plugins/momentjs/moment.js"></script>

    <!-- Bootstrap Datepicker Plugin Js -->
    <script src="plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>

    <!-- Autosize Plugin Js -->
    <script src="plugins/autosize/autosize.js"></script>

    <!-- Custom Js -->
    <!-- <script src="js/admin.js"></script> -->
    <script src="js/pages/examples/sign-up.js"></script>
    <script type="text/javascript">
        $('[name="txtGraduate"]').selectpicker();
        $('[name="slctUserType"]').selectpicker();
        function showNotification(colorName, text, placementFrom, placementAlign, animateEnter, animateExit) {
            if (colorName === null || colorName === '') { colorName = 'bg-black'; }
            if (text === null || text === '') { text = 'Turning standard Bootstrap alerts'; }
            if (animateEnter === null || animateEnter === '') { animateEnter = 'animated fadeInDown'; }
            if (animateExit === null || animateExit === '') { animateExit = 'animated fadeOutUp'; }
            var allowDismiss = true;

            $.notify({
                message: text
            },
                {
                    type: colorName,
                    allow_dismiss: allowDismiss,
                    newest_on_top: true,
                    timer: 2000,
                    placement: {
                        from: placementFrom,
                        align: placementAlign
                    },
                    animate: {
                        enter: animateEnter,
                        exit: animateExit
                    },
                    template: '<div data-notify="container" class="bootstrap-notify-container alert alert-dismissible {0} ' + (allowDismiss ? "p-r-35" : "") + '" role="alert">' +
                    '<button type="button" aria-hidden="true" class="close" data-notify="dismiss">×</button>' +
                    '<span data-notify="icon"></span> ' +
                    '<span data-notify="title">{1}</span> ' +
                    '<span data-notify="message">{2}</span>' +
                    '<div class="progress" data-notify="progressbar">' +
                    '<div class="progress-bar progress-bar-{0}" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>' +
                    '</div>' +
                    '<a href="{3}" target="{4}" data-notify="url"></a>' +
                    '</div>'
                });
        }
    </script>
    <?php 
        if(isset($_SESSION['error'])){
           echo "<script>
                showNotification('alert-danger', '".$_SESSION['error']."', 'top', 'center', '', '');
            </script>";
            session_destroy();
        }
        if(isset($_SESSION['success'])){
            echo "<script>
            showNotification('alert-success', '".$_SESSION['success']."', 'top', 'center', '', '');
            </script>";
            session_destroy();
        }
        if(isset($_SESSION['warning'])){
           echo "<script>
            showNotification('alert-warning', '".$_SESSION['warning']."', 'top', 'center', '', '');
            </script>";
            session_destroy();
        }
        if(isset($_SESSION['info'])){
           echo "<script>
            showNotification('alert-info', '".$_SESSION['info']."', 'top', 'center', '', '');
            </script>";
            session_destroy();
        }
    ?>
</body>

</html>