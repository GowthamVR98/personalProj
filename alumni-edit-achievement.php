<?php
include 'includes/admin-config.php';
include_once 'auth.php';
$dbobj = new GetCommomOperation();
$loginSessionId = $dbobj->getSessionId();
if(isset($_GET['achievementId'])){
    include 'includes/emp-crypto-graphy.php';
    $SafeCrypto = new SafeCrypto();
    $id = $SafeCrypto->decrypt(trim($_GET['achievementId']));
    $exists = $dbobj->selectData($dbh, TBL_PRIFIX.'achievement_details', array('title','attachement_file','description'), 'achievement_id = ?', array($id));
    if($exists['total'] > 0){
        $data = $exists['values'][0];
    }else{
        $_SESSION['error'] = 'No record found.';
        header('location:alumni-add-achievements');
        exit(0);
    }
}else{
    $_SESSION['error'] = 'Invalid url.';
    header('location:alumni-add-achievements.php');
    exit(0);
}
if(isset($_POST['btnskill'])){
     if(!empty($_FILES['txtLogo']['name'])){
            $fileUpload = $dbobj->fileUpload(ALUM_ABS_PATH.ALUM_COMMON_UPLOADS.ALUM_ACHIEVEMENT, $_FILES['txtLogo']);
        }else{
             $fileUpload = $data['attachement_file'];
        }
    $update = $dbobj->UpdatetData($dbh, TBL_PRIFIX.'achievement_details', 'title = ?,attachement_file = ?,description = ?,modified_date = ?, modified_by = ?, modified_ip = ?', 'achievement_id = ?', 
        array(
            trim($_POST['txtTitle']),
            $fileUpload,
            trim($_POST['txtDescription']),
            CURRENT_DT,
            $loginSessionId,
            $_SERVER['REMOTE_ADDR'],$id
        )
    );
    if($update){ 
        $_SESSION['success'] = 'Attachment has been updated successfully.';
        header('location:alumni-edit-achievement.php?achievementId='.$SafeCrypto->encrypt($id));
        exit(0);
    }else{
        $_SESSION['warning'] = 'No changes Done.';
        header('location:alumni-edit-achievement.php?achievementId='.$SafeCrypto->encrypt($id));
        exit(0);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <?php include (ALUM_TEMPLATES.'metatag.php');?>
    <link rel="stylesheet" href="css/fullcalendar.min.css" />
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
                                <h2>Edit Achievement</h2>
                                <ul class="header-dropdown m-r--5">
                                    <li class="dropdown">
                                        <button class="btn btn-warning waves-effect" onclick="location.href='alumni-add-achievements'" type="button">Back</button>
                                        <button class="btn btn-primary waves-effect" name="btnskill" type="submit">Update</button>
                                    </li>
                                </ul>
                            </div>
                            <div class="body">
                                <!-- Inline Layout -->
                                <div class="form-group form-float clearfix">
                                    <div class="form-line">
                                        <input type="text" class="form-control" name="txtTitle" value="<?php echo (!empty($data['title']) ? $data['title'] : '');?>" required maxlength="60">
                                        <label class="form-label">Title</label>
                                    </div>
                                </div>
                                <div class="form-group form-float">
                                        <label class="form-label">Upload attachement</label>
                                    <div class="form-line">
                                        <input type="file"  class="form-control" name="txtLogo" value="<?php echo (!empty($data['attachement_file']) ? $data['attachement_file'] : '');?>">
                                    </div>
                                    <?php 
                                        if(!empty($data['attachement_file'])){
                                            echo '<a href="'.(!empty($data['attachement_file']) ? ALUM_WS_UPLOADS.ALUM_ACHIEVEMENT.$data['attachement_file'] : ALUM_WS_IMAGES.'Alumni-Logo.png').'" target="_blank"><span>View Attachment</span></a>'; 
                                        }
                                    ?>
                                </div>
                                <div class="form-group form-float">
                                    <div class="form-line">
                                        <textarea class="form-control" name="txtDescription" required maxlength="360"><?php echo (!empty($data['description']) ? $data['description'] : '');?></textarea>
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
</body>
</html>
