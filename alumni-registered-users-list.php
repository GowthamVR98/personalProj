<?php
include 'includes/admin-config.php';
include_once 'auth.php';
$dbobj = new GetCommomOperation();
$loginSessionId = $dbobj->getSessionId();
    if(isset($_POST['btnReject'])){
        $update = $dbobj->UpdatetData($dbh, TBL_PRIFIX.'user_login_details', 'reject_reason = ?, approval_status = ?, date_modified = ?,modified_by = ?,modified_ip = ?', 'user_id = ?', array(trim($_POST['txtRejectReason']),'rejected',CURRENT_DT,$loginSessionId,$_SERVER['REMOTE_ADDR'],$_POST['hiddenId']));
        if($update){
            $_SESSION['success'] = 'User has been rejected successfully.';
           
        }else{
            $_SESSION['warning'] = 'Somthing went to wrong, Please try again later.';
        }
         header('location:alumni-registered-users-list');
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
                                Approve/Reject Users
                            </h2>
                        </div>
                        <div class="body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover js-basic-example dataTable">
                                    <thead>
                                        <tr>
                                            <th>Reg No.</th>
                                            <th>Name</th>
                                            <th>Email Id</th>
                                            <th>Mobile Number</th>
                                            <th>Gender</th>
                                            <th>User Type</th>
                                            <th>Graduated Year</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $sql = "SELECT concat_ws(' ',fname,lname) as Name,registration_id,email_id,approval_status,mobile_number,gender,user_type,year_of_graduation,user_id FROM ".TBL_PRIFIX."user_login_details  WHERE approval_status = ? AND super_admin = ? GROUP BY date_added ASC";
                                        $stmt = $dbh->prepare($sql);
                                        $stmt->execute(array('pending','no'));
                                        $rowCount = $stmt->rowCount();
                                        if ($rowCount > 0) {
                                                for ($i = 1; $i <= $rowCount; $i++) {
                                                    $result['values'][] = $stmt->fetch(PDO::FETCH_ASSOC);
                                                }
                                                foreach($result['values'] as $row){
                                                    echo '<tr>';
                                                    echo '<td>'.$row['registration_id'].'</td>';
                                                    echo '<td>'.$row['Name'].'</td>';
                                                    echo '<td>'.$row['email_id'].'</td>';
                                                    echo '<td>'.$row['mobile_number'].'</td>';
                                                    echo '<td>'.$row['gender'].'</td>';
                                                    echo '<td>'.$row['user_type'].'</td>';
                                                    echo '<td>'.$row['year_of_graduation'].'</td>';
                                                    echo '<td style="text-align:center;">
                                                     <a href="javascript:void(0);" class="ManageAction" id="approve_'.$row['user_id'].'"><i class="material-icons" title = "Approve" style="font-size:21px;color:green;font-weight:900;">check</i></a>
                                                    <a href="javascript:void(0);" class="RejectedReason" id="reject_'.$row['user_id'].'"><i class="material-icons" data-toggle="modal" data-target="#RemarkModal" style="font-size:21px;color:red;font-weight:900;" title="Reject">close</i></a>
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
           $('body').on('click','.ManageAction',function(){
                var id = $(this).attr('id');
                var Splitvalue = id.split('_');
                if (confirm('Are you sure you want '+Splitvalue[0]+'?')) {
                    $.ajax({
                        type:'POST',
                        url:'ajax/ajax-manage-registered-user.php',
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
                    url:'ajax/ajax-manage-registered-user.php',
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
