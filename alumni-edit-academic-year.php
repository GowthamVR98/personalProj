<?php
include 'includes/admin-config.php';
include_once 'auth.php';
$dbobj = new GetCommomOperation();
if(isset($_GET['academicId'])){
    include 'includes/emp-crypto-graphy.php';
    $SafeCrypto = new SafeCrypto();
    $id = $SafeCrypto->decrypt(trim($_GET['academicId']));
    $exists = $dbobj->selectData($dbh, TBL_PRIFIX.'academic_year_master', array('academic_name','academic_year','academic_start','academic_end'), 'academic_id = ?', array($id));
    if($exists['total'] > 0){
        $getData = $exists['values'][0];
    }else{
        $_SESSION['error'] = 'No record found.';
        header('location:alumni-add-academic-year');
        exit(0);
    }
}else{
    $_SESSION['error'] = 'Invalid url.';
    header('location:alumni-add-academic-year.php');
    exit(0);
}
if(isset($_POST['btnskill'])){
    $checkExists = $dbobj->check_exist($dbh, TBL_PRIFIX.'academic_year_master',array('academic_id'), 'academic_name = ?', array(trim($_POST['txtSkill'])),$id,true);
    if($checkExists){
        $_SESSION['error'] = 'Academic year already exist.';
        header('location:alumni-edit-academic-year.php?academicId='.$SafeCrypto->encrypt($id));
        exit(0);
    }else{
        $update = $dbobj->UpdatetData($dbh, TBL_PRIFIX.'academic_year_master', 'academic_name = ?,academic_year = ?,academic_start = ?,academic_end = ?', 'academic_id = ?', array(trim($_POST['txtAcademicName']),trim($_POST['txtAcademicYear']),trim(SystemDate::date($_POST['txtStart'])),trim(SystemDate::date($_POST['txtEnd'])),$id));
        if($update){
            $_SESSION['success'] = 'Academic year has been updated successfully.';
            header('location:alumni-edit-academic-year.php?academicId='.$SafeCrypto->encrypt($id));
            exit(0);
        }else{
            $_SESSION['warning'] = 'No changes done.';
            header('location:alumni-edit-academic-year.php?academicId='.$SafeCrypto->encrypt($id));
            exit(0);
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <?php include (ALUM_TEMPLATES.'metatag.php');?>
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
            <!-- Calender -->
            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <form id="form_validation" method="POST">
                            <div class="header">
                                <h2>Edit Academic Year</h2>
                                <ul class="header-dropdown m-r--5">
                                    <li class="dropdown">
                                        <button class="btn btn-warning waves-effect" onclick="location.href='alumni-add-academic-year'" type="button">Back</button>
                                        <button class="btn btn-primary waves-effect" name="btnskill" type="submit">Update</button>
                                    </li>
                                </ul>
                            </div>
                            <div class="body">
                                <!-- Inline Layout -->
                                <div class="col-sm-4">
                                    <div class="form-group form-float clearfix">
                                        <div class="form-line">
                                            <input type="text" class="form-control" name="txtAcademicName" value="<?php echo $getData['academic_name']; ?>" required maxlength="60">
                                            <label class="form-label">Enter Academic Name</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group form-float clearfix">
                                        <div class="form-line" id="bs_year">
                                            <input type="text" value="<?php echo $getData['academic_year']; ?>" class="form-control" name="txtAcademicYear" placeholder="Select Academic Year" required maxlength="60">
                                        </div>
                                    </div>
                                </div>
                                 <div class="col-sm-4">
                                    <div class="form-group form-float clearfix">
                                        <div class="form-line" id="bs_datepicker_container">
                                            <input type="text" value="<?php echo AppDate::date($getData['academic_start']); ?>" Placeholder="Academic Year Start Date" class="form-control" required readonly title="Academic Year Start Date" name="txtStart">
                                        </div>
                                    </div>
                                </div>
                                 <div class="col-sm-4">
                                    <div class="form-group form-float clearfix">
                                        <div class="form-line" id="bs_datepicker_container">
                                            <input type="text" value="<?php echo AppDate::date($getData['academic_end']); ?>" Placeholder="Academic Year End Date" class="form-control" required readonly title="Academic Year End Date" name="txtEnd">
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
        </div>
    </section>
    <?php include ALUM_TEMPLATES.'footer.php';?> 
    <script src="plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
    <script>
        $(document).ready(function(){
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