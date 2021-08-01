<?php
    include('../includes/admin-config.php');
    $dbobj = new GetCommomOperation();
    $loginSessionId = $dbobj->getSessionId();
    if(isset($_POST['approveProcess'])){
        if(isset($_POST['approve'])){
            $id = $_POST['approve'];
            $approveStatus = 'approved';
            $msg = 'User has been approved successfully.';
        }else{
            $msg = 'User has been rejected successfully.';
            $id = $_POST['reject'];
            $approveStatus = 'rejected';
        }
        $values = array($approveStatus,CURRENT_DT,$loginSessionId,$_SERVER['REMOTE_ADDR'],trim($id));
        $result = $dbobj->UpdatetData($dbh, TBL_PRIFIX.'user_login_details','approval_status = ?,date_modified = ?,modified_by = ?,modified_ip = ?','user_id = ?', $values,true);
        if($result){
            $_SESSION['success'] =  $msg;
            exit(0);
        }else{
            $_SESSION['error'] = 'Something went to wrong.';
            exit(0);
        }
    }else if(isset($_POST['reject'])){
        $getData = $dbobj->selectData($dbh, TBL_PRIFIX.'user_login_details', array('user_id'), 'user_id = ?', array($_POST['reject']));
        if($getData['total'] > 0){
            $data = '';
            $data .='<form id="form_validation" method="POST" enctype="multipart/form-data">
                        <div class="body">
                            <!-- Inline Layout -->
                            <div class="form-group form-float">
                                <div class="form-line">
                                    <textarea class="form-control" placeholder="Reject Reason" name="txtRejectReason"></textarea>
                                    <input type="hidden" name="hiddenId" value="'.$getData['values'][0]['user_id'].'">
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <!-- #END# Inline Layout -->
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">CLOSE</button>
                            <button type="submit" name="btnReject" class="btn btn-danger waves-effect" title="Reject">Reject</button>
                        </div>
                    </form>';
        }
        echo $data;
    }
?>