<?php
include 'includes/admin-config.php';
include_once 'auth.php';
$dbobj = new GetCommomOperation();

if(isset($_GET['ReunionId'])){
    include 'includes/emp-crypto-graphy.php';
    $SafeCrypto = new SafeCrypto();
    $id = $SafeCrypto->decrypt(trim($_GET['ReunionId']));
    $exists = $dbobj->selectData($dbh, TBL_PRIFIX.'reunion_details', array('title', 'location', 'start_date', 'start_time', 'end_date', 'end_time',
                 'reunion_logo','description'), 'reunion_id = ?', array($id));
    if($exists['total'] > 0){
        $data = $exists['values'][0];
    }else{
        $_SESSION['error'] = 'No record found.';
        header('location:alumni-add-reunion');
        exit(0);
    }
}else{
    $_SESSION['error'] = 'Invalid url.';
    header('location:alumni-add-reunion');
    exit(0);
}
    if(isset($_POST['btnReunion'])){
        $loginSessionId = $dbobj->getSessionId();
         if(!empty($_FILES['txtImage']['name'])){
            $fileUpload = $dbobj->fileUpload(ALUM_ABS_PATH.ALUM_COMMON_UPLOADS.ALUM_RUNION, $_FILES['txtImage']);
        }else{
             $fileUpload = $data['reunion_logo'];
        }
        $values = array(trim($_POST['txtTitle']),trim($_POST['txtLocation']),
                        trim(SystemDate::date($_POST['txtStartDate'])),trim(SystemDate::time($_POST['txtStartTime'])),
                        trim(SystemDate::date($_POST['txtEndDate'])),trim(SystemDate::time($_POST['txtEndTime'])),$fileUpload,
                        trim($_POST['txtDescription']),CURRENT_DT,$loginSessionId,$_SERVER['REMOTE_ADDR'],$id);
        $result = $dbobj->UpdatetData($dbh, TBL_PRIFIX.'reunion_details', 
            'title = ?,location = ?,start_date = ?,start_time = ?,end_date = ?, end_time = ?,
                 reunion_logo = ?,description = ?, modified_date = ?, modified_by = ?, modified_ip = ?','reunion_id = ?', $values);
        if($result){
            // include_once ALUM_ETEMPLATES.'job-details-tpl.php';
            $_SESSION['success'] = 'Reunion has been updated successfully.';
            header('location:alumni-edit-reunion.php?ReunionId='.$_GET['ReunionId']);
            exit(0);
        }else{
            $_SESSION['error'] = 'Something went to wrong.';
            header('location:alumni-edit-reunion.php?ReunionId='.$_GET['ReunionId']);
            exit(0);
        }
    }
?>
<!DOCTYPE html>
<html>
<head>
    <?php include (ALUM_TEMPLATES.'metatag.php');?>
    <!-- Bootstrap Material Datetime Picker Css -->
    <link href="plugins/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css" rel="stylesheet" />
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
                        <form id="form_validation" method="POST" enctype="multipart/form-data">
                            <div class="header">
                                <h2>Edit Reunion</h2>
                                <ul class="header-dropdown m-r--5">
                                    <li class="dropdown">
                                        <button class="btn btn-warning waves-effect" onclick="location.href='alumni-add-reunion'" type="button">Back</button>
                                        <button class="btn btn-primary waves-effect" name="btnReunion" type="submit" title="Update">Update</button>
                                    </li>
                                </ul>
                            </div>
                            <div class="body">
                                <!-- Inline Layout -->
                                <div class="form-group form-float">
                                    <div class="form-line">
                                        <input type="text" class="form-control"  title="Title" name="txtTitle" value="<?php echo (!empty($data['title']) ? $data['title'] : '');?>" required>
                                        <label class="form-label">Title</label>
                                    </div>
                                </div>
                                <div class="form-group form-float">
                                    <div class="form-line">
                                        <input type="text" class="form-control" value="<?php echo (!empty($data['location']) ? $data['location'] : '');?>"  title="Location" name="txtLocation" required>
                                        <label class="form-label">Location</label>
                                    </div>
                                </div>
                                <div class="form-group form-float">
                                    <div class="form-line" id="bs_datepicker_container">
                                        <input type="text" class="form-control" readonly  value="<?php echo (!empty($data['start_date']) ? AppDate::date($data['start_date']) : '');?>" title="Start Date" name="txtStartDate" required>
                                        <label class="form-label">Start Date</label>
                                    </div>
                                </div>
                                <div class="form-group form-float">
                                    <div class="form-line">
                                        <input type="text" class="form-control timepicker" readonly  value="<?php echo (!empty($data['start_time']) ? $data['start_time'] : '');?>" title="Start Time" name="txtStartTime" required>
                                        <label class="form-label">Start Time</label>
                                    </div>
                                </div>
                                <div class="form-group form-float">
                                    <div class="form-line" id="bs_datepicker_container">
                                        <input type="text" class="form-control" readonly  value="<?php echo (!empty($data['end_date']) ? AppDate::date($data['end_date']) : '');?>" title="End Date" name="txtEndDate" required>
                                        <label class="form-label">End Date</label>
                                    </div>
                                </div>
                                <div class="form-group form-float">
                                    <div class="form-line">
                                        <input type="text" class="form-control timepicker" readonly  value="<?php echo (!empty($data['end_time']) ? $data['end_time'] : '');?>" title="End Time" name="txtEndTime" required>
                                        <label class="form-label">End Time</label>
                                    </div>
                                </div>
                                 <div class="form-group form-float">
                                        <label class="form-label">Upload Image</label>
                                    <div class="form-line">
                                        <input type="file" class="form-control" name="txtImage">
                                    </div>
                                     <?php 
                                        if(!empty($data['reunion_logo'])){
                                            echo '<a href="'.(!empty($data['reunion_logo']) ? ALUM_WS_UPLOADS.ALUM_RUNION.$data['reunion_logo']: ALUM_WS_IMAGES.'Alumni-Logo.png').'" target="_blank"><span>View Attachment</span></a>'; 
                                        }
                                    ?>
                                </div>
                                <div class="form-group form-float">
                                    <div class="form-line">
                                        <textarea class="form-control"  title="Description" name="txtDescription" required><?php echo (!empty($data['description']) ? $data['description'] : '');?></textarea>
                                        <label class="form-label">Description</label>
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
   <!-- Bootstrap Material Datetime Picker Plugin Js -->
    <script src="plugins/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js"></script>
    <script>
        $('#bs_datepicker_container input').datepicker({
            format: 'dd-mm-yyyy',
            autoclose: true,
            container: '#bs_datepicker_container'
        });
        $('.timepicker').bootstrapMaterialDatePicker({
            format: 'HH:mm',
            clearButton: true,
            date: false
        });
    </script>
</body>
</html>