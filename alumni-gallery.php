<?php
include 'includes/admin-config.php';
include_once 'auth.php';
include 'includes/emp-crypto-graphy.php';
$dbobj = new GetCommomOperation();
$SafeCrypto = new SafeCrypto();
    if(isset($_GET['albumId'])){
        $albumId = $SafeCrypto->decrypt($_GET['albumId']);
    }else{
       $_SESSION['error'] = 'No Record Found.';
        header('location:alumni-manage-memories.php');
        exit(0); 
    }
    if(isset($_POST['btnCompany'])){
        $checkExists = $dbobj->check_exist($dbh, TBL_PRIFIX.'gallery_details',array('album_id'), 'gallery_name = ?', array(trim($_POST['txtAlbumName'])));
        if($checkExists){
            $_SESSION['error'] = 'Title already exist.';
            header('location:alumni-gallery?albumId='.$_GET['albumId']);
            exit(0);
        }else{
            if(!empty($_FILES['txtImage']['name'])){
                $fileUpload = $dbobj->fileUpload(ALUM_ABS_PATH.ALUM_COMMON_UPLOADS.ALUM_ALBUM, $_FILES['txtImage']);
            }else{
                $fileUpload = NULL;
            }
            $loginSessionId = $dbobj->getSessionId();
            $value = array($albumId,trim($_POST['txtAlbumName']),$fileUpload,CURRENT_DT,$loginSessionId,$_SERVER['REMOTE_ADDR']);
            $gallery = $dbobj->InsertQuery($dbh, TBL_PRIFIX.'gallery_details', array('album_id','gallery_name','gallery_file','added_date','added_by','added_ip'), $value);
            if($gallery['status']){
                $_SESSION['success'] = 'Image has been added successfully.';
                header('location:alumni-gallery?albumId='.$_GET['albumId']);
                exit(0);
            }else{
                $_SESSION['error'] = 'Image could not added.';
                header('location:alumni-gallery?albumId='.$_GET['albumId']);
                exit(0);
            }
        }
    }
?>
<!DOCTYPE html>
<html>
<head>
    <?php include (ALUM_TEMPLATES.'metatag.php');?>
    <!-- Light Gallery Plugin Css -->
    <!-- <link href="plugins/light-gallery/css/lightgallery.css" rel="stylesheet"> -->
    <style>
        .carousel-control:hover, .carousel-control:focus {
        color: #fff;
        text-decoration: none;
        filter: alpha(opacity=90);
        outline: 0;
        opacity: .5;
        background:#1e5b79;
        }
    </style>
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
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="card">
                <?php
                    $userData = $dbobj->getUserData($dbh,$loginSessionId);
                    $sql = "SELECT T1.gallery_id,T2.album_id,T2.album_name,T1.gallery_name,T1.gallery_file,T2.added_by FROM ".TBL_PRIFIX."gallery_details as T1
                            LEFT JOIN ".TBL_PRIFIX."album_details as T2 ON T1.album_id = T2.album_id WHERE T1.album_id = ?
                            GROUP BY T1.gallery_id ASC";
                    $stmt = $dbh->prepare($sql);
                    $stmt->execute(array($albumId));
                    $rowCount = $stmt->rowCount();
                    $dataSet = '';
                    if ($rowCount > 0) {
                        for ($i = 1; $i <= $rowCount; $i++) {
                            $result['values'][] = $stmt->fetch(PDO::FETCH_ASSOC);
                        }
                        echo '<div class="header">
                                <h2>
                                    '.$result['values'][0]['album_name'].'
                                </h2>
                                 <ul class="header-dropdown m-r--5">
                                    <li class="dropdown">
                                        <button type="button" data-color="blue" class="btn bg-blue waves-effect addImage" data-toggle="modal" data-target="#defaultModal">Add Image</button>
                                    </li>
                                </ul>
                            </div>
                            <div class="body">
                                <div id="aniimated-thumbnials" class="list-unstyled row clearfix">';
                        foreach($result['values'] as $row){
                           echo '<div class="col-lg-3 col-md-4 col-sm-6 col-xs-12"  style="position:relative;">
                                    <i class="material-icons deleteImage" id="delete_'.$row['gallery_id'].'"style="position:absolute; top:5px;left:20px;z-index: 1; color:#e21313; cursor: pointer;" title="Delete">delete</i>
                                        <img class="img-responsive thumbnail" src="'.ALUM_WS_UPLOADS.ALUM_ALBUM.$row['gallery_file'].'">
                                </div> ';
                        }
                    }
                ?>    
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="modal fade" id="defaultModal" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="defaultModalLabel">Add Image</h4>
                        </div>
                        <div class="modal-body">
                            <form id="form_validation" method="POST" enctype="multipart/form-data">
                            <div class="body">
                                <!-- Inline Layout -->
                                <div class="form-group form-float">
                                    <div class="form-line">
                                        <input type="text" class="form-control" name="txtAlbumName" required maxlength="60">
                                        <label class="form-label">Image Title</label>
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
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">CLOSE</button>
                                <button type="submit" name="btnCompany" class="btn btn-danger waves-effect">Add</button>
                            </div>
                        </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php include ALUM_TEMPLATES.'footer.php';?> 
    <!-- Light Gallery Plugin Js -->
    <!-- <script src="plugins/light-gallery/js/lightgallery-all.js"></script> -->
    <script type="text/javascript">
        $('body').on('click','.deleteImage',function(){
            var id = $(this).attr('id');
            var Splitvalue = id.split('_');
            if (confirm('Are you sure you want '+Splitvalue[0]+'?')) {
                $.ajax({
                    type:'POST',
                    url:'ajax/ajax-manage-memories.php',
                    data:'GalleryDelete='+Splitvalue[1],
                    success: function(response){
                        window.location.reload();                           
                    }

                });
            }
        }); 
    </script>
</body>

</html>
