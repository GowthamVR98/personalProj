<?php
include 'includes/admin-config.php';
include_once 'auth.php';
include 'includes/emp-crypto-graphy.php';
$dbobj = new GetCommomOperation();
$SafeCrypto = new SafeCrypto();
$loginSessionId = $dbobj->getSessionId();
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
        if(isset($_POST['btnCompany'])){
             $checkExists = $dbobj->check_exist($dbh, TBL_PRIFIX.'video_details',array('video_id'), 'title = ?', array(trim($_POST['txtTitle'])));
            if($checkExists){
                $_SESSION['error'] = 'Video title already exist.';
                header('location:alumni-add-videos.php');
                exit(0);
            }else{
                if($getLoggedData['user_type'] == 'admin' || $getLoggedData['user_type'] == 'superadmin'){
                    $approveStatus = 'approved';
                }else{
                    $approveStatus = 'pending';
                }
                $values = array(trim($_POST['txtTitle']),trim($_POST['txtLink']),trim($_POST['txtDescription']),$approveStatus,CURRENT_DT,$loginSessionId,$_SERVER['REMOTE_ADDR']);
                $result = $dbobj->InsertQuery($dbh, TBL_PRIFIX.'video_details', array('title','youtub_link','description','approval_status','added_date','added_by','added_ip'), $values);
                if($result['status']){
                    $_SESSION['success'] = 'Video has been added successfully.';
                    header('location:alumni-add-videos.php');
                    exit(0);
                }else{
                    $_SESSION['error'] = 'Something went to wrong.';
                    header('location:alumni-add-videos.php');
                    exit(0);
                }
            }
        }
    ?>
    <section class="content">
        <div class="container-fluid">
            <div class="block-header">
                <h2>
                    <button class="btn btn-primary waves-effect pull-right" id="ShowHide" type="submit">Add Video</button>
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
                                <h2>Add Videos</h2>
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
                                    <div class="form-line">
                                        <input type="url" class="form-control" name="txtLink" required>
                                        <label class="form-label">YouTube Link</label>
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
                                Manage Video Details
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
                                            <th>Added By</th>
                                            <th>Approval Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $sql = "SELECT concat_ws(' ',T1.fname,T1.lname) as Name,T2.approval_status,T2.added_by,T2.title,T2.youtub_link ,T2.video_id,T2.status  FROM ".TBL_PRIFIX."video_details as T2
                                            LEFT JOIN ".TBL_PRIFIX."user_login_details as T1 ON T1.user_id = T2.added_by WHERE T2.added_by = ?
                                            GROUP BY T2.video_id ASC";
                                        $stmt = $dbh->prepare($sql);
                                        $stmt->execute(array($loginSessionId));
                                        $rowCount = $stmt->rowCount();
                                        if ($rowCount > 0) {
                                                for ($i = 1; $i <= $rowCount; $i++) {
                                                    $result['values'][] = $stmt->fetch(PDO::FETCH_ASSOC);
                                                }
                                                foreach($result['values'] as $row){
                                                    if($row['approval_status'] == 'pending'){
                                                        $approvalStatus = '<span class="badge bg-orange" >Pending</span>';
                                                    }else if($row['approval_status'] == 'approved'){
                                                        $approvalStatus = '<span class="badge bg-teal" >Approved</span>';
                                                    }else if($row['approval_status'] == 'rejected'){
                                                        $approvalStatus = '<a href="javascript:void(0);" class="RejectedReason" id="ReasonId_'.$row['video_id'].'" data-toggle="modal" data-target="#RemarkModal" ><span class="badge bg-pink" >Rejected</span></a>';
                                                    }
                                                    echo '<tr>';
                                                    echo '<td>'.$row['title'].'</td>';
                                                    echo '<td>'.$row['Name'].'</td>';
                                                    echo '<td>'.$approvalStatus.'</td>';
                                                    echo '<td style="text-align:center;">
                                                    '.($row['status'] == 1 ? '<a href="javascript:void(0);" class="ManageAction" id="disable_'.$row['video_id'].'"><i class="material-icons" title = "Disable" style="font-size:21px;">block</i></a>' : '<a href="javascript:void(0);" class="ManageAction" id="enable_'.$row['video_id'].'"><i class="material-icons" style="font-size:21px;" title="Enable">check_circle</i></a>').'
                                                    <a href="alumni-edit-video.php?VideoId='.$SafeCrypto->encrypt($row['video_id']).'"><i class="material-icons" title="Edit" id="'.$row['video_id'].'" style=" font-size:21px;">edit</i></a>
                                                    <a href="javascript:void(0);" class="ManageAction" id="delete_'.$row['video_id'].'"><i class="material-icons" title="Delete" style="color:#e21313; font-size:21px;">delete</i></a>
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
            <div class="modal fade" id="RemarkModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="defaultModalLabel">Reject Reason</h4>
                    </div>
                    <div class="modal-body">
                        
                    </div>
                </div>
            </div>
        </div>
            <!-- #END# Basic Examples -->
        </div>
    </section>
    <?php include ALUM_TEMPLATES.'footer.php';?> 
    <script>
        $(document).ready(function(){
            $('.ManageAction').on('click',function(){
                var id = $(this).attr('id');
                var Splitvalue = id.split('_');
                if (confirm('Are you sure you want '+Splitvalue[0]+'?')) {
                    $.ajax({
                        type:'POST',
                        url:'ajax/ajax-manage-video.php',
                        data:Splitvalue[0]+'='+Splitvalue[1],
                        success: function(response){
                            window.location.reload();                           
                        }

                    });
                } else {
                    // Do nothing!
                }
            });
            $('body').on('click','.RejectedReason',function(){
                var id = $(this).attr('id');
                var Splitvalue = id.split('_');
                $.ajax({
                    type:'POST',
                    url:'ajax/ajax-manage-video.php',
                    data:Splitvalue[0]+'='+Splitvalue[1],
                    success: function(response){
                        $('#RemarkModal .modal-body').html(response);                           
                    }

                });
            });
        });
    </script>
</body>

</html>
