<?php
include_once 'auth.php';
include 'includes/admin-config.php';
$dbobj = new GetCommomOperation();
if(isset($_GET['companyId'])){
    include 'includes/emp-crypto-graphy.php';
    $SafeCrypto = new SafeCrypto();
    $id = $SafeCrypto->decrypt(trim($_GET['companyId']));
    $exists = $dbobj->selectData($dbh, TBL_PRIFIX.'company_master', array('company_name','headquarter','logo','website_url'), 'company_id = ?', array($id));
    if($exists['total'] > 0){
        $data = $exists['values'][0];
    }else{
        $_SESSION['error'] = 'No record found.';
        header('location:alumni-add-company');
        exit(0);
    }
}else{
    $_SESSION['error'] = 'Invalid url.';
    header('location:alumni-add-company.php');
    exit(0);
}
if(isset($_POST['btnskill'])){
    $checkExists = $dbobj->check_exist($dbh, TBL_PRIFIX.'company_master',array('company_id'), 'company_name = ?', array(trim($_POST['txtcompany'])),$id,true);
    if($checkExists){
        $_SESSION['error'] = 'Company already exist.';
        header('location:alumni-edit-compay.php?companyId='.$SafeCrypto->encrypt($id));
        exit(0);
    }else{
        if(!empty($_FILES['txtLogo']['name'])){
            $fileUpload = $dbobj->fileUpload(ALUM_ABS_PATH.ALUM_COMMON_UPLOADS.ALUM_COMPANY, $_FILES['txtLogo']);
        }else{
             $fileUpload = $data['logo'];
        }
        $update = $dbobj->UpdatetData($dbh, TBL_PRIFIX.'company_master', 'company_name = ?, headquarter = ?,logo = ?,website_url = ?,modified_date = ?, modified_by = ?, modified_ip = ?', 'company_id = ?', 
                        array(trim($_POST['txtcompany']),
                            trim($_POST['txtHeadquarter']),
                            $fileUpload,
                            trim($_POST['txtWebsite']),
                            CURRENT_DT,
                            $loginSessionId,
                            $_SERVER['REMOTE_ADDR'],$id));
        if($update){
            $_SESSION['success'] = 'Company has been updated successfully.';
            header('location:alumni-edit-compay.php?companyId='.$SafeCrypto->encrypt($id));
            exit(0);
        }else{
            $_SESSION['warning'] = 'No changes Done.';
            header('location:alumni-edit-compay.php?companyId='.$SafeCrypto->encrypt($id));
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
                        <form id="form_validation" method="POST" enctype="multipart/form-data">
                            <div class="header">
                                <h2>Edit Company</h2>
                                <ul class="header-dropdown m-r--5">
                                    <li class="dropdown">
                                        <button class="btn btn-warning waves-effect" onclick="location.href='alumni-add-company'" type="button">Back</button>
                                        <button class="btn btn-primary waves-effect" name="btnskill" type="submit">Update</button>
                                    </li>
                                </ul>
                            </div>
                            <div class="body">
                                <!-- Inline Layout -->
                                <div class="form-group form-float">
                                    <div class="form-line">
                                        <input type="text" value="<?php echo (!empty($data['company_name']) ? $data['company_name'] : '');?>" class="form-control" name="txtcompany" required maxlength="60">
                                        <label class="form-label">Company Name</label>
                                    </div>
                                </div>
                                <div class="form-group form-float">
                                    <div class="form-line">
                                        <input type="text" value="<?php echo (!empty($data['headquarter']) ? $data['headquarter'] : '');?>" class="form-control" name="txtHeadquarter" required maxlength="60">
                                        <label class="form-label">Headquarter</label>
                                    </div>
                                </div>
                                <div class="form-group form-float">
                                        <label class="form-label">Upload Logo</label>
                                    <div class="form-line">
                                        <input type="file" value="<?php echo (!empty($data['logo']) ? $data['logo'] : '');?>" class="form-control" name="txtLogo">
                                    </div>
                                    <?php 
                                        if(!empty($data['logo'])){
                                            echo '<a href="'.(!empty($data['logo']) ? ALUM_WS_UPLOADS.ALUM_COMPANY.$data['logo']: ALUM_WS_IMAGES.'Alumni-Logo.png').'" target="_blank"><span>View Attachment</span></a>'; 
                                        }
                                    ?>
                                </div>
                                <div class="form-group form-float">
                                    <div class="form-line">
                                        <input type="url"  value="<?php echo (!empty($data['website_url']) ? $data['website_url'] : '');?>" class="form-control" name="txtWebsite" required>
                                        <label class="form-label">Website Url</label>
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
