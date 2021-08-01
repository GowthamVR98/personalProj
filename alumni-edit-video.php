<?php
include 'includes/admin-config.php';
include_once 'auth.php';
$dbobj = new GetCommomOperation();
$loginSessionId = $dbobj->getSessionId();
if(isset($_GET['VideoId'])){
    include 'includes/emp-crypto-graphy.php';
    $SafeCrypto = new SafeCrypto();
    $id = $SafeCrypto->decrypt(trim($_GET['VideoId']));
    $exists = $dbobj->selectData($dbh, TBL_PRIFIX.'video_details', array('title','youtub_link','description'), 'video_id = ?', array($id));
    if($exists['total'] > 0){
        $data = $exists['values'][0];
    }else{
        $_SESSION['error'] = 'No record found.';
        header('location:alumni-add-videos');
        exit(0);
    }
}else{
    $_SESSION['error'] = 'Invalid url.';
    header('location:alumni-add-achievements.php');
    exit(0);
}
if(isset($_POST['btnskill'])){
    $update = $dbobj->UpdatetData($dbh, TBL_PRIFIX.'video_details', 'title = ?,youtub_link = ?,description = ?,modified_date = ?, modified_by = ?, modified_ip = ?', 'video_id = ?', 
        array(
            trim($_POST['txtTitle']),
            trim($_POST['txtLink']),
            trim($_POST['txtDescription']),
            CURRENT_DT,
            $loginSessionId,
            $_SERVER['REMOTE_ADDR'],$id
        )
    );
    if($update){ 
        $_SESSION['success'] = 'Video has been updated successfully.';
        header('location:alumni-edit-video.php?VideoId='.$SafeCrypto->encrypt($id));
        exit(0);
    }else{
        $_SESSION['warning'] = 'No changes Done.';
        header('location:alumni-edit-video.php?VideoId='.$SafeCrypto->encrypt($id));
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
                                <h2>Edit Video</h2>
                                <ul class="header-dropdown m-r--5">
                                    <li class="dropdown">
                                        <button class="btn btn-warning waves-effect" onclick="location.href='alumni-add-videos'" type="button">Back</button>
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
                                        <label class="form-label">YouTube Link</label>
                                    <div class="form-line">
                                        <input type="url"  class="form-control" name="txtLink" value="<?php echo (!empty($data['youtub_link']) ? $data['youtub_link'] : '');?>">
                                        <label class="form-label">YouTube Link</label>
                                    </div>
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
