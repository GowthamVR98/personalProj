<?php
    if (!session_id() ) {
		session_start();
	}
    if(isset($_SESSION['SESS_ADMIN_ID']) && (trim($_SESSION['SESS_ADMIN_ID']) != '')) {
		header("location: dashboard");
		exit(0);
	}
    include_once 'includes/admin-config.php';
    if(isset($_POST['txtpassword']) && isset($_POST['txtusername'])){
        $dbobj = new GetCommomOperation();
        $values = array(trim($_POST['txtusername']));
        $result = $dbobj->selectData($dbh, TBL_PRIFIX.'user_login_details', array('user_id','concat_ws(" ",fname ,lname) as userName','password','approval_status','super_admin'), 'email_id = ?', $values);
        if($result['total'] > 0){
            $verify = password_verify(trim($_POST['txtpassword']),$result['values'][0]['password']);
            if($verify == true){
                if($result['values'][0]['super_admin'] == 'yes'){
                     $_SESSION['SESS_ADMIN_ID'] = $result['values'][0]['user_id'];
                    $_SESSION['SESS_ADMIN_NAME'] = $result['values'][0]['userName'];
                    header('location:dashboard');
                    exit(0);
                }else{
                    if($result['values'][0]['approval_status'] == 'pending'){
                        $_SESSION['warning'] = 'Your account not approved yet. Please wait till approval process complete.';
                        header('location:index');
                        exit(0);
                    }else if($result['values'][0]['approval_status'] == 'rejected'){
                        $_SESSION['warning'] = 'Your account has been rejected. Please contact admin for more information.';
                        header('location:index');
                        exit(0);
                    }else if($result['values'][0]['approval_status'] == 'approved'){
                        $_SESSION['SESS_ADMIN_ID'] = $result['values'][0]['user_id'];
                        $_SESSION['SESS_ADMIN_NAME'] = $result['values'][0]['userName'];
                        header('location:dashboard');
                        exit(0);
                    }
                }
            }else{
                $_SESSION['error'] = 'Invalid password';
                header('location:index');
                exit(0);
            }
        }else{
            $_SESSION['error'] = 'Invalid username';
            header('location:index');
            exit(0);
        }
    }
    
?>
<!DOCTYPE html>
<html>
<head>
    <?php include (ALUM_TEMPLATES.'metatag.php');?> 
    <link href="css/auth-light.css" rel="stylesheet" />
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
</head>
<body class="login showMsg">
    <div class="content login_form">
        <form action="" method="Post">
            <div class=" col-md-12 login-body">
                <div class="col-md-5 login-wish">
                    <div class="wish-blk">
                        <h3>Welcome to AlumniPortal</h3>
                        <span>Technology. Applied to Life</span>
                        <div class="social-auth-hr"></div>
                        <div class="wish-icon">
                            <?php
                            date_default_timezone_set("Asia/Kolkata");
                            $time = date("H");
                                if ($time < 12) {
                                echo '<i class="fa fa-cloud-sun morng-icon"></i><h4>Good Morning</h4>';
                                } else if ($time >= 12 && $time < 17) { /* If the time is grater than or equal to 1200 hours, but less than 1700 hours, so good afternoon */
                                    echo '<i class="material-icons NotVisiblePaassword" style="font-size:60px;">wb_sunny</i><h4>Good Afternoon</h4>';
                                } else if ($time >= 17 && $time < 19) { /* Should the time be between or equal to 1700 and 1900 hours, show good evening */
                                    echo '<i class="fa fa-mug-hot morng-icon"></i><h4>Good evening</h4>';
                                } else if ($time >= 19) { /* Finally, show good night if the time is greater than or equal to 1900 hours */
                                    echo '<i class="fa fa-cloud-moon morng-icon"></i><h4>Good Night</h4>';
                                }
                            ?>
                        </div>
                    </div>
                </div>
                <div class=" col-md-6 login-blk">
                    <div class="login-blk-cell">
                        <h2 class="login-title"><img src="images/Alumni-Logo.png" width="200px"></h2>
                        <div class="social-auth-hr">
                            <span>Please Login</span>
                        </div>
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="material-icons">person</i>
                            </span>
                            <div class="form-line">
                                <input type="text" class="form-control" name="txtusername" placeholder="Username" required autofocus>
                            </div>
                        </div>
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="material-icons">lock</i>
                            </span>
                            <div class="form-line">
                                <input type="password" class="form-control" name="txtpassword" placeholder="Password" required>
                            </div>
                                <span class="input-group-addon">
                                    <i class="material-icons NotVisiblePaassword" style="cursor: pointer; color: rgb(179, 174, 174);">visibility_off</i>
                                    <i class="material-icons VisiblePaassword" style="cursor: pointer; display:none;color: rgb(179, 174, 174);">visibility</i>
                                </span>
                        </div>
                        <div class="col-xs-12">
                            <button name="login" class="btn btn-block bg-pink waves-effect" type="submit">SIGN IN</button>
                        </div>
                        <div class="clearfix"></div>
                        <div class="row m-t-15 m-b--20">
                            <div class="col-xs-6">
                                <a href="alumni-registration.php">Register Now!</a>
                            </div>
                            <div class="col-xs-6 align-right">
                                <a href="forgot-password.html">Forgot Password?</a>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        </div>
                    </div>
            </div><div class="clearfix"></div>
        </form>
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

    <!-- Bootstrap Material Datetime Picker Plugin Js -->
    <script src="plugins/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js"></script>
    
    <!-- Bootstrap Datepicker Plugin Js -->
    <script src="plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>

    <!-- Autosize Plugin Js -->
    <script src="plugins/autosize/autosize.js"></script>

    <!-- Custom Js -->
    <script src="js/admin.js"></script>
    <script src="js/pages/forms/basic-form-elements.js"></script>
    <script src="js/pages/examples/sign-up.js"></script>
    <script>
        $('.NotVisiblePaassword').click(function(){
            $(this).hide().parent().parent().find('.VisiblePaassword').show().parent().parent().find('input').attr('type','text');
        });
        $('.VisiblePaassword').click(function(){
            $(this).hide().parent().parent().find('.NotVisiblePaassword').show().parent().parent().find('input').attr('type','password');
        });
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
                    timer: 1000,
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
        }else if(isset($_SESSION['warning'])){
            echo "<script>
            showNotification('alert-warning', '".$_SESSION['warning']."', 'top', 'center', '', '');
            </script>";
            unset($_SESSION['warning']);
        }
    ?>
</body>

</html>