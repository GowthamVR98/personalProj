<?php
include 'includes/admin-config.php';
include_once 'auth.php';
include 'includes/emp-crypto-graphy.php';
$dbobj = new GetCommomOperation();
$SafeCrypto = new SafeCrypto();
    if(isset($_POST['btnReunion'])){
        $loginSessionId = $dbobj->getSessionId();
         if(!empty($_FILES['txtImage']['name'])){
                    $fileUpload = $dbobj->fileUpload(ALUM_ABS_PATH.ALUM_COMMON_UPLOADS.ALUM_RUNION, $_FILES['txtImage']);
                }else{
                    $fileUpload = NULL;
                }
        $values = array(trim($_POST['txtTitle']),trim($_POST['txtLocation']),
                        trim(SystemDate::date($_POST['txtStartDate'])),trim(SystemDate::time($_POST['txtStartTime'])),
                        trim(SystemDate::date($_POST['txtEndDate'])),trim(SystemDate::time($_POST['txtEndTime'])),$fileUpload,
                        trim($_POST['txtDescription']),CURRENT_DT,$loginSessionId,$_SERVER['REMOTE_ADDR']);
        $result = $dbobj->InsertQuery($dbh, TBL_PRIFIX.'reunion_details', 
            array('title', 'location', 'start_date', 'start_time', 'end_date', 'end_time',
                 'reunion_logo','description', 'added_date', 'added_by','added_ip'), $values,true);
        if($result['status']){
            // include_once ALUM_ETEMPLATES.'job-details-tpl.php';
            $_SESSION['success'] = 'Reunion has been added successfully.';
            header('location:alumni-add-reunion');
            exit(0);
        }else{
            $_SESSION['error'] = 'Something went to wrong.';
            header('location:alumni-add-reunion');
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
            <div class="block-header">
                <h2>
                    <button class="btn btn-primary waves-effect pull-right" id="ShowHide" type="submit">Add Reunion</button>
                    <button class="btn btn-primary waves-effect pull-right" id="hideOptionBlock" type="button" style="display:none;">Hide</button>
                </h2>
                <div class="clearfix"></div>
            </div>
            <!-- Calender -->
            <div class="row clearfix"  id="hideShow">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <form id="form_validation" method="POST" enctype="multipart/form-data">
                            <div class="header">
                                <h2>Add Reunion</h2>
                                <ul class="header-dropdown m-r--5">
                                    <li class="dropdown">
                                        <button class="btn btn-primary waves-effect" name="btnReunion" type="submit">SUBMIT</button>
                                    </li>
                                </ul>
                            </div>
                            <div class="body">
                                <!-- Inline Layout -->
                                <div class="form-group form-float">
                                    <div class="form-line">
                                        <input type="text" class="form-control"  title="Title" name="txtTitle" required>
                                        <label class="form-label">Title</label>
                                    </div>
                                </div>
                                <div class="form-group form-float">
                                    <div class="form-line">
                                        <input type="text" class="form-control"  title="Location" name="txtLocation" required>
                                        <label class="form-label">Location</label>
                                    </div>
                                </div>
                                <div class="form-group form-float">
                                    <div class="form-line" id="bs_datepicker_container">
                                        <input type="text" class="form-control" readonly  title="Start Date" name="txtStartDate" required>
                                        <label class="form-label">Start Date</label>
                                    </div>
                                </div>
                                <div class="form-group form-float">
                                    <div class="form-line">
                                        <input type="text" class="form-control timepicker" readonly  title="Start Time" name="txtStartTime" required>
                                        <label class="form-label">Start Time</label>
                                    </div>
                                </div>
                                <div class="form-group form-float">
                                    <div class="form-line" id="bs_datepicker_container">
                                        <input type="text" class="form-control" readonly  title="End Date" name="txtEndDate" required>
                                        <label class="form-label">End Date</label>
                                    </div>
                                </div>
                                <div class="form-group form-float">
                                    <div class="form-line">
                                        <input type="text" class="form-control timepicker" readonly  title="End Time" name="txtEndTime" required>
                                        <label class="form-label">End Time</label>
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
                                        <textarea class="form-control"  title="Description" name="txtDescription" required></textarea>
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
             <!-- Basic Examples -->
             <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="header">
                            <h2>
                                Manage Reunion Details
                            </h2>
                        </div>
                        <div class="body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover js-basic-example dataTable">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Location</th>
                                            <th>Start</th>
                                            <th>End</th>
                                            <th>Approval Status</th>
                                            <th>Added By</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $sql = "SELECT concat_ws(' ',T1.fname,T1.lname) as Name, T2.start_date,T2.end_date,T2.start_time,T2.end_time,T2.title,T2.location,T2.status,T2.approval_status,T2.reunion_id,T2.added_date,T2.added_by FROM ".TBL_PRIFIX."reunion_details as T2 LEFT JOIN ".TBL_PRIFIX."user_login_details as T1 ON T1.user_id = T2.added_by GROUP BY T2.reunion_id ASC";
                                        $stmt = $dbh->prepare($sql);
                                        $stmt->execute();
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
                                                        $approvalStatus = '<a href="javascript:void(0);" class="RejectedReason" id="ReasonId_'.$row['reunion_id'].'" data-toggle="modal" data-target="#RemarkModal" ><span class="badge bg-pink" >Rejected</span></a>';
                                                    }
                                                    echo '<tr>';
                                                    echo '<td>'.$row['title'].'</td>';
                                                    echo '<td>'.$row['location'].'</td>';
                                                    echo '<td>'.AppDate::datetime($row['start_date'].' '.$row['start_time']).'</td>';
                                                    echo '<td>'.AppDate::datetime($row['end_date'].' '.$row['end_time']).'</td>';
                                                    echo '<td>'.$approvalStatus.'</td>';
                                                    echo '<td>'.$row['Name'].'</td>';
                                                    echo '<td style="text-align:center;">
                                                    '.($row['status'] == 1 ? '<a href="javascript:void(0);" class="ManageAction" id="disable_'.$row['reunion_id'].'"><i class="material-icons" title = "Disable" style="font-size:21px;">block</i></a>' : '<a href="javascript:void(0);" class="ManageAction" id="enable_'.$row['reunion_id'].'"><i class="material-icons" style="font-size:21px;" title="Enable">check_circle</i></a>').'
                                                    <a href="alumni-edit-reunion.php?ReunionId='.$SafeCrypto->encrypt($row['reunion_id']).'"><i class="material-icons" title="Edit" id="'.$row['reunion_id'].'" style=" font-size:21px;">edit</i></a>
                                                    <a href="javascript:void(0);" class="ManageAction" id="delete_'.$row['reunion_id'].'"><i class="material-icons" title="Delete" style="color:#e21313; font-size:21px;">delete</i></a>
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
    </section>
    <?php include ALUM_TEMPLATES.'footer.php';?> 
   <!-- Bootstrap Material Datetime Picker Plugin Js -->
    <script src="plugins/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js"></script>
    <script>
        $(document).ready(function(){
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
            $('.ManageAction').on('click',function(){
                var id = $(this).attr('id');
                var Splitvalue = id.split('_');
                if (confirm('Are you sure you want '+Splitvalue[0]+'?')) {
                    $.ajax({
                        type:'POST',
                        url:'ajax/ajax-manage-reunion.php',
                        data:Splitvalue[0]+'='+Splitvalue[1],
                        success: function(response){
                            window.location.reload();                           
                        }

                    });
                } 
            });
            $('body').on('click','.RejectedReason',function(){
                var id = $(this).attr('id');
                var Splitvalue = id.split('_');
                $.ajax({
                    type:'POST',
                    url:'ajax/ajax-manage-reunion.php',
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
