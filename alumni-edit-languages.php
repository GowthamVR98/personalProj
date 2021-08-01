<?php
include 'includes/admin-config.php';
include_once 'auth.php';
$dbobj = new GetCommomOperation();
if(isset($_GET['languageId'])){
    include 'includes/emp-crypto-graphy.php';
    $SafeCrypto = new SafeCrypto();
    $id = $SafeCrypto->decrypt(trim($_GET['languageId']));
    $exists = $dbobj->selectData($dbh, TBL_PRIFIX.'language_master', array('language'), 'language_id = ?', array($id));
    if($exists['total'] > 0){
        $data = $exists['values'][0]['language'];
    }else{
        $_SESSION['error'] = 'No record found.';
        header('location:alumni-add-language');
        exit(0);
    }
}else{
    $_SESSION['error'] = 'Invalid url.';
    header('location:alumni-add-language.php');
    exit(0);
}
if(isset($_POST['btnskill'])){
    $checkExists = $dbobj->check_exist($dbh, TBL_PRIFIX.'language_master',array('language_id'), 'language = ?', array(trim($_POST['txtlanguage'])),$id,true);
    if($checkExists){
        $_SESSION['error'] = 'Language already exist.';
        header('location:alumni-edit-languages.php?languageId='.$SafeCrypto->encrypt($id));
        exit(0);
    }else{
        $update = $dbobj->UpdatetData($dbh, TBL_PRIFIX.'language_master', 'language = ?', 'language_id = ?', array(trim($_POST['txtSkill']),$id));
        if($update){
            $_SESSION['success'] = 'language has been updated successfully.';
            header('location:alumni-edit-languages.php?languageId='.$SafeCrypto->encrypt($id));
            exit(0);
        }else{
            $_SESSION['warning'] = 'No changes Done.';
            header('location:alumni-edit-languages.php?languageId='.$SafeCrypto->encrypt($id));
            exit(0);
        }
        
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
                        <form id="form_validation" method="POST">
                            <div class="header">
                                <h2>Edit Language</h2>
                                <ul class="header-dropdown m-r--5">
                                    <li class="dropdown">
                                        <button class="btn btn-warning waves-effect" onclick="location.href='alumni-add-language'" type="button">Back</button>
                                        <button class="btn btn-primary waves-effect" name="btnskill" type="submit">Update</button>
                                    </li>
                                </ul>
                            </div>
                            <div class="body">
                                <!-- Inline Layout -->
                                <div class="form-group form-float clearfix">
                                    <div class="form-line">
                                        <input type="text" class="form-control" name="txtSkill" value="<?php echo $data; ?>" required maxlength="60">
                                        <label class="form-label">Enter Language Name</label>
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
