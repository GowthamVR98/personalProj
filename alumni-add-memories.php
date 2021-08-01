<?php
include 'includes/admin-config.php';
include_once 'auth.php';
include 'includes/emp-crypto-graphy.php';
$dbobj = new GetCommomOperation();
$SafeCrypto = new SafeCrypto();
?>
<!DOCTYPE html>
<html>
<head>
    <?php include (ALUM_TEMPLATES.'metatag.php');?>
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
        if(isset($_POST['btnCompany'])){
            $checkExists = $dbobj->check_exist($dbh, TBL_PRIFIX.'album_details',array('alumni_id'), 'album_name = ?', array(trim($_POST['txtAlbumName'])));
            if($checkExists){
                $_SESSION['error'] = 'Memories already exist.';
                header('location:alumni-add-memories.php');
                exit(0);
            }else{
                if(!empty($_FILES['txtImage']['name'])){
                    $fileUpload = $dbobj->fileUpload(ALUM_ABS_PATH.ALUM_COMMON_UPLOADS.ALUM_ALBUM, $_FILES['txtImage']);
                }else{
                    $fileUpload = NULL;
                }
                if($getLoggedData['user_type'] == 'admin' || $getLoggedData['user_type'] == 'superadmin'){
                    $approveStatus = 'approved';
                }else{
                    $approveStatus = 'pending';
                }
                $loginSessionId = $dbobj->getSessionId();
                $values = array(trim($_POST['txtAlbumName']),$approveStatus,CURRENT_DT,$loginSessionId,$_SERVER['REMOTE_ADDR']);
                $result = $dbobj->InsertQuery($dbh, TBL_PRIFIX.'album_details', array('album_name','approval_status','added_date','added_by','added_ip'), $values);
                if($result['status']){
                    $value = array($result['lastInsertId'],trim($_POST['txtAlbumName']),$fileUpload,CURRENT_DT,$loginSessionId,$_SERVER['REMOTE_ADDR']);
                    $gallery = $dbobj->InsertQuery($dbh, TBL_PRIFIX.'gallery_details', array('album_id','gallery_name','gallery_file','added_date','added_by','added_ip'), $value);
                    if($gallery['status']){
                        $_SESSION['success'] = 'New memories has been added successfully.';
                        header('location:alumni-add-memories.php');
                        exit(0);
                    }else{
                        $_SESSION['error'] = 'Memories could not added.';
                        header('location:alumni-add-memories.php');
                        exit(0);
                    }
                }else{
                    $_SESSION['error'] = 'Something went to wrong.';
                    header('location:alumni-add-memories.php');
                    exit(0);
                }
            }
        }
    ?>
    <section class="content">
        <div class="container-fluid">
            <!-- Calender -->
            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <form id="form_validation" method="POST" enctype="multipart/form-data">
                            <div class="header">
                                <h2>Add Memories</h2>
                                <ul class="header-dropdown m-r--5">
                                    <li class="dropdown">
                                        <button class="btn btn-primary waves-effect" name="btnCompany" type="submit">SUBMIT</button>
                                    </li>
                                </ul>
                            </div>
                            <div class="body">
                                <!-- Inline Layout -->
                                <div class="form-group form-float">
                                    <div class="form-line">
                                        <input type="text" class="form-control" name="txtAlbumName" required maxlength="60">
                                        <label class="form-label">Album Name</label>
                                    </div>
                                </div>
                                <div class="form-group form-float">
                                        <label class="form-label">Upload Image</label>
                                    <div class="form-line">
                                        <input type="file" class="form-control" name="txtImage" required>
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
    </section>
    <?php include ALUM_TEMPLATES.'footer.php';?> 
    <script>
        $('.ManageAction').on('click',function(){
            var id = $(this).attr('id');
            var Splitvalue = id.split('_');
            if (confirm('Are you sure you want '+Splitvalue[0]+'?')) {
                $.ajax({
                    type:'POST',
                    url:'ajax/ajax-manage-company.php',
                    data:Splitvalue[0]+'='+Splitvalue[1],
                    success: function(response){
                        window.location.reload();                           
                    }

                });
            } else {
                // Do nothing!
            }
        });
    </script>
</body>

</html>
