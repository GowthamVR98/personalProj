<?php
include 'includes/admin-config.php';
include_once 'auth.php';
include 'includes/emp-crypto-graphy.php';
$SafeCrypto = new SafeCrypto();
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
                                Users List
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
                                        $stmt->execute(array('approved','no'));
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
                                                     <a href="alumni-view-user-profile.php?UserId='.$SafeCrypto->encrypt($row['user_id']).'"><i class="material-icons" title = "View" style="font-size:21px;color:#03A9F4;font-weight:900;">visibility</i></a>
                                                    <a href="alumni-user-edit.php?UserId='.$SafeCrypto->encrypt($row['user_id']).'" class="RejectedReason"><i class="material-icons" style="font-size:21px;font-weight:900;" title="Edit">edit</i></a>
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
</body>

</html>
