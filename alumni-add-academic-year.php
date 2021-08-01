<?php
include 'includes/admin-config.php';
include_once 'auth.php';
include 'includes/emp-crypto-graphy.php';
$dbobj = new GetCommomOperation();
$SafeCrypto = new SafeCrypto();
if (isset($_POST['btnsAcdemic'])) {
    $checkExists = $dbobj->check_exist($dbh, TBL_PRIFIX . 'academic_year_master', array('academic_id'), 'academic_name = ?', array(trim($_POST['txtAcademicName'])));
    if ($checkExists) {
        $_SESSION['error'] = 'Academic year already exist.';
        header('location:alumni-add-academic-year');
        exit(0);
    } else {
        $loginSessionId = $dbobj->getSessionId();
        $values = array(trim($_POST['txtAcademicName']), trim($_POST['txtAcademicYear']), trim(SystemDate::date($_POST['txtStart'])), trim(SystemDate::date($_POST['txtEnd'])), CURRENT_DT, $loginSessionId, $_SERVER['REMOTE_ADDR']);
        $result = $dbobj->InsertQuery($dbh, TBL_PRIFIX . 'academic_year_master', array('academic_name', 'academic_year', 'academic_start', 'academic_end', 'added_date', 'added_by', 'added_ip'), $values);
        if ($result['status']) {
            $_SESSION['success'] = 'New academic year has been added successfully.';
            header('location:alumni-add-academic-year');
            exit(0);
        } else {
            $_SESSION['error'] = 'Something went to wrong.';
            header('location:alumni-add-academic-year');
            exit(0);
        }
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?php include (ALUM_TEMPLATES . 'metatag.php'); ?>
        <link href="plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" />
        <link href="plugins/bootstrap-datepicker/css/bootstrap-datepicker.css" rel="stylesheet" />
        <style>
            #captcha {
                width: 50%;

                position: absolute;
                left: 50%;
                top: 50%;

                padding: 15px;

                border: 2px solid #999;
                outline: 0;

                color: #666;
                font-size: 17px;

                transform:  translateX(-50%)
                    translateY(-50%);

                transition: all 0.3s ease;
            }
            #captcha.correct {
                border-color: #009688;
            }
        </style>
    </head>

    <body class="theme-red">
        <?php
        // <!-- Page Loader -->
        include ALUM_TEMPLATES . 'loader.php';
        // <!-- #END# Page Loader -->
        // <!-- Top Bar -->
        include ALUM_TEMPLATES . 'top-navigation.php';
        // <!-- #Top Bar -->
        // <!-- Left Sidebar -->
        include ALUM_TEMPLATES . 'left-links.php';
        // <!-- #Left Sidebar -->
        ?>
        <section class="content">
            <div class="container-fluid">
                <div class="block-header">
                    <h2>
                        <button class="btn btn-primary waves-effect pull-right" id="ShowHide" type="submit">Add Academic Year</button>
                        <button class="btn btn-primary waves-effect pull-right" id="hideOptionBlock" type="button" style="display:none;">Hide</button>
                    </h2>
                    <div class="clearfix"></div>
                </div>
                <!-- Calender -->
                <div class="row clearfix"  id="hideShow">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="card">
                            <form id="form_validation" method="POST">
                                <div class="header">
                                    <h2>Add Academic Year</h2>
                                    <ul class="header-dropdown m-r--5">
                                        <li class="dropdown">
                                            <button class="btn btn-primary waves-effect" name="btnsAcdemic" type="submit">SUBMIT</button>
                                        </li>
                                    </ul>
                                </div>
                                <div class="body">
                                    <!-- Inline Layout -->
                                    <div class="col-sm-4">
                                        <div class="form-group form-float clearfix">
                                            <div class="form-line">
                                                <input type="text" class="form-control" name="txtAcademicName" required maxlength="60">
                                                <label class="form-label">Enter Academic Name</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group form-float clearfix">
                                            <div class="form-line" id="bs_year">
                                                <input type="text" class="form-control" name="txtAcademicYear" placeholder="Select Academic Year" required maxlength="60">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group form-float clearfix">
                                            <div class="form-line" id="bs_datepicker_container">
                                                <input type="text" Placeholder="Academic Year Start Date" class="form-control" required readonly title="Academic Year Start Date" name="txtStart">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group form-float clearfix">
                                            <div class="form-line" id="bs_datepicker_container">
                                                <input type="text" Placeholder="Academic Year End Date" class="form-control" required readonly title="Academic Year End Date" name="txtEnd">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                    <!-- #END# Inline Layout -->
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <!-- #END# Calender -->
                <!-- Basic Examples -->
                <div class="row clearfix">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="card">
                            <div class="header">
                                <h2>
                                    Manage Academic Year
                                </h2>
                                <input type="text" id="captcha">
                                <ul class="header-dropdown m-r--5">
                                    <li class="dropdown">
                                        <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                            <i class="material-icons">more_vert</i>
                                        </a>
                                        <ul class="dropdown-menu pull-right">
                                            <li><a href="javascript:void(0);">Action</a></li>
                                            <li><a href="javascript:void(0);">Another action</a></li>
                                            <li><a href="javascript:void(0);">Something else here</a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                            <div class="body">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped table-hover js-basic-example dataTable">
                                        <thead>
                                            <tr>
                                                <th>Academic Name</th>
                                                <th>Academic Year</th>
                                                <th>Start Date</th>
                                                <th>End Date</th>
                                                <th>Date Added</th>
                                                <th>Added By</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $sql = "SELECT concat_ws(' ',T1.fname,T1.lname) as Name,T2.academic_id,T2.academic_name,T2.academic_year,T2.academic_start,T2.academic_end,T2.status,T2.added_date,T2.added_by FROM " . TBL_PRIFIX . "academic_year_master as T2 LEFT JOIN  " . TBL_PRIFIX . "user_login_details as T1 ON T1.user_id = T2.added_by ORDER BY T2.academic_id ASC";
                                            $stmt = $dbh->prepare($sql);
                                            $stmt->execute();
                                            $rowCount = $stmt->rowCount();
                                            if ($rowCount > 0) {
                                                for ($i = 1; $i <= $rowCount; $i++) {
                                                    $result['values'][] = $stmt->fetch(PDO::FETCH_ASSOC);
                                                }
                                                foreach ($result['values'] as $row) {
                                                    echo '<tr>';
                                                    echo '<td>' . $row['academic_name'] . '</td>';
                                                    echo '<td>' . $row['academic_year'] . '</td>';
                                                    echo '<td>' . AppDate::date($row['academic_start']) . '</td>';
                                                    echo '<td>' . AppDate::date($row['academic_end']) . '</td>';
                                                    echo '<td>' . AppDate::date($row['added_date']) . '</td>';
                                                    echo '<td>' . $row['Name'] . '</td>';
                                                    echo '<td style="text-align:center;">
                                                    ' . ($row['status'] == 1 ? '<a href="javascript:void(0);" class="ManageAction" id="disable_' . $row['academic_id'] . '"><i class="material-icons" title = "Disable" style="font-size:21px;">block</i></a>' : '<a href="javascript:void(0);" class="ManageAction" id="enable_' . $row['academic_id'] . '"><i class="material-icons" style="font-size:21px;" title="Enable">check_circle</i></a>') . '
                                                    <a href="alumni-edit-academic-year.php?academicId=' . $SafeCrypto->encrypt($row['academic_id']) . '"><i class="material-icons" title="Edit" id="' . $row['academic_id'] . '" style=" font-size:21px;">edit</i></a>';
                                                    $exists = $dbobj->selectData($dbh, TBL_PRIFIX . 'user_login_details', array('user_id'), 'academic_id = ?', array($row['academic_id']));
                                                    if ($exists['total'] == 0) {
                                                        echo '<a href="javascript:void(0);" class="ManageAction" id="delete_' . $row['academic_id'] . '"><i class="material-icons" title="Delete" style="color:#e21313; font-size:21px;">delete</i></a>';
                                                    }
                                                    echo '</td>';
                                                    echo '</tr>';
                                                }
                                            } else {
                                                
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- #END# Basic Examples -->
            </div>
        </section>
        <?php include ALUM_TEMPLATES . 'footer.php'; ?> 
        <script src="plugins/bootstrap-select/js/bootstrap-select.js"></script>
        <script src="plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
        <script>

            $(document).ready(function () {
                initCaptcha();

                setInterval(function () {
                    initCaptcha();
                }, 10000);
            });

            function initCaptcha() {
                var captcha = generateCaptcha(),
                    captchaAns = eval(captcha);
                $("#captcha").attr("placeholder", captcha + " = ")
                        .on("keyup", function () {
                            if ($(this).val() !== "" && $(this).val() == captchaAns)
                                $(this).addClass("correct");
                            else
                                $(this).removeClass("correct");
                        });
            }

            function generateCaptcha() {
                var randomNo = function (n) {
                    return Math.floor(Math.random() * n + 1);
                }

                var randomOp = function () {
                    return "+-*"[randomNo(3) - 1];
                }
                return randomNo(10) + " " + randomOp() + " " + randomNo(10);
            }







            $(document).ready(function () {
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
                $('.ManageAction').on('click', function () {
                    var id = $(this).attr('id');
                    var Splitvalue = id.split('_');
                    if (confirm('Are you sure you want ' + Splitvalue[0] + '?')) {
                        $.ajax({
                            type: 'POST',
                            url: 'ajax/ajax-manage-academic-year.php',
                            data: Splitvalue[0] + '=' + Splitvalue[1],
                            success: function (response) {
                                window.location.reload();
                            }
                        });
                    }
                });
            });
        </script>
    </body>

</html>
