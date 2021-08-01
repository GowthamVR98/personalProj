<?php
include 'includes/admin-config.php';
include_once 'auth.php';
$dbobj = new GetCommomOperation();
$loginSessionId = $dbobj->getSessionId();
    if(isset($_POST['btnReject'])){
        $update = $dbobj->UpdatetData($dbh, TBL_PRIFIX.'reunion_details', 'reject_reason = ?, approval_status = ?, modified_date = ?,modified_by = ?,modified_ip = ?', 'reunion_id = ?', array(trim($_POST['txtRejectReason']),'rejected',CURRENT_DT,$loginSessionId,$_SERVER['REMOTE_ADDR'],$_POST['hiddenId']));
        if($update){
            $_SESSION['success'] = 'Reunion has been rejected successfully.';
           
        }else{
            $_SESSION['warning'] = 'Somthing went to wrong, Please try again later.';
        }
         header('location:alumni-approve-reunion');
        exit(0);
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
             <!-- Basic Examples -->
             <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="header">
                            <h2>
                                Approve/Reject Reunion
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
                                        $sql = "SELECT concat_ws(' ',T1.fname,T1.lname) as Name, T2.start_date,T2.end_date,T2.start_time,T2.end_time,T2.title,T2.location,T2.status,T2.approval_status,T2.reunion_id,T2.added_date,T2.added_by FROM ".TBL_PRIFIX."reunion_details as T2 LEFT JOIN ".TBL_PRIFIX."user_login_details as T1 ON T1.user_id = T2.added_by WHERE T2.approval_status = ? GROUP BY T2.reunion_id ASC";
                                        $stmt = $dbh->prepare($sql);
                                        $stmt->execute(array('pending'));
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
                                                     <a href="javascript:void(0);" class="ManageAction" id="approve_'.$row['reunion_id'].'"><i class="material-icons" title = "Approve" style="font-size:21px;color:green;font-weight:900;">check</i></a>
                                                    <a href="javascript:void(0);" class="RejectedReason" id="reject_'.$row['reunion_id'].'"><i class="material-icons" data-toggle="modal" data-target="#RemarkModal" style="font-size:21px;color:red;font-weight:900;" title="Reject">close</i></a>
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
           $('body').on('click','.ManageAction',function(){
                var id = $(this).attr('id');
                var Splitvalue = id.split('_');
                if (confirm('Are you sure you want '+Splitvalue[0]+'?')) {
                    $.ajax({
                        type:'POST',
                        url:'ajax/ajax-manage-reunion.php',
                        data:'approveProcess=true&'+Splitvalue[0]+'='+Splitvalue[1],
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
