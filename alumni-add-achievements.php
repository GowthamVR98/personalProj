<?php
include_once 'auth.php';
include 'includes/admin-config.php';
include 'includes/emp-crypto-graphy.php';
include ('cron-job/alumni-cron-job-class.php');  
$dbobj = new GetCommomOperation();
$SafeCrypto = new SafeCrypto();
    if(isset($_POST['btnCompany'])){
        if(!empty($_FILES['txtImage']['name'])){
            $fileUpload = $dbobj->fileUpload(ALUM_ABS_PATH.ALUM_COMMON_UPLOADS.ALUM_ACHIEVEMENT, $_FILES['txtImage']);
        }else{
            $fileUpload = NULL;
        }
        $loginSessionId = $dbobj->getSessionId();
        $values = array(trim($_POST['txtTitle']),$fileUpload,trim($_POST['txtDescription']),CURRENT_DT,$loginSessionId,$_SERVER['REMOTE_ADDR']);
        $result = $dbobj->InsertQuery($dbh, TBL_PRIFIX.'achievement_details', array('title','attachement_file','description','added_date','added_by','added_ip'), $values);
        if($result['status']){
            $cronJob = new mailCron($dbh,$loginSessionId,$dbobj);
            $result = $cronJob->getUserData($dbh, 0,'user_id != ?');
            foreach ($result['values'] as $key => $value) {
                $toMailIdWithName[] = $value['mailData'];
                $content[] = 'New achievement has been added.';
            }
            $cronJob->insertCronJobData('Achievement', $toMailIdWithName,$content);
            $_SESSION['success'] = 'Achievement has been added successfully.';
       }else{
            $_SESSION['error'] = 'Something went to wrong.';
        }
        header('location:alumni-add-achievements.php');
        exit(0);
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
            <div class="block-header">
                <h2>
                    <button class="btn btn-primary waves-effect pull-right" id="ShowHide" type="submit">Add Achievement</button>
                    <button class="btn btn-primary waves-effect pull-right" id="hideOptionBlock" type="button" style="display:none;">Hide</button>
                </h2>
                <div class="clearfix"></div>
            </div>
            <!-- Calender -->
            <div class="row clearfix" id="hideShow">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <form id="form_validation" method="POST" enctype="multipart/form-data">
                            <div class="header">
                                <h2>Add Achievement</h2>
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
                                        <input type="text" class="form-control" name="txtTitle" required maxlength="60">
                                        <label class="form-label">Title</label>
                                    </div>
                                </div>
                                <div class="form-group form-float">
                                        <label class="form-label">Upload Image</label>
                                    <div class="form-line">
                                        <input type="file" class="form-control" name="txtImage" required>
                                    </div>
                                </div>
                                <div class="form-group form-float">
                                    <div class="form-line">
                                        <textarea class="form-control" name="txtDescription" required maxlength="360"></textarea>
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
            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="header">
                            <h2>
                                Manage Achievements Details
                            </h2>
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
                                            <th>Title</th>
                                            <th>Attachement</th>
                                            <th>Added By</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $sql = "SELECT concat_ws(' ',T1.fname,T1.lname) as Name,T2.added_by,T2.title,T2.attachement_file ,T2.achievement_id,T2.status  FROM ".TBL_PRIFIX."achievement_details as T2
                                            LEFT JOIN ".TBL_PRIFIX."user_login_details as T1 ON T1.user_id = T2.added_by
                                            GROUP BY T2.achievement_id ASC";
                                        $stmt = $dbh->prepare($sql);
                                        $stmt->execute();
                                        $rowCount = $stmt->rowCount();
                                        if ($rowCount > 0) {
                                                for ($i = 1; $i <= $rowCount; $i++) {
                                                    $result['values'][] = $stmt->fetch(PDO::FETCH_ASSOC);
                                                }
                                                foreach($result['values'] as $row){
                                                    echo '<tr>';
                                                    echo '<td>'.$row['title'].'</td>';
                                                    echo '<td>
                                                                <a href="'.(!empty($row['attachement_file']) ? ALUM_WS_UPLOADS.ALUM_ACHIEVEMENT.$row['attachement_file']: ALUM_WS_IMAGES.'Alumni-attachement_file.png').'" target="_blank">
                                                                    <img scr="'.(!empty($row['attachement_file']) ? ALUM_WS_UPLOADS.ALUM_ACHIEVEMENT.$row['attachement_file']: ALUM_WS_IMAGES.'Alumni-attachement_file.png').'" alt="View attachement_file" height="100px" width="100px">
                                                                </a>
                                                            </td>';
                                                    echo '<td>'.$row['Name'].'</td>';
                                                    echo '<td style="text-align:center;">
                                                    '.($row['status'] == 1 ? '<a href="javascript:void(0);" class="ManageAction" id="disable_'.$row['achievement_id'].'"><i class="material-icons" title = "Disable" style="font-size:21px;">block</i></a>' : '<a href="javascript:void(0);" class="ManageAction" id="enable_'.$row['achievement_id'].'"><i class="material-icons" style="font-size:21px;" title="Enable">check_circle</i></a>').'
                                                    <a href="alumni-edit-achievement.php?achievementId='.$SafeCrypto->encrypt($row['achievement_id']).'"><i class="material-icons" title="Edit" id="'.$row['achievement_id'].'" style=" font-size:21px;">edit</i></a>
                                                    <a href="javascript:void(0);" class="ManageAction" id="delete_'.$row['achievement_id'].'"><i class="material-icons" title="Delete" style="color:#e21313; font-size:21px;">delete</i></a>
                                                    </td>';
                                                    echo '</tr>';
                                                }
                                            }else{
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
    <?php include ALUM_TEMPLATES.'footer.php';?> 
    <script>
        $('.ManageAction').on('click',function(){
            var id = $(this).attr('id');
            var Splitvalue = id.split('_');
            if (confirm('Are you sure you want '+Splitvalue[0]+'?')) {
                $.ajax({
                    type:'POST',
                    url:'ajax/ajax-manage-achievement.php',
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
