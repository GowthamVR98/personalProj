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
?>
<!DOCTYPE html>
<html>
<head>
    <?php include (ALUM_TEMPLATES.'metatag.php');?>
    <!-- Light Gallery Plugin Css -->
    <link href="plugins/light-gallery/css/lightgallery.css" rel="stylesheet">
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
                    $sql = "SELECT T2.album_id,T2.album_name,T1.gallery_name,T1.gallery_file,T2.added_by FROM ".TBL_PRIFIX."gallery_details as T1
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
                            </div>
                            <div class="body">
                                <div id="aniimated-thumbnials" class="list-unstyled row clearfix">';
                        foreach($result['values'] as $row){
                           echo '<div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
                                    <a href="'.ALUM_WS_UPLOADS.ALUM_ALBUM.$row['gallery_file'].'" data-sub-html="'.$row['gallery_name'].'">
                                        <img class="img-responsive thumbnail" src="'.ALUM_WS_UPLOADS.ALUM_ALBUM.$row['gallery_file'].'">
                                    </a>
                                </div> ';
                        }
                    }
                ?>    
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </section>
    <?php include ALUM_TEMPLATES.'footer.php';?> 
    <!-- Light Gallery Plugin Js -->
    <script src="plugins/light-gallery/js/lightgallery-all.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            $('#aniimated-thumbnials').lightGallery({
                thumbnail: true,
                selector: 'a'
            }); 
        });
    </script>
</body>

</html>
